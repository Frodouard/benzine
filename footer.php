<?php
$base = $base ?? '';
?>
</main>
<footer class="site-footer">
    <div class="container">
        <div class="grid">
            <div>
                <div class="foot-brand">
                    <span class="brand-mark">M</span>
                    <span class="brand-name">MICKY <span>SHOP</span></span>
                </div>
                <p>A trusted online marketplace delivering quality products with fast, reliable service and secure checkout.</p>
                <div class="foot-social">
                    <a href="#" aria-label="Facebook"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.7l-.4 2.9h-2.3v7A10 10 0 0 0 22 12z"/></svg></a>
                    <a href="#" aria-label="Twitter"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-6.8 7.8L23.3 22h-6.3l-4.9-6.4L6.5 22H3.4l7.3-8.3L1.5 2h6.4l4.4 5.9L18.9 2zm-1.1 18h1.7L7.6 3.8H5.8L17.8 20z"/></svg></a>
                    <a href="#" aria-label="Instagram"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.2c3.2 0 3.6 0 4.8.1 1.2.1 1.8.2 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.2.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 1.2-.2 1.8-.4 2.2a3.8 3.8 0 0 1-.9 1.4c-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.2.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-1.2-.1-1.8-.2-2.2-.4a3.8 3.8 0 0 1-1.4-.9 3.8 3.8 0 0 1-.9-1.4c-.2-.4-.4-1-.4-2.2-.1-1.2-.1-1.6-.1-4.8s0-3.6.1-4.8c.1-1.2.2-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4 1.2-.1 1.6-.1 4.8-.1zm0 2c-3.1 0-3.5 0-4.7.1-1.1.1-1.7.2-2.1.4-.5.2-.9.4-1.2.8-.4.4-.6.7-.8 1.2-.2.4-.3 1-.4 2.1-.1 1.2-.1 1.6-.1 4.7s0 3.5.1 4.7c.1 1.1.2 1.7.4 2.1.2.5.4.9.8 1.2.4.4.7.6 1.2.8.4.2 1 .3 2.1.4 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c1.1-.1 1.7-.2 2.1-.4.5-.2.9-.4 1.2-.8.4-.4.6-.7.8-1.2.2-.4.3-1 .4-2.1.1-1.2.1-1.6.1-4.7s0-3.5-.1-4.7c-.1-1.1-.2-1.7-.4-2.1a3.2 3.2 0 0 0-.8-1.2 3.2 3.2 0 0 0-1.2-.8c-.4-.2-1-.3-2.1-.4-1.2-.1-1.6-.1-4.7-.1zm0 3.3a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11zm0 2a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7zm5.7-2.3a1.3 1.3 0 1 1 0 2.6 1.3 1.3 0 0 1 0-2.6z"/></svg></a>
                </div>
            </div>
            <div>
                <h4>Shop</h4>
                <a href="<?php echo $base; ?>products.php">All products</a>
                <a href="<?php echo $base; ?>catalog.php">Download catalog</a>
                <a href="<?php echo $base; ?>index.php#categories">Categories</a>
            </div>
            <div>
                <h4>Account</h4>
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                    <a href="<?php echo $base; ?>admin/dashboard.php">Admin dashboard</a>
                <?php elseif (($user['role'] ?? '') === 'trader'): ?>
                    <a href="<?php echo $base; ?>trader/index.php">Trader dashboard</a>
                <?php else: ?>
                    <a href="<?php echo $base; ?>login.php">Login</a>
                    <a href="<?php echo $base; ?>register.php">Create account</a>
                <?php endif; ?>
                <a href="<?php echo $base; ?>cart.php">My cart</a>
                <a href="<?php echo $base; ?>chat.php">Support chat</a>
            </div>
            <div>
                <h4>Contact</h4>
                <p>MICKY SHOP Marketplace</p>
                <p>customer@mickyshop.com</p>
                <p>+1 (555) 010-2030</p>
                <a href="<?php echo $base; ?>contact.php">Send a message</a>
            </div>
        </div>
        <div class="foot-bottom">
            <span>&copy; <?php echo date('Y'); ?> MICKY SHOP Marketplace. All rights reserved.</span>
            <span>Secure checkout &middot; Fast delivery &middot; Trusted sellers</span>
        </div>
    </div>
</footer>
<script src="<?php echo $base; ?>assets/js/main.js"></script>
</body>
</html>
