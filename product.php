<?php
require_once __DIR__ . '/inc/functions.php';
$product = getProduct($_GET['id'] ?? 0);
if (!$product) {
    header('Location: products.php');
    exit;
}
$page = 'product';
$title = $product['name'] . ' - MICKY SHOP';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    addToCart($product['id'], $quantity);
    flash('Product added to cart.');
    header('Location: cart.php  ');
    exit;
}
require __DIR__ . '/inc/header.php';
?>
<section class="section-tight">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span>
            <a href="products.php">Products</a> <span>/</span>
            <span><?php echo e($product['name']); ?></span>
        </nav>

        <div class="product-detail">
            <div class="media">
                <img src="<?php echo e($product['image'] ?: 'assets/images/default.png'); ?>" alt="<?php echo e($product['name']); ?>">
            </div>
            <div class="pd-body">
                <div class="meta">
                    <?php if (!empty($product['category_name'])): ?>
                        <span class="cat-chip plain"><?php echo e($product['category_name']); ?></span>
                    <?php else: ?>
                        <span class="cat-chip plain">Uncategorized</span>
                    <?php endif; ?>
                    <span class="badge badge-success">In stock</span>
                </div>
                <h2><?php echo e($product['name']); ?></h2>
                <div class="price-lg">$<?php echo formatPrice($product['price']); ?></div>
                <p class="desc"><?php echo nl2br(e($product['description'])); ?></p>

                <form method="post" action="product.php?id=<?php echo (int)$product['id']; ?>">
                    <div class="pd-cta">
                        <div class="qty-stepper">
                            <button type="button" data-qty="down" aria-label="Decrease">&minus;</button>
                            <input type="number" name="quantity" value="1" min="1" data-qty-input>
                            <button type="button" data-qty="up" aria-label="Increase">+</button>
                        </div>
                        <button class="btn" type="submit">Add to cart</button>
                    </div>
                </form>

                <div class="pd-facts">
                    <div class="pd-fact"><strong>Delivery</strong><span>Fast tracked delivery with real-time status updates.</span></div>
                    <div class="pd-fact"><strong>Support</strong><span>Questions? <a href="chat.php">Chat with us</a> or <a href="contact.php">send a message</a>.</span></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
