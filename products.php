<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$page = 'products';
$title = 'Manage Products - MICKY SHOP Admin';
$pdo = getDb();
$products = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC')->fetchAll();
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Products <span class="muted" style="font-size:.9rem;font-weight:400;">(<?php echo count($products); ?>)</span></h2>
            <div class="actions">
                <a class="btn small" href="product_form.php">+ Add product</a>
                <a class="btn outline small" href="export_products.php">Export CSV</a>
            </div>
        </div>

        <div class="table-wrap">
            <div class="table-scroll">
                <table>
                    <thead><tr><th>ID</th><th>Product</th><th>Category</th><th>Price</th><th>Created</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>#<?php echo (int)$product['id']; ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <img src="<?php echo e($product['image'] ?: '../assets/images/default.png'); ?>" alt="" style="width:44px;height:44px;border-radius:10px;object-fit:cover;">
                                    <strong><?php echo e($product['name']); ?></strong>
                                </div>
                            </td>
                            <td><?php echo e($product['category_name'] ?: 'Uncategorized'); ?></td>
                            <td>$<?php echo formatPrice($product['price']); ?></td>
                            <td><?php echo e($product['created_at']); ?></td>
                            <td>
                                <div class="flex" style="gap:6px;flex-wrap:nowrap;">
                                    <a class="icon-link" href="product_form.php?id=<?php echo (int)$product['id']; ?>">Edit</a>
                                    <a class="icon-link" href="product_photo.php?id=<?php echo (int)$product['id']; ?>">Photo</a>
                                    <a class="icon-link danger" href="product_delete.php?id=<?php echo (int)$product['id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
                                </div>
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
