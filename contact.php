<?php
require_once __DIR__ . '/inc/functions.php';
$page = 'contact';
$title = 'Contact - MICKY SHOP';
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message) {
        file_put_contents(__DIR__ . '/contact_messages.txt', date('Y-m-d H:i:s') . " | $name | $email | $subject | $message\n", FILE_APPEND);
        $success = 'Thank you! Your message has been submitted. We will reply shortly.';
    }
}
require __DIR__ . '/inc/header.php';
?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="kicker">Get in touch</span>
            <h2>Contact MICKY SHOP</h2>
            <p>Questions about an order, delivery or a product? We are here to help.</p>
        </div>

        <div class="grid-2">
            <div class="form-card">
                <?php if ($success): ?><div class="alert success"><?php echo e($success); ?></div><?php endif; ?>
                <form method="post" class="form-grid">
                    <div class="field-row">
                        <div class="field">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" required>
                        </div>
                        <div class="field">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" required>
                        </div>
                    </div>
                    <div class="field">
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" id="subject" placeholder="Order, delivery, product...">
                    </div>
                    <div class="field">
                        <label for="message">Message</label>
                        <textarea name="message" id="message" rows="5" required></textarea>
                    </div>
                    <button class="btn" type="submit">Send message</button>
                </form>
            </div>

            <div>
                <div class="feature">
                    <div class="f-ico">📍</div>
                    <h3>Visit us</h3>
                    <p>MICKY SHOP Marketplace<br>Main Street, Suite 10</p>
                </div>
                <div class="feature">
                    <div class="f-ico">📧</div>
                    <h3>Email us</h3>
                    <p>customer@mickyshop.com</p>
                </div>
                <div class="feature">
                    <div class="f-ico">📞</div>
                    <h3>Call us</h3>
                    <p>+1 (555) 010-2030<br>Mon&ndash;Sat, 9:00&ndash;18:00</p>
                </div>
                <?php if (isLoggedIn()): ?>
                <div class="feature">
                    <div class="f-ico">💬</div>
                    <h3>Live chat</h3>
                    <p>Prefer real-time help? <a href="chat.php">Start a chat</a> with our team.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
