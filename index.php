<?php
require_once __DIR__ . '/../inc/functions.php';
requireTrader();
$page = 'trader_home';
$title = 'Trader Dashboard - MICKY SHOP';
$trader = $_SESSION['user'];
$products = getMyProducts($trader['id']);
$message = flash();
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Welcome back, <?php echo e($trader['name']); ?> 👋</h2>
            <a class="btn small" href="product_form.php">+ Add product</a>
        </div>

        <?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>

        <div class="admin-summary">
            <div class="summary-card"><div class="s-ico">📦</div><strong><?php echo count($products); ?></strong><span>My products</span></div>
            <div class="summary-card green"><div class="s-ico">💰</div><strong>Live</strong><span>Listings active</span></div>
            <div class="summary-card accent"><div class="s-ico">🏪</div><strong>Trader</strong><span>Seller account</span></div>
        </div>

        <div class="page-head mt-2">
            <h2>My products</h2>
            <div class="actions">
                <a class="btn outline small" href="product_form.php">+ New product</a>
                <a class="btn outline small" href="upload.php">Upload photo</a>
            </div>
        </div>

        <?php if (!$products): ?>
            <div class="empty-state">
                <div class="e-ico">🏷️</div>
                <h3>You have no products yet</h3>
                <p>Add your first product, then upload a photo to get started.</p>
                <a class="btn" href="product_form.php">Add your first product</a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>ID</th><th>Product</th><th>Category</th><th>Price</th><th>Photo</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>#<?php echo (int)$product['id']; ?></td>
                                <td><strong><?php echo e($product['name']); ?></strong></td>
                                <td><?php echo e($product['category_name'] ?: 'Uncategorized'); ?></td>
                                <td>$<?php echo formatPrice($product['price']); ?></td>
                                <td>
                                    <?php if (!empty($product['image'])): ?>
                                        <img class="admin-product-image" src="../<?php echo e($product['image']); ?>" alt="">
                                    <?php else: ?>
                                        <span class="muted">No photo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="flex" style="gap:6px;flex-wrap:nowrap;">
                                        <a class="icon-link" href="upload.php?id=<?php echo (int)$product['id']; ?>">Photo</a>
                                        <a class="icon-link" href="product_form.php?id=<?php echo (int)$product['id']; ?>">Edit</a>
                                        <a class="icon-link danger" href="product_delete.php?id=<?php echo (int)$product['id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
                                    </div>
                                </td>
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
