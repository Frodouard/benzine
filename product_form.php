<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$page = 'product_form';
$pdo = getDb();
$categories = getCategories();
$product = null;

if (!empty($_GET['id'])) {
    $product = getProduct($_GET['id']);
}
$title = $product ? 'Edit Product - MICKY SHOP Admin' : 'Add Product - MICKY SHOP Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = $_POST['category_id'] ?: null;
    $image = '';

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/../assets/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $target = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $image = 'assets/images/' . basename($_FILES['image']['name']);
    }

    if ($product) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = COALESCE(NULLIF(?, ''), image) WHERE id = ?");
        $stmt->execute([$name, $description, $price, $category_id, $image, $product['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $description, $price, $category_id, $image]);
    }
    flash('Product saved.');
    header('Location: products.php');
    exit;
}
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2><?php echo $product ? 'Edit product' : 'Add product'; ?></h2>
            <a class="btn outline small" href="products.php">&larr; Back to products</a>
        </div>

        <div class="form-card" style="max-width:640px;">
            <form method="post" enctype="multipart/form-data" class="form-grid">
                <div class="field">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" required value="<?php echo e($product['name'] ?? ''); ?>">
                </div>
                <div class="field">
                    <label for="description">Description</label>
                    <textarea name="description" id="description"><?php echo e($product['description'] ?? ''); ?></textarea>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="price">Price ($)</label>
                        <input type="number" step="0.01" min="0" name="price" id="price" required value="<?php echo e($product['price'] ?? ''); ?>">
                    </div>
                    <div class="field">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id">
                            <option value="">Uncategorized</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo (int)$category['id']; ?>" <?php echo (!empty($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : ''; ?>><?php echo e($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image" accept="image/*">
                    <span class="hint">Upload JPG, PNG, GIF or WebP. Leave empty to keep the current photo.</span>
                    <?php if (!empty($product['image'])): ?>
                        <img class="admin-product-image" src="../<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>">
                    <?php endif; ?>
                </div>
                <div class="flex">
                    <button class="btn" type="submit"><?php echo $product ? 'Save changes' : 'Add product'; ?></button>
                    <a class="btn outline" href="products.php">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../inc/admin_footer.php'; ?>
