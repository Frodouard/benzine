<?php
require_once __DIR__ . '/inc/functions.php';
$page = 'products';
$title = 'Products - MICKY SHOP';
$search = trim($_GET['search'] ?? '');
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$categories = getCategories();
$products = getProducts($search ?: null, $categoryId ?: null);
$count = count($products);
require __DIR__ . '/inc/header.php';
?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="kicker">Marketplace</span>
            <h2>All products</h2>
            <p><?php echo $count; ?> product<?php echo $count === 1 ? '' : 's'; ?> available
                <?php echo $search ? 'for &ldquo;' . e($search) . '&rdquo;' : ''; ?>
            </p>
        </div>

        <div class="filter-bar">
            <form action="products.php" method="get">
                <div class="search-wrap">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="search" name="search" placeholder="Search products..." value="<?php echo e($search); ?>">
                </div>
                <select name="category" onchange="this.form.submit()">
                    <option value="">All categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo $categoryId === (int)$cat['id'] ? 'selected' : ''; ?>><?php echo e($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn" type="submit">Search</button>
                <a class="btn outline small" href="products.php">Reset</a>
            </form>
            <a class="btn outline small" href="catalog.php">Download catalog (CSV)</a>
        </div>

        <?php if (!$products): ?>
            <div class="empty-state">
                <div class="e-ico">🔍</div>
                <h3>No products found</h3>
                <p>Try a different search term or category.</p>
                <a class="btn" href="products.php">View all products</a>
            </div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($products as $product): ?>
                <article class="card">
                    <div class="media">
                        <a href="product.php?id=<?php echo (int)$product['id']; ?>">
                            <img src="<?php echo e($product['image'] ?: 'assets/images/default.png'); ?>" alt="<?php echo e($product['name']); ?>">
                        </a>
                        <?php if (!empty($product['category_name'])): ?>
                            <span class="cat-chip"><?php echo e($product['category_name']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3><a href="product.php?id=<?php echo (int)$product['id']; ?>"><?php echo e($product['name']); ?></a></h3>
                        <p class="desc"><?php echo e(substr($product['description'], 0, 80)); ?></p>
                        <div class="card-row">
                            <span class="price">$<?php echo formatPrice($product['price']); ?></span>
                            <a class="btn small" href="product.php?id=<?php echo (int)$product['id']; ?>">Details</a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
