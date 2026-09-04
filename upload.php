<?php
require_once __DIR__ . '/../inc/functions.php';
requireTrader();
$page = 'trader_upload';
$title = 'Upload Product Photo - MICKY SHOP';
$trader = $_SESSION['user'];
$pdo = getDb();
$products = getMyProducts($trader['id']);
$selectedId = intval($_GET['id'] ?? $_POST['product_id'] ?? 0);
$product = $selectedId ? getProduct($selectedId) : null;

if ($product && $product['seller_id'] != $trader['id']) {
    $product = null;
    $selectedId = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedId = intval($_POST['product_id'] ?? 0);
    $product = getProduct($selectedId);
    if (!$product || $product['seller_id'] != $trader['id']) {
        flash('Please choose one of your products.');
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
            $filename = $trader['id'] . '_' . $selectedId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $target = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $stmt = $pdo->prepare('UPDATE products SET image = ? WHERE id = ? AND seller_id = ?');
                $stmt->execute(['assets/images/' . $filename, $selectedId, $trader['id']]);
                flash('Photo uploaded successfully.');
                header('Location: upload.php?id=' . $selectedId);
                exit;
            }
            flash('Failed to save the uploaded photo.');
        }
    }
}
$message = flash();
require __DIR__ . '/../inc/admin_header.php';
?>
<section class="section-tight">
    <div class="container">
        <div class="page-head">
            <h2>Upload product photo</h2>
            <a class="btn outline small" href="index.php">&larr; Back to my products</a>
        </div>

        <?php if ($message): ?><div class="alert <?php echo strpos($message, 'successfully') !== false ? 'success' : ''; ?>"><?php echo e($message); ?></div><?php endif; ?>

        <?php if (!$products): ?>
            <div class="empty-state">
                <div class="e-ico">🏷️</div>
                <h3>No products yet</h3>
                <p>Add your first product before uploading a photo.</p>
                <a class="btn" href="product_form.php">Add a product</a>
            </div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../inc/admin_footer.php'; ?>
