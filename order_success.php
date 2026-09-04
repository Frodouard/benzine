<?php
require_once __DIR__ . '/inc/functions.php';
$orderId = intval($_GET['id'] ?? 0);
$pdo = getDb();
$stmt = $pdo->prepare('SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON o.customer_id = u.id WHERE o.id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();
if (!$order) {
    header('Location: index.php');
    exit;
}
$page = 'home';
$title = 'Order Confirmed - MICKY SHOP';
$message = flash();
require __DIR__ . '/inc/header.php';
?>
<section class="section">
    <div class="container">
        <div class="success-hero">
            <div class="success-ico">✓</div>
            <h2>Thank you for your order!</h2>
            <p class="text-soft">Your order has been received and is now pending confirmation.</p>
            <?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>

            <div class="order-box">
                <div class="row"><span>Order ID</span><strong>#<?php echo (int)$order['id']; ?></strong></div>
                <div class="row"><span>Customer</span><strong><?php echo e($order['customer_name']); ?></strong></div>
                <div class="row"><span>Total amount</span><strong>$<?php echo formatPrice($order['total_amount']); ?></strong></div>
                <div class="row"><span>Status</span><span><?php echo orderStatusBadge($order['status']); ?></span></div>
                <div class="row"><span>Delivery address</span><span><?php echo e($order['delivery_address']); ?></span></div>
                <div class="row"><span>Placed on</span><span><?php echo e($order['created_at']); ?></span></div>
            </div>

            <div class="flex" style="justify-content:center;">
                <a class="btn" href="index.php">Return home</a>
                <a class="btn outline" href="products.php">Keep shopping</a>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
