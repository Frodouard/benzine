<?php
session_start();
require_once __DIR__ . '/db.php';

function flash($message = null) {
    if ($message === null) {
        if (!empty($_SESSION['flash'])) {
            $msg = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $msg;
        }
        return null;
    }
    $_SESSION['flash'] = $message;
}

function isLoggedIn() {
    return !empty($_SESSION['user']);
}

function isAdmin() {
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
    enforceActive();
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../login.php');
        exit;
    }
    enforceActive();
}

function enforceActive() {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT is_active FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user']['id']]);
    $active = $stmt->fetchColumn();
    if ($active === null || !$active) {
        session_destroy();
        header('Location: ../login.php');
        exit;
    }
}

function isTrader() {
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'trader';
}

function requireTrader() {
    if (!isTrader()) {
        header('Location: ../login.php');
        exit;
    }
    enforceActive();
}

function getMyProducts($sellerId) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.seller_id = ? ORDER BY p.created_at DESC');
    $stmt->execute([$sellerId]);
    return $stmt->fetchAll();
}

function getAdminId() {
    $pdo = getDb();
    $id = $pdo->query("SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1")->fetchColumn();
    return $id ? intval($id) : 0;
}

function sendMessage($senderId, $receiverId, $message) {
    $pdo = getDb();
    $stmt = $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)');
    $stmt->execute([$senderId, $receiverId, trim($message)]);
}

function getConversation($userA, $userB) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC, id ASC');
    $stmt->execute([$userA, $userB, $userB, $userA]);
    return $stmt->fetchAll();
}

function getAllUsers() {
    $pdo = getDb();
    return $pdo->query(
        'SELECT u.*, (SELECT COUNT(*) FROM products p WHERE p.seller_id = u.id) AS product_count
         FROM users u ORDER BY u.role, u.name'
    )->fetchAll();
}

function getAllTraders() {
    $pdo = getDb();
    return $pdo->query(
        "SELECT u.*, (SELECT COUNT(*) FROM products p WHERE p.seller_id = u.id) AS product_count
         FROM users u WHERE u.role = 'trader' ORDER BY u.name"
    )->fetchAll();
}

function getProductsBySeller($sellerId) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.seller_id = ? ORDER BY p.created_at DESC');
    $stmt->execute([$sellerId]);
    return $stmt->fetchAll();
}

function getAllCategories() {
    $pdo = getDb();
    return $pdo->query(
        'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
         FROM categories c ORDER BY c.name'
    )->fetchAll();
}

function getCategories() {
    $pdo = getDb();
    return $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function formatPrice($value) {
    return number_format((float)$value, 2);
}

function getProducts($search = null, $categoryId = null) {
    $pdo = getDb();
    $sql = 'SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id';
    $where = [];
    $params = [];
    if ($search) {
        $where[] = '(p.name LIKE :search OR p.description LIKE :search)';
        $params['search'] = "%$search%";
    }
    if ($categoryId) {
        $where[] = 'p.category_id = :category_id';
        $params['category_id'] = (int)$categoryId;
    }
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY p.created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getRecentProducts($limit = 4) {
    $pdo = getDb();
    $stmt = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT ' . (int)$limit);
    return $stmt->fetchAll();
}

function getFeaturedProducts($limit = 3) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY RAND() LIMIT ?');
    $stmt->execute([(int)$limit]);
    return $stmt->fetchAll();
}

function getProduct($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function addToCart($productId, $quantity) {
    $product = getProduct($productId);
    if (!$product) {
        return false;
    }
    $quantity = max(1, intval($quantity));
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image']
        ];
    }
    return true;
}

function getCartItems() {
    return $_SESSION['cart'] ?? [];
}

function cartCount() {
    $count = 0;
    foreach (getCartItems() as $item) {
        $count += (int)$item['quantity'];
    }
    return $count;
}

function updateCartQuantity($productId, $quantity) {
    if (!isset($_SESSION['cart'][$productId])) {
        return false;
    }
    $quantity = max(1, intval($quantity));
    $_SESSION['cart'][$productId]['quantity'] = $quantity;
    return true;
}

function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        return true;
    }
    return false;
}

function getCartTotal() {
    $total = 0;
    foreach (getCartItems() as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return number_format($total, 2, '.', '');
}

function orderStatusBadge($status) {
    $map = [
        'Pending'           => 'badge-pending',
        'Confirmed'         => 'badge-info',
        'Out for delivery'  => 'badge-out-for-delivery',
        'Delivered'         => 'badge-success',
    ];
    $class = $map[$status] ?? 'badge-plain';
    $label = str_replace('_', ' ', $status);
    return '<span class="badge ' . $class . '">' . e($label) . '</span>';
}
