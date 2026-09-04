<?php

session_start();

require_once "../config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$search =
    trim(
        $_GET["search"] ?? ""
    );

$result = null;
$search_error = "";

if ($search != "") {

    try {

        $stmt = $conn->prepare(

            "SELECT *,
                    (unit_price * quantity)
                    AS stock_value

             FROM products

             WHERE product_code LIKE ?

             ORDER BY product_name"

        );

        $value =
            "%" . $search . "%";

        $stmt->bind_param(
            "s",
            $value
        );

        $stmt->execute();

        $result =
            $stmt->get_result();

    } catch (mysqli_sql_exception $e) {

        $search_error =
            "Search failed. Please try again.";

    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Search Products</title>

<link rel="stylesheet"
      href="../css/style.css">

</head>

<body>

<?php include "../nav.php"; ?>

<div class="container">

<h2>
Search Product by Product Code
</h2>

<form method="GET">

<label>
Product Code
</label>

<input
    type="text"
    name="search"
    placeholder="Example: PROD001"
    value="<?= htmlspecialchars(
        $search
    ) ?>"
    required
>

<button type="submit">
Search
</button>

</form>

<?php if ($search_error): ?>

<div class="error">
<?= htmlspecialchars($search_error) ?>
</div>

<?php elseif ($result): ?>

<table>

<tr>

<th>Product Code</th>

<th>Product Name</th>

<th>Category</th>

<th>Unit Price</th>

<th>Quantity</th>

<th>Stock Value</th>

<th>Action</th>

</tr>

<?php if (
    $result->num_rows > 0
): ?>

<?php while (
    $row = $result->fetch_assoc()
): ?>

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
    $row["stock_value"],
    2
) ?>
RWF
</td>

<td>

<a href="product_edit.php?id=<?= $row["id"] ?>">
Update
</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="7">

No product found.

</td>

</tr>

<?php endif; ?>

</table>

<?php endif; ?>

</div>

</body>

</html>