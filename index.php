<?php
require_once __DIR__ . '/inc/functions.php';
$page = 'home';
$title = 'MICKY SHOP - Premium Online Marketplace';
$featured = getRecentProducts(8);
$heroItems = getFeaturedProducts(3);
$categories = getCategories();
$pdo = getDb();
$stats = [
    'products'  => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'orders'    => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'customers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
    'categories'=> count($categories),
];
require __DIR__ . '/inc/header.php';
?>
<div class="hero">
    <div class="container">
        <div>
            <span class="kicker">Trusted online marketplace</span>
            <h2>Shop smarter with <span>MICKY SHOP</span></h2>
            <p>Discover quality products from trusted sellers, track your delivery in real time, and enjoy a secure, effortless checkout experience.</p>
            <div class="actions">
                <a class="btn" href="products.php">Browse products</a>
                <a class="btn outline" href="contact.php">Contact us</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><strong><?php echo $stats['products']; ?></strong><span>Products listed</span></div>
                <div class="hero-stat"><strong><?php echo $stats['categories']; ?></strong><span>Categories</span></div>
                <div class="hero-stat"><strong><?php echo $stats['customers']; ?></strong><span>Happy customers</span></div>
            </div>
        </div>
        <div class="hero-art">
            <div class="hero-card">
                <h3>Featured now <a href="products.php">View all &rarr;</a></h3>
                <div class="hero-list">
                    <?php foreach ($heroItems as $p): ?>
                    <div class="hero-item">
                        <img class="thumb" src="<?php echo e($p['image'] ?: 'assets/images/default.png'); ?>" alt="<?php echo e($p['name']); ?>">
                        <div>
                            <div class="t-name"><?php echo e($p['name']); ?></div>
                            <div class="t-price">$<?php echo formatPrice($p['price']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="section" id="categories">
    <div class="container">
        <div class="section-head">
            <span class="kicker">Categories</span>
            <h2>Shop by category</h2>
            <p>Explore our curated collections and find exactly what you need.</p>
        </div>
        <div class="cat-grid">
            <?php foreach ($categories as $i => $cat): ?>
            <a class="cat-tile" href="products.php?category=<?php echo (int)$cat['id']; ?>">
                <span class="ico"><?php echo ['🛒', '🏠', '👕', '🧸', '📱', '⚡', '🎁', '🍎'][$i % 8]; ?></span>
                <div>
                    <strong><?php echo e($cat['name']); ?></strong>
                    <span>Shop now &rarr;</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-tight">
    <div class="container">
        <div class="section-head">
            <span class="kicker">Latest arrivals</span>
            <h2>Featured products</h2>
            <p>Hand-picked items our customers love right now.</p>
        </div>
        <?php if ($featured): ?>
        <div class="card-grid">
            <?php foreach ($featured as $product): ?>
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
                        <a class="btn small" href="product.php?id=<?php echo (int)$product['id']; ?>">View</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><h3>No products yet</h3><p>Products added by our sellers will appear here.</p></div>
        <?php endif; ?>
    </div>
</section>

<section class="section-tight">
    <div class="container">
        <div class="section-head">
            <span class="kicker">Why choose us</span>
            <h2>Built for a better shopping experience</h2>
        </div>
        <div class="features-grid">
            <div class="feature">
                <div class="f-ico">🚚</div>
                <h3>Fast, tracked delivery</h3>
                <p>Order today and follow every step of your delivery status in real time.</p>
            </div>
            <div class="feature">
                <div class="f-ico">🔒</div>
                <h3>Secure checkout</h3>
                <p>Protected accounts and reliable order management keep your purchases safe.</p>
            </div>
            <div class="feature">
                <div class="f-ico">💬</div>
                <h3>Direct support chat</h3>
                <p>Message our team any time for help with orders, products or delivery.</p>
            </div>
            <div class="feature">
                <div class="f-ico">⭐</div>
                <h3>Trusted sellers</h3>
                <p>Every trader is managed and monitored by our marketplace team.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-tight">
    <div class="container">
        <div class="chart-section">
            <h2>Live shop overview</h2>
            <p id="chart-total" class="chart-total">Loading...</p>
            <div id="live-chart" class="live-chart"></div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
<script>
(function () {
    function loadChart() {
        fetch('chart_data.php?t=' + Date.now(), { headers: { 'Cache-Control': 'no-cache' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var totalEl = document.getElementById('chart-total');
                if (totalEl) totalEl.textContent = data.total + ' product' + (data.total === 1 ? '' : 's') + ' available in ' + data.categoryCount + ' categor' + (data.categoryCount === 1 ? 'y' : 'ies');
                var el = document.getElementById('live-chart');
                if (!el) return;
                var html = '';
                var max = 1;
                data.categories.forEach(function (c) { if (+c.count > max) max = +c.count; });
                data.categories.forEach(function (c) {
                    var pct = Math.round((+c.count / max) * 100);
                    html += '<div class="chart-row"><span class="chart-label">' + c.name + '</span>'
                         + '<div class="chart-bar-track"><div class="chart-bar" style="width:' + pct + '%"></div></div>'
                         + '<span class="chart-value">' + c.count + '</span></div>';
                });
                el.innerHTML = html || '<p class="muted">No products yet.</p>';
            })
            .catch(function () {});
    }
    loadChart();
    setInterval(loadChart, 10000);
})();
</script>
