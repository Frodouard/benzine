<?php
require_once __DIR__ . '/functions.php';
$base = $base ?? '../';
$page = $page ?? '';
$title = $title ?? 'MICKY SHOP Admin';
$bodyClass = $bodyClass ?? '';
$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? 'admin';
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
<header class="admin-header">
    <div class="container bar">
        <h1>
            <span class="brand-mark" style="width:30px;height:30px;font-size:.9rem;">M</span>
            <?php echo $role === 'trader' ? 'Trader Dashboard' : 'Admin Panel'; ?>
        </h1>
        <nav class="admin-nav">
            <?php if ($role === 'trader'): ?>
                <a href="index.php" class="<?php echo in_array($page, ['trader_home', 'trader_form', 'trader_upload']) ? 'active' : ''; ?>">My products</a>
                <a href="product_form.php" class="<?php echo $page === 'trader_form' ? 'active' : ''; ?>">Add product</a>
                <a href="upload.php" class="<?php echo $page === 'trader_upload' ? 'active' : ''; ?>">Upload photo</a>
            <?php else: ?>
                <a href="dashboard.php" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
                <a href="products.php" class="<?php echo in_array($page, ['products', 'product_form', 'product_photo']) ? 'active' : ''; ?>">Products</a>
                <a href="orders.php" class="<?php echo $page === 'orders' ? 'active' : ''; ?>">Orders</a>
                <a href="messages.php" class="<?php echo $page === 'messages' ? 'active' : ''; ?>">Messages</a>
                <a href="users.php" class="<?php echo $page === 'users' ? 'active' : ''; ?>">Users</a>
                <a href="traders.php" class="<?php echo $page === 'traders' ? 'active' : ''; ?>">Traders</a>
                <a href="categories.php" class="<?php echo $page === 'categories' ? 'active' : ''; ?>">Categories</a>
            <?php endif; ?>
            <a href="<?php echo $base; ?>index.php">View shop</a>
            <a href="logout.php" class="<?php echo $page === 'logout' ? 'active' : ''; ?>">Logout</a>
        </nav>
    </div>
</header>
<main class="site-main">
