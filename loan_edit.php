<?php

session_start();

include "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit();
}

$id = intval($_GET["id"] ?? 0);

if ($id < 1) {

    header("Location: loans-list.php");

    exit();
}

$message = "";
$loan = null;

try {

    $sql = "SELECT *
            FROM loans
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $loan = $stmt->get_result()->fetch_assoc();

} catch (mysqli_sql_exception $e) {

    error_log("Load loan error: " . $e->getMessage());

    $message = "Could not load this loan record.";
}

if (!$loan) {

    die("Loan record not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($loan["status"] === "Returned") {

        $message = "This book has already been marked as returned.";

    } else {

        $actual_return_date = $_POST["actual_return_date"] ?? "";

        if (empty($actual_return_date)) {

            $message = "Please provide the actual return date.";

        } else {

            $conn->begin_transaction();

            try {

                $status = "Returned";

                $sql = "UPDATE loans
                        SET actual_return_date = ?,
                            status = ?
                        WHERE id = ?";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "ssi",
                    $actual_return_date,
                    $status,
                    $id
                );

                $stmt->execute();

                if ($conn->affected_rows !== 1) {

                    throw new Exception("Could not update the loan record.");
                }

                $sql = "UPDATE books
                        SET available_quantity =
                            available_quantity + 1
                        WHERE id = ?";

                $book_stmt = $conn->prepare($sql);

                $book_stmt->bind_param("i", $loan["book_id"]);

                $book_stmt->execute();

                $conn->commit();

                header("Location: loans-list.php");

                exit();

            } catch (Exception $e) {

                $conn->rollback();

                error_log("Return book error: " . $e->getMessage());

                $message = "Failed to update return. Please try again.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Update Return</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<div class="form-container">

    <h2>Update Book Return</h2>

    <?php if ($message != ""): ?>

        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <p>
        Loan ID:
        <?php
        echo htmlspecialchars(
            $loan["loan_id"]
        );
        ?>
    </p>

    <p>
        Expected Return Date:
        <?php
        echo htmlspecialchars(
            $loan["expected_return_date"]
        );
        ?>
    </p>

    <p>
        Status:
        <?php
        echo htmlspecialchars(
            $loan["status"]
        );
        ?>
    </p>

    <?php if ($loan["status"] !== "Returned"): ?>

        <form method="POST">

            <label>
                Actual Return Date
            </label>

            <input
                type="date"
                name="actual_return_date"
                required
            >

            <button type="submit">
                Mark as Returned
            </button>

        </form>

    <?php else: ?>

        <p>
            <a href="loans-list.php">
                Back to Loans
            </a>
        </p>

    <?php endif; ?>

</div>

</body>
</html>
