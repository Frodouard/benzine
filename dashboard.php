<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

$total_products = 0;
$total_quantity = 0;
$total_value = 0;

try {

    $result = $conn->query(
        "SELECT COUNT(*) AS total
         FROM products"
    );

    $total_products =
        $result->fetch_assoc()["total"];

    $result = $conn->query(
        "SELECT COALESCE(
            SUM(quantity), 0
         ) AS total
         FROM products"
    );

    $total_quantity =
        $result->fetch_assoc()["total"];

    $result = $conn->query(
        "SELECT COALESCE(
            SUM(unit_price * quantity),
            0
         ) AS total
         FROM products"
    );

    $total_value =
        $result->fetch_assoc()["total"];

} catch (mysqli_sql_exception $e) {

    $dashboard_error =
        "Could not load dashboard statistics.";

}

?>

<!DOCTYPE html>
<html>

<head>

<title>Dashboard</title>

<link rel="stylesheet"
      href="css/style.css">

</head>

<body>

<nav>

<div class="logo">
Inventory Management System
</div>

<a href="dashboard.php">
Dashboard
</a>

<a href="products/product_add.php">
Add Product
</a>

<a href="products/product_list.php">
Products
</a>

<a href="products/product_search.php">
Search
</a>

<a href="reports/inventory.php">
Report
</a>

<a href="logout.php">
Logout
</a>

</nav>

<div class="container">

<?php if (isset($dashboard_error)): ?>

<div class="error">
<?= htmlspecialchars($dashboard_error) ?>
</div>

<?php endif; ?>

<h1>
Welcome,
<?= htmlspecialchars(
    $_SESSION["full_name"]
) ?>
</h1>

<div class="cards">

<div class="card">

<h3>Total Products</h3>

<h1>
<?= $total_products ?>
</h1>

</div>

<div class="card">

<h3>Total Quantity</h3>

<h1>
<?= $total_quantity ?>
</h1>

</div>

<div class="card">

<h3>Total Stock Value</h3>

<h1>
<?= number_format(
    $total_value,
    2
) ?>
</h1>

<p>RWF</p>

</div>

</div>

</div>

</body>

</html>