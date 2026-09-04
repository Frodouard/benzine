<?php
require_once __DIR__ . '/inc/functions.php';
$page = 'login';
$title = 'Login - MICKY SHOP';
$bodyClass = 'auth-body';
$message = flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && !$user['is_active']) {
        flash('Your account has been blocked. Contact MICKY SHOP for help.');
        header('Location: login.php');
        exit;
    }
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        if ($user['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } elseif ($user['role'] === 'trader') {
            header('Location: trader/index.php');
        } else {
            header('Location: index.php');
        }
        exit;
    }
    flash('Invalid login credentials.');
    header('Location: login.php');
    exit;
}
require __DIR__ . '/inc/header.php';
?>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="logo"><span class="brand-mark">M</span></div>
        <h2>Welcome back</h2>
        <p class="lead">Login to your MICKY SHOP account</p>
        <?php if ($message): ?><div class="alert"><?php echo e($message); ?></div><?php endif; ?>
        <form method="post" class="form-grid">
            <div class="field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required autofocus>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button class="btn block" type="submit">Login</button>
        </form>
        <p class="auth-alt">Don't have an account? <a href="register.php"><strong>Register now</strong></a></p>
    </div>
</div>
<?php require __DIR__ . '/inc/footer.php'; ?>
