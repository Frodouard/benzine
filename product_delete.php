<?php
require_once __DIR__ . '/../inc/functions.php';
requireTrader();
$trader = $_SESSION['user'];
$id = intval($_GET['id'] ?? 0);
if ($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ? AND seller_id = ?');
    $stmt->execute([$id, $trader['id']]);
}
header('Location: index.php');
exit;
