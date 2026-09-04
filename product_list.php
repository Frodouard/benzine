<?php

session_start();

require_once "../config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$result = null;
$list_error = "";

try {

    $result = $conn->query(

        "SELECT *,
                (unit_price * quantity)
                AS stock_value

         FROM products

         ORDER BY id DESC"

    );

} catch (mysqli_sql_exception $e) {

    $list_error =
        "Could not load products.";

}

?>

<!DOCTYPE html>
<html>

<head>

<title>Products</title>

<link rel="stylesheet"
      href="../css/style.css">

</head>

<body>

<?php include "../nav.php"; ?>

<div class="container">

<h2>Product Inventory</h2>

<?php if ($list_error): ?>

<div class="error">
<?= htmlspecialchars($list_error) ?>
</div>

<?php elseif ($result->num_rows == 0): ?>

<p>
No products registered yet.
</p>

<?php else: ?>

<table>

<tr>

<th>Product Code</th>

<th>Product Name</th>

<th>Category</th>

<th>Supplier</th>

<th>Unit Price</th>

<th>Quantity</th>

<th>Stock Value</th>

<th>Action</th>

</tr>

<?php while (
    $row = $result->fetch_assoc()
): ?>

<tr>

<td>
<strong>
<?= htmlspecialchars(
    $row["product_code"]
) ?>
</strong>
</td>

<td>
<?= htmlspecialchars(
    $row["product_name"]
) ?>
</td>

<td>
<?= htmlspecialchars(
    $row["category"]
) ?>
</td>

<td>
<?= htmlspecialchars(
    $row["supplier"]
) ?>
</td>

<td>
<?= number_format(
    $row["unit_price"],
    2
) ?>
RWF
</td>

<td>
<?= $row["quantity"] ?>
</td>

<td>
<strong>
<?= number_format(
    $row["stock_value"],
    2
) ?>
RWF
</strong>
</td>

<td>

<a href="product_edit.php?id=<?= $row["id"] ?>">
Update
</a>

</td>

</tr>

<?php endwhile; ?>

</table>

<?php endif; ?>

</div>

</body>

</html>