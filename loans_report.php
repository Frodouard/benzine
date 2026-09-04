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

$loan = null;
$message = "";

try {

    $sql = "SELECT

                loans.*,

                students.registration_number,
                students.first_name,
                students.last_name,
                students.phone,
                students.email,

                books.book_code,
                books.title,
                books.author,
                books.category

            FROM loans

            INNER JOIN students
            ON loans.student_id =
               students.id

            INNER JOIN books
            ON loans.book_id =
               books.id

            WHERE loans.id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $loan = $stmt->get_result()->fetch_assoc();

} catch (mysqli_sql_exception $e) {

    error_log("Load report error: " . $e->getMessage());

    $message = "Could not load this report.";
}

if (!$loan) {

    if ($message != "") {

        die(htmlspecialchars($message));
    }

    die("Loan record not found.");
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Borrowing Report</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<div class="report">

    <h1>
        LIBRARY BORROWING REPORT
    </h1>

    <hr>

    <h2>Borrower Information</h2>

    <p>

        <strong>
            Registration Number:
        </strong>

        <?php

        echo htmlspecialchars(
            $loan["registration_number"]
        );

        ?>

    </p>

    <p>

        <strong>
            Student Name:
        </strong>

        <?php

        echo htmlspecialchars(
            $loan["first_name"] .
            " " .
            $loan["last_name"]
        );

        ?>

    </p>

    <p>

        <strong>Phone:</strong>

        <?php

        echo htmlspecialchars(
            $loan["phone"]
        );

        ?>

    </p>

    <hr>

    <h2>Book Information</h2>

    <p>

        <strong>Loan ID:</strong>

        <?php

        echo htmlspecialchars(
            $loan["loan_id"]
        );

        ?>

    </p>

    <p>

        <strong>Book Code:</strong>

        <?php

        echo htmlspecialchars(
            $loan["book_code"]
        );

        ?>

    </p>

    <p>

        <strong>Title:</strong>

        <?php

        echo htmlspecialchars(
            $loan["title"]
        );

        ?>

    </p>

    <p>

        <strong>Author:</strong>

        <?php

        echo htmlspecialchars(
            $loan["author"]
        );

        ?>

    </p>

    <p>

        <strong>Borrow Date:</strong>

        <?php
        echo $loan["borrow_date"];
        ?>

    </p>

    <p>

        <strong>Expected Return:</strong>

        <?php
        echo $loan["expected_return_date"];
        ?>

    </p>

    <p>

        <strong>Actual Return:</strong>

        <?php

        echo $loan["actual_return_date"]
            ?? "Not returned";

        ?>

    </p>

    <p>

        <strong>Status:</strong>

        <?php

        echo htmlspecialchars(
            $loan["status"]
        );

        ?>

    </p>

    <hr>

    <button onclick="window.print()">
        Print Report
    </button>

    <button
        onclick="window.history.back()"
        class="no-print"
    >
        Back
    </button>

</div>

</body>
</html>
