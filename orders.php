<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$page = 'orders';
$title = 'Manage Orders - MICKY SHOP Admin';
$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_id']) && !empty($_POST['status'])) {
    $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $stmt->execute([$_POST['status'], intval($_POST['order_id'])]);
    flash('Order status updated.');
    header('Location: orders.php');
    exit;
}

$orders = $pdo->query(
    "SELECT o.*, u.name AS customer_name,
        GROUP_CONCAT(CONCAT(oi.quantity, ' x ', p.name) ORDER BY oi.id SEPARATOR '; ') AS items
     FROM orders o
     JOIN users u ON o.customer_id = u.id
     LEFT JOIN order_items oi ON oi.order_id = o.id
     LEFT JOIN products p ON p.id = oi.product_id
     GROUP BY o.id
     ORDER BY o.created_at DESC"
)->fetchAll();
$message = flash();
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Orders <span class="muted" style="font-size:.9rem;font-weight:400;">(<?php echo count($orders); ?>)</span></h2>
            <a class="btn small" href="export_orders.php">Export orders CSV</a>
        </div>

        <?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>

        <div class="table-wrap">
            <div class="table-scroll">
                <table>
                    <thead><tr><th>ID</th><th>Customer</th><th>Products</th><th>Total</th><th>Address</th><th>Contact</th><th>Status</th><th>Date</th><th>Update</th></tr></thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo (int)$order['id']; ?></td>
                            <td><?php echo e($order['customer_name']); ?></td>
                            <td><?php echo e($order['items'] ?: 'No items'); ?></td>
                            <td><strong>$<?php echo formatPrice($order['total_amount']); ?></strong></td>
                            <td><?php echo e($order['delivery_address']); ?></td>
                            <td>
                                <?php echo e($order['contact_phone'] ?: ''); ?>
                                <span class="sub"><?php echo e($order['contact_email'] ?: ''); ?></span>
                            </td>
                            <td><?php echo orderStatusBadge($order['status']); ?></td>
                            <td><?php echo e($order['created_at']); ?></td>
                            <td>
                                <form method="post" class="status-form" style="display:flex;gap:6px;align-items:center;">
                                    <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
                                    <select name="status" style="width:auto;padding:7px 10px;">
                                        <?php foreach (['Pending', 'Confirmed', 'Out for delivery', 'Delivered'] as $status): ?>
                                            <option value="<?php echo $status; ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn sm" type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../inc/admin_footer.php'; ?>
