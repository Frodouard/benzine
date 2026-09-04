<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$page = 'dashboard';
$title = 'Dashboard - MICKY SHOP Admin';
$pdo = getDb();

$stats = [
    'products'   => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'orders'     => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'customers'  => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
    'traders'    => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'trader'")->fetchColumn(),
    'categories' => (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    'sales'      => (float)$pdo->query('SELECT COALESCE(SUM(total_amount), 0) FROM orders')->fetchColumn(),
    'pending'    => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn(),
    'messages'   => (int)$pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn(),
];

$statusBreakdown = $pdo->query("SELECT status, COUNT(*) AS count FROM orders GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
$maxStatus = 1;
foreach ($statusBreakdown as $s) {
    if ((int)$s['count'] > $maxStatus) $maxStatus = (int)$s['count'];
}

$orders = $pdo->query('SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON o.customer_id = u.id ORDER BY o.created_at DESC LIMIT 8')->fetchAll();
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Overview</h2>
            <a class="btn small" href="export_orders.php">Export orders CSV</a>
        </div>

        <div class="admin-summary">
            <div class="summary-card"><div class="s-ico">📦</div><strong><?php echo $stats['products']; ?></strong><span>Products</span></div>
            <div class="summary-card accent"><div class="s-ico">🛒</div><strong><?php echo $stats['orders']; ?></strong><span>Orders</span></div>
            <div class="summary-card green"><div class="s-ico">💰</div><strong>$<?php echo number_format($stats['sales'], 2); ?></strong><span>Total sales</span></div>
            <div class="summary-card orange"><div class="s-ico">⏳</div><strong><?php echo $stats['pending']; ?></strong><span>Pending orders</span></div>
            <div class="summary-card"><div class="s-ico">👤</div><strong><?php echo $stats['customers']; ?></strong><span>Customers</span></div>
            <div class="summary-card"><div class="s-ico">🏪</div><strong><?php echo $stats['traders']; ?></strong><span>Traders</span></div>
            <div class="summary-card"><div class="s-ico">🗂️</div><strong><?php echo $stats['categories']; ?></strong><span>Categories</span></div>
            <div class="summary-card"><div class="s-ico">💬</div><strong><?php echo $stats['messages']; ?></strong><span>Messages</span></div>
        </div>

        <div class="grid-2">
            <div class="chart-section">
                <h2>Orders by status</h2>
                <?php if (!$statusBreakdown): ?>
                    <p class="muted">No orders yet.</p>
                <?php else: ?>
                    <div class="live-chart">
                        <?php foreach ($statusBreakdown as $s): ?>
                            <div class="chart-row">
                                <span class="chart-label"><?php echo e($s['status']); ?></span>
                                <div class="chart-bar-track"><div class="chart-bar" style="width:<?php echo round($s['count'] / $maxStatus * 100); ?>%"></div></div>
                                <span class="chart-value"><?php echo (int)$s['count']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-wrap">
                <h2 style="padding:20px 20px 0;font-size:1.1rem;">Recent orders</h2>
                <?php if (!$orders): ?>
                    <p class="muted" style="padding:20px;">No orders yet.</p>
                <?php else: ?>
                    <div class="table-scroll" style="margin-top:14px;">
                        <table>
                            <thead><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo (int)$order['id']; ?></td>
                                    <td><?php echo e($order['customer_name']); ?></td>
                                    <td>$<?php echo formatPrice($order['total_amount']); ?></td>
                                    <td><?php echo orderStatusBadge($order['status']); ?></td>
                                    <td><?php echo e($order['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../inc/admin_footer.php'; ?>
