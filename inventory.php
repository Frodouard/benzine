<?php

session_start();

require_once "../config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$result = null;
$report_error = "";

try {

    $result = $conn->query(

        "SELECT
            product_code,
            product_name,
            category,
            supplier,
            unit_price,
            quantity,
            (unit_price * quantity)
            AS stock_value

         FROM products

         ORDER BY product_name"

    );

} catch (mysqli_sql_exception $e) {

    $report_error =
        "Could not generate the report.";

}

$total_value = 0;

?>

<!DOCTYPE html>
<html>

<head>

<title>Inventory Report</title>

<link rel="stylesheet"
      href="../css/style.css">

</head>

<body>

<?php include "../nav.php"; ?>

<div class="container">

<div class="report-header">

<h1>
INVENTORY AND STOCK REPORT
</h1>

<p>
Generated:
<?= date("Y-m-d H:i:s") ?>
</p>

</div>

<?php if ($report_error): ?>

<div class="error">
<?= htmlspecialchars($report_error) ?>
</div>

<?php elseif ($result->num_rows == 0): ?>

<p>
No products available for reporting.
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

</tr>

<?php while (
    $row = $result->fetch_assoc()
): ?>

<?php

$stock_value =
    $row["unit_price"] *
    $row["quantity"];

$total_value +=
    $stock_value;

?>

<tr>

<td>
<?= htmlspecialchars(
    $row["product_code"]
) ?>
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
<?= number_format(
    $stock_value,
    2
) ?>
RWF
</td>

</tr>

<?php endwhile; ?>

<tr>

<th colspan="6">
TOTAL INVENTORY VALUE
</th>

<th>
<?= number_format(
    $total_value,
    2
) ?>
RWF
</th>

</tr>

</table>

<button
    onclick="window.print()"
>
Print Inventory Report
</button>

<?php endif; ?>

</div>

</body>

</html>