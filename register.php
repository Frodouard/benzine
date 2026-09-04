<?php
require_once __DIR__ . '/inc/functions.php';
$page = 'register';
$title = 'Register - MICKY SHOP';
$bodyClass = 'auth-body';
$message = flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = in_array($_POST['role'] ?? 'customer', ['customer', 'trader']) ? $_POST['role'] : 'customer';

    if (!$name || !$email || !$password || $password !== $confirm) {
        flash('Please complete the form correctly.');
        header('Location: register.php');
        exit;
    }

    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        flash('Email already registered.');
        header('Location: register.php');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)');
    $insert->execute([$name, $email, $phone, $hash, $role]);
    flash('Registration successful. Please login.');
    header('Location: login.php');
    exit;
}
require __DIR__ . '/inc/header.php';
?>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="logo"><span class="brand-mark">M</span></div>
        <h2>Create your account</h2>
        <p class="lead">Join MICKY SHOP as a customer or seller</p>
        <?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>
        <form method="post" class="form-grid">
            <div class="field">
                <label for="name">Full name</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="field">
                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone">
            </div>
            <div class="field">
                <label>I am a</label>
                <div class="role-picker">
                    <label class="role-card">
                        <input type="radio" name="role" value="customer" checked>
                        <span class="r-ico">🛍️</span>
                        <strong>Customer</strong>
                        <span>Shopper</span>
                    </label>
                    <label class="role-card">
                        <input type="radio" name="role" value="trader">
                        <span class="r-ico">🏪</span>
                        <strong>Trader</strong>
                        <span>Seller</span>
                    </label>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="field">
                    <label for="confirm_password">Confirm password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
            </div>
            <button class="btn block" type="submit">Create account</button>
        </form>
        <p class="auth-alt">Already registered? <a href="login.php"><strong>Login here</strong></a></p>
    </div>
</div>
<?php require __DIR__ . '/inc/footer.php'; ?>
