<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$page = 'traders';
$title = 'Traders - MICKY SHOP Admin';
$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = intval($_POST['user_id'] ?? 0);
    if ($action === 'toggle_active' && $userId) {
        $stmt = $pdo->prepare('UPDATE users SET is_active = 1 - is_active WHERE id = ? AND role = ?');
        $stmt->execute([$userId, 'trader']);
        flash('Trader updated.');
        header('Location: traders.php');
        exit;
    }
}

$traders = getAllTraders();
$sellerId = intval($_GET['seller'] ?? 0);
$products = $sellerId ? getProductsBySeller($sellerId) : [];
$message = flash();
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Trader oversight <span class="muted" style="font-size:.9rem;font-weight:400;">(<?php echo count($traders); ?>)</span></h2>
        </div>

        <?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>

        <?php if (!$traders): ?>
            <div class="empty-state"><h3>No traders registered yet</h3><p>New trader accounts will appear here.</p></div>
        <?php else: ?>
            <div class="table-wrap">
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($traders as $trader): ?>
                            <tr>
                                <td>#<?php echo (int)$trader['id']; ?></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <span class="nav-avatar" style="background:var(--accent);"><?php echo e(strtoupper(mb_substr($trader['name'], 0, 1))); ?></span>
                                        <strong><?php echo e($trader['name']); ?></strong>
                                    </div>
                                </td>
                                <td><?php echo e($trader['email']); ?></td>
                                <td><a href="traders.php?seller=<?php echo (int)$trader['id']; ?>"><?php echo (int)$trader['product_count']; ?> products</a></td>
                                <td><span class="badge <?php echo $trader['is_active'] ? 'badge-active' : 'badge-blocked'; ?>"><?php echo $trader['is_active'] ? 'active' : 'blocked'; ?></span></td>
                                <td>
                                    <form method="post" style="display:contents;">
                                        <input type="hidden" name="action" value="toggle_active">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$trader['id']; ?>">
                                        <button class="icon-link" type="submit"><?php echo $trader['is_active'] ? 'Disable' : 'Enable'; ?></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($products): ?>
            <div class="page-head mt-3">
                <h2>Products by selected trader</h2>
            </div>
            <div class="table-wrap">
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Image</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>#<?php echo (int)$product['id']; ?></td>
                                <td><?php echo e($product['name']); ?></td>
                                <td><?php echo e($product['category_name'] ?? 'Uncategorized'); ?></td>
                                <td>$<?php echo formatPrice($product['price']); ?></td>
                                <td>
                                    <?php if (!empty($product['image'])): ?>
                                        <img class="admin-product-image" src="../<?php echo e($product['image']); ?>" alt="">
                                    <?php else: ?>
                                        <span class="muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <td><a class="icon-link" href="product_form.php?id=<?php echo (int)$product['id']; ?>">Edit</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../inc/admin_footer.php'; ?>
