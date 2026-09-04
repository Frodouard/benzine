<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$page = 'product_photo';
$title = 'Upload Product Photo - MICKY SHOP Admin';
$pdo = getDb();
$products = $pdo->query('SELECT id, name, image FROM products ORDER BY name')->fetchAll();
$selectedId = intval($_GET['id'] ?? $_POST['product_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedId = intval($_POST['product_id'] ?? 0);
    $product = getProduct($selectedId);
    if (!$product) {
        flash('Please choose a product.');
    } elseif (empty($_FILES['image']['name'])) {
        flash('Please choose a photo to upload.');
    } else {
        $file = $_FILES['image'];
        $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed) || !getimagesize($file['tmp_name'])) {
            flash('Invalid file. Please upload a JPG, PNG, GIF or WebP image.');
        } else {
            $uploadDir = __DIR__ . '/../assets/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = $selectedId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $target = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $stmt = $pdo->prepare('UPDATE products SET image = ? WHERE id = ?');
                $stmt->execute(['assets/images/' . $filename, $selectedId]);
                flash('Photo uploaded successfully.');
                header('Location: product_photo.php?id=' . $selectedId);
                exit;
            }
            flash('Failed to save the uploaded photo.');
        }
    }
}
$product = $selectedId ? getProduct($selectedId) : null;
$message = flash();
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Upload product photo</h2>
            <a class="btn outline small" href="products.php">&larr; Back to products</a>
        </div>

        <?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>

        <div class="form-card" style="max-width:560px;">
            <form method="post" enctype="multipart/form-data" class="form-grid">
                <div class="field">
                    <label for="product_id">Product</label>
                    <select name="product_id" id="product_id" onchange="this.form.submit()">
                        <option value="">-- Choose a product --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?php echo (int)$p['id']; ?>" <?php echo $p['id'] === $selectedId ? 'selected' : ''; ?>><?php echo e($p['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($product): ?>
                    <div class="field">
                        <span class="hint">Current photo:</span>
                        <?php if (!empty($product['image'])): ?>
                            <img class="admin-product-image" src="../<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>">
                        <?php else: ?>
                            <p class="muted">No photo yet.</p>
                        <?php endif; ?>
                    </div>
                    <div class="field">
                        <label for="image">New photo</label>
                        <input type="file" name="image" id="image" accept="image/*" required>
                        <span class="hint">JPG, PNG, GIF or WebP.</span>
                    </div>
                    <button class="btn" type="submit">Upload photo</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../inc/admin_footer.php'; ?>
