<?php

declare(strict_types=1);

session_start();

require_once "../config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$id =
    intval(
        $_GET["id"] ?? 0
    );

if ($id <= 0) {

    die("Invalid product ID.");

}

$load_error = "";

try {

    $stmt = $conn->prepare(
        "SELECT *
         FROM products
         WHERE id = ?"
    );

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    $product =
        $stmt
        ->get_result()
        ->fetch_assoc();

} catch (mysqli_sql_exception $e) {

    $product = null;

    $load_error =
        "Could not load the product. Please try again.";

}

if ($load_error) {

    ?>

    <!DOCTYPE html>
    <html>
    <head>
    <title>Update Product</title>
    <link rel="stylesheet"
          href="../css/style.css">
    </head>
    <body>

    <?php include "../nav.php"; ?>

    <div class="container">

    <div class="error">
    <?= htmlspecialchars($load_error) ?>
    </div>

    <p>
    <a href="product_list.php">
    Back to products
    </a>
    </p>

    </div>

    </body>
    </html>

    <?php

    exit;
}

if (!$product) {

    die("Product not found.");

}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_name =
        trim(
            $_POST["product_name"] ?? ""
        );

    $category =
        trim(
            $_POST["category"] ?? ""
        );

    $supplier =
        trim(
            $_POST["supplier"] ?? ""
        );

    $unit_price =
        floatval(
            $_POST["unit_price"] ?? 0
        );

    $new_quantity =
        intval(
            $_POST["quantity"] ?? 0
        );

    $old_quantity =
        intval(
            $product["quantity"]
        );

    if (
        empty($product_name) ||
        empty($category) ||
        $unit_price < 0 ||
        $new_quantity < 0
    ) {

        $message =
            "Name, category are required. Price and quantity cannot be negative.";

    } else {

        try {

            $update = $conn->prepare(

                "UPDATE products

                 SET product_name = ?,
                     category = ?,
                     supplier = ?,
                     unit_price = ?,
                     quantity = ?

                 WHERE id = ?"

            );

            $update->bind_param(
                "sssdii",
                $product_name,
                $category,
                $supplier,
                $unit_price,
                $new_quantity,
                $id
            );

            $update->execute();

            $difference =
                $new_quantity -
                $old_quantity;

            if ($difference != 0) {

                $transaction_type =
                    $difference > 0
                        ? "IN"
                        : "OUT";

                $transaction_quantity =
                    abs($difference);

                $transaction =
                    $conn->prepare(

                        "INSERT INTO
                         stock_transactions
                        (
                            product_id,
                            transaction_type,
                            quantity
                        )
                        VALUES (?, ?, ?)"

                    );

                $transaction->bind_param(
                    "isi",
                    $id,
                    $transaction_type,
                    $transaction_quantity
                );

                $transaction->execute();
            }

            header(
                "Location: product_list.php"
            );

            exit;

        } catch (mysqli_sql_exception $e) {

            $message =
                "Update failed. Please try again.";

        }
    }
}

?>
<!DOCTYPE html>
<html>

<head>

<title>Update Product</title>

<link rel="stylesheet"
      href="../css/style.css">

</head>

<body>

<?php include "../nav.php"; ?>

<div class="container">

<h2>Update Product</h2>

<?php if ($message): ?>

<div class="error">
<?= htmlspecialchars($message) ?>
</div>

<?php endif; ?>

<form method="POST">

<label>Product Code</label>

<input
    type="text"
    value="<?= htmlspecialchars(
        $product["product_code"]
    ) ?>"
    disabled
>

<label>Product Name</label>

<input
    type="text"
    name="product_name"
    value="<?= htmlspecialchars(
        $product["product_name"]
    ) ?>"
    required
>

<label>Category</label>

<input
    type="text"
    name="category"
    value="<?= htmlspecialchars(
        $product["category"]
    ) ?>"
    required
>

<label>Supplier</label>

<input
    type="text"
    name="supplier"
    value="<?= htmlspecialchars(
        $product["supplier"] ?? ""
    ) ?>"
>

<label>Unit Price</label>

<input
    type="number"
    name="unit_price"
    value="<?= $product["unit_price"] ?>"
    min="0"
    step="0.01"
    required
>

<label>Quantity</label>

<input
    type="number"
    name="quantity"
    value="<?= $product["quantity"] ?>"
    min="0"
    required
>

<button type="submit">
Update Product
</button>

</form>

</div>

</body>

</html>
