<?php
require_once __DIR__ . '/inc/functions.php';
$page = 'cart';
$title = 'Cart - MICKY SHOP';
$cart = getCartItems();
$message = flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update' && isset($_POST['product_id'])) {
        updateCartQuantity((int)$_POST['product_id'], (int)($_POST['quantity'] ?? 1));
        header('Location: cart.php');
        exit;
    }

    if ($action === 'remove' && isset($_POST['product_id'])) {
        removeFromCart((int)$_POST['product_id']);
        header('Location: cart.php');
        exit;
    }

    if (isset($_POST['checkout'])) {
        if (!isLoggedIn()) {
            flash('Please login to complete checkout.');
            header('Location: login.php');
            exit;
        }

        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$address) {
            flash('Delivery address is required.');
            header('Location: cart.php');
            exit;
        }

        $pdo = getDb();
        $pdo->beginTransaction();
        try {
            $total = getCartTotal();
            $stmt = $pdo->prepare('INSERT INTO orders (customer_id, total_amount, delivery_address, contact_phone, contact_email) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$_SESSION['user']['id'], $total, $address, $phone, $email]);
            $orderId = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
            foreach ($cart as $item) {
                $itemStmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            }
            $pdo->commit();
            unset($_SESSION['cart']);
            flash('Order placed successfully.');
            header('Location: order_success.php?id=' . $orderId);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            flash('Unable to complete order. Please try again.');
        }
    }
}
require __DIR__ . '/inc/header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Your cart</h2>
            <a class="btn outline small" href="products.php">&larr; Continue shopping</a>
        </div>

        <?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>

        <?php if (!$cart): ?>
            <div class="empty-state">
                <div class="e-ico">🛒</div>
                <h3>Your cart is empty</h3>
                <p>Browse our products and add something you love.</p>
                <a class="btn" href="products.php">Browse products</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <div>
                    <?php foreach ($cart as $item): ?>
                    <div class="cart-item">
                        <img class="thumb" src="<?php echo e($item['image'] ?: 'assets/images/default.png'); ?>" alt="<?php echo e($item['name']); ?>">
                        <div>
                            <h4><a href="product.php?id=<?php echo (int)$item['id']; ?>"><?php echo e($item['name']); ?></a></h4>
                            <span class="sub">$<?php echo formatPrice($item['price']); ?> each</span>
                            <div class="qty-stepper mt-1">
                                <form method="post" action="cart.php" style="display:contents;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo (int)$item['id']; ?>">
                                    <button type="button" data-qty="down" aria-label="Decrease">&minus;</button>
                                    <input type="number" name="quantity" value="<?php echo (int)$item['quantity']; ?>" min="1" data-qty-input>
                                    <button type="button" data-qty="up" aria-label="Increase">+</button>
                                    <button class="btn sm outline" type="submit" style="margin-left:8px;border-radius:10px;">Update</button>
                                </form>
                            </div>
                        </div>
                        <div class="right">
                            <span class="price">$<?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                            <form method="post" action="cart.php" onsubmit="return confirm('Remove this item?');">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo (int)$item['id']; ?>">
                                <button class="cart-remove" type="submit">✕ Remove</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <aside>
                    <div class="summary-card">
                        <h3>Order summary</h3>
                        <div class="summary-row"><span>Items (<?php echo cartCount(); ?>)</span><span>$<?php echo getCartTotal(); ?></span></div>
                        <div class="summary-row"><span>Delivery</span><span class="muted">Calculated on request</span></div>
                        <div class="summary-row total"><span>Total</span><span class="amount">$<?php echo getCartTotal(); ?></span></div>

                        <form method="post" class="form-grid mt-3">
                            <div class="field">
                                <label for="address">Delivery address</label>
                                <textarea name="address" id="address" required placeholder="Street, city, postal code..."></textarea>
                            </div>
                            <div class="field-row">
                                <div class="field">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" value="<?php echo e($_SESSION['user']['phone'] ?? ''); ?>">
                                </div>
                                <div class="field">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" value="<?php echo e($_SESSION['user']['email'] ?? ''); ?>">
                                </div>
                            </div>
                            <?php if (isLoggedIn()): ?>
                                <button class="btn block" type="submit" name="checkout">Place order</button>
                            <?php else: ?>
                                <p class="alert warning" style="margin:0;">Please <a href="login.php">login</a> or <a href="register.php">register</a> to checkout.</p>
                            <?php endif; ?>
                        </form>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
