<?php

session_start();

require_once "../config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_code =
        strtoupper(
            trim(
                $_POST["product_code"] ?? ""
            )
        );

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

    $quantity =
        intval(
            $_POST["quantity"] ?? 0
        );

    if (
        $product_code == "" ||
        $product_name == "" ||
        $category == "" ||
        $unit_price < 0 ||
        $quantity < 0
    ) {

        $message =
            "Product code, name and category are required. "
            . "Price and quantity cannot be negative.";

    } else {

        try {

            $conn->begin_transaction();

            $stmt = $conn->prepare(

                "INSERT INTO products
                (
                    product_code,
                    product_name,
                    category,
                    supplier,
                    unit_price,
                    quantity
                )

                VALUES (?, ?, ?, ?, ?, ?)"

            );

            $stmt->bind_param(
                "ssssdi",
                $product_code,
                $product_name,
                $category,
                $supplier,
                $unit_price,
                $quantity
            );

            $stmt->execute();

            $product_id = $conn->insert_id;

            if ($quantity > 0) {

                $transaction =
                    $conn->prepare(

                        "INSERT INTO
                         stock_transactions
                        (
                            product_id,
                            transaction_type,
                            quantity
                        )
                        VALUES (?, 'IN', ?)"

                    );

                $transaction->bind_param(
                    "ii",
                    $product_id,
                    $quantity
                );

                $transaction->execute();
            }

            $conn->commit();

            $message =
                "Product registered successfully.";

        } catch (mysqli_sql_exception $e) {

            // Attempt a safe rollback if a transaction is active. Some PHP/MySQLi builds
            // may not expose in_transaction(), so check before calling it.
            try {
                $inTx = (is_object($conn) && method_exists($conn, 'in_transaction')) ? $conn->in_transaction() : false;
            } catch (Throwable $txEx) {
                $inTx = false;
            }

            if ($inTx) {
                // Suppress any warnings while rolling back to avoid masking the original exception.
                @ $conn->rollback();
            }

            if ($e->getCode() == 1062) {

                $message =
                    "Error. Product code already exists.";

            } else {

                $message =
                    "Error saving product. Please try again.";

            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Add Product</title>

<link rel="stylesheet"
      href="../css/style.css">

<script src="../js/script.js"
        defer></script>

</head>

<body>

<?php include "../nav.php"; ?>

<div class="container">

<h2>Register Product</h2>

<?php if ($message): ?>

<div class="message">
<?= htmlspecialchars($message) ?>
</div>

<?php endif; ?>

<form method="POST">

<label>Product Code</label>

<input
    type="text"
    name="product_code"
    placeholder="PROD001"
    required
>

<label>Product Name</label>

<input
    type="text"
    name="product_name"
    placeholder="Laptop"
    required
>

<label>Category</label>

<input
    type="text"
    name="category"
    placeholder="Electronics"
    required
>

<label>Supplier</label>

<input
    type="text"
    name="supplier"
    placeholder="ABC Suppliers"
>

<label>Unit Price (RWF)</label>

<input
    type="number"
    name="unit_price"
    min="0"
    step="0.01"
    required
>

<label>Quantity</label>

<input
    type="number"
    name="quantity"
    min="0"
    required
>

<button type="submit">
Save Product
</button>

</form>

</div>

</body>

</html>