<?php
require_once __DIR__ . '/functions.php';
$base = $base ?? '';
$page = $page ?? '';
$title = $title ?? 'MICKY SHOP';
$bodyClass = $bodyClass ?? '';
$user = $_SESSION['user'] ?? null;
$cartCount = cartCount();
$initials = $user ? strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body class="<?php echo htmlspecialchars($bodyClass); ?>">
<header class="site-header">
    <div class="container bar">
        <a class="brand" href="<?php echo $base; ?>index.php">
            <span class="brand-mark">M</span>
            <span class="brand-name">MICKY <span>SHOP</span></span>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>

        <nav class="main-nav" id="mainNav">
            <a href="<?php echo $base; ?>index.php" class="<?php echo $page === 'home' ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo $base; ?>products.php" class="<?php echo in_array($page, ['products', 'product']) ? 'active' : ''; ?>">Products</a>
            <a href="<?php echo $base; ?>contact.php" class="<?php echo $page === 'contact' ? 'active' : ''; ?>">Contact</a>

            <span class="nav-divider"></span>

            <?php if ($user): ?>
                <a href="<?php echo $base; ?>chat.php" class="<?php echo $page === 'chat' ? 'active' : ''; ?>">Chat</a>
                <a href="<?php echo $base; ?>cart.php" class="nav-cart <?php echo $page === 'cart' ? 'active' : ''; ?>">Cart
                    <?php if ($cartCount > 0): ?><span class="cart-badge"><?php echo $cartCount; ?></span><?php endif; ?>
                </a>
                <span class="nav-btn" title="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
                    <span class="nav-avatar"><?php echo htmlspecialchars($initials); ?></span><?php echo htmlspecialchars(mb_strtolower($user['role'] ?? 'customer')); ?>
                </span>
                <a href="<?php echo $base; ?>logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base; ?>login.php" class="<?php echo $page === 'login' ? 'active' : ''; ?>">Login</a>
                <a href="<?php echo $base; ?>register.php" class="<?php echo $page === 'register' ? 'active' : ''; ?>">Register</a>
            <?php endif; ?>

            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="<?php echo $base; ?>admin/dashboard.php">Admin panel</a>
            <?php elseif (($user['role'] ?? '') === 'trader'): ?>
                <a href="<?php echo $base; ?>trader/index.php">Trader panel</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="site-main">
