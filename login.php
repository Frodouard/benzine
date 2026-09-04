<?php
require_once __DIR__ . '/../inc/functions.php';
$message = flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = ?');
    $stmt->execute([$email, 'trader']);
    $trader = $stmt->fetch();
    if ($trader && !$trader['is_active']) {
        flash('Your account has been blocked. Contact MICKY SHOP for help.');
        header('Location: login.php');
        exit;
    }
    if ($trader && password_verify($password, $trader['password'])) {
        unset($trader['password']);
        $_SESSION['user'] = $trader;
        header('Location: index.php');
        exit;
    }
    flash('Invalid trader credentials.');
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trader Login - MICKY SHOP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="logo"><span class="brand-mark">M</span></div>
        <h2>Trader Login</h2>
        <p class="lead">Seller portal &middot; MICKY SHOP</p>
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
        <p class="auth-alt"><a href="../index.php">&larr; Back to shop</a></p>
    </div>
</div>
</body>
</html>
