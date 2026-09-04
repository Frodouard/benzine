<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$page = 'categories';
$title = 'Categories - MICKY SHOP Admin';
$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name'] ?? '');

    if ($action === 'add' && $name) {
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$name]);
        flash('Category added.');
    } elseif ($action === 'rename' && $name) {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);
        flash('Category renamed.');
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('UPDATE products SET category_id = NULL WHERE category_id = ?');
        $stmt->execute([$id]);
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        flash('Category deleted. Products are now uncategorized.');
    }
    header('Location: categories.php');
    exit;
}

$categories = getAllCategories();
$message = flash();
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Categories <span class="muted" style="font-size:.9rem;font-weight:400;">(<?php echo count($categories); ?>)</span></h2>
        </div>

        <?php if ($message): ?><div class="alert success"><?php echo e($message); ?></div><?php endif; ?>

        <div class="form-card mb-3" style="max-width:560px;">
            <form method="post" style="display:flex;gap:10px;">
                <input type="hidden" name="action" value="add">
                <input type="text" name="name" placeholder="New category name" required>
                <button class="btn" type="submit">Add category</button>
            </form>
        </div>

        <div class="table-wrap" style="max-width:560px;">
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Products</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>#<?php echo (int)$category['id']; ?></td>
                        <td>
                            <form method="post" style="display:flex;gap:8px;align-items:center;">
                                <input type="hidden" name="action" value="rename">
                                <input type="hidden" name="id" value="<?php echo (int)$category['id']; ?>">
                                <input type="text" name="name" value="<?php echo e($category['name']); ?>" required style="width:180px;">
                                <button class="btn sm outline" type="submit">Rename</button>
                            </form>
                        </td>
                        <td><?php echo (int)$category['product_count']; ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Delete this category? Its products will become uncategorized.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int)$category['id']; ?>">
                                <button class="icon-link danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../inc/admin_footer.php'; ?>
