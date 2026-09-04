<?php

session_start();

include "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit();
}

$result = null;
$message = "";

try {

    $sql = "SELECT
                loans.*,

                students.registration_number,
                students.first_name,
                students.last_name,

                books.book_code,
                books.title

            FROM loans

            INNER JOIN students
            ON loans.student_id = students.id

            INNER JOIN books
            ON loans.book_id = books.id

            ORDER BY loans.id DESC";

    $result = $conn->query($sql);

} catch (mysqli_sql_exception $e) {

    error_log("List loans error: " . $e->getMessage());

    $message = "Could not load borrowing records. Please try again later.";
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Borrowed Books</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<div class="table-container">

    <h2>Borrowing Records</h2>

    <?php if ($message != ""): ?>

        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <a href="loans.php" class="btn">
        Borrow Book
    </a>

    <a href="loan_search.php" class="btn">
        Search
    </a>

    <table>

        <tr>

            <th>Loan ID</th>

            <th>Registration Number</th>

            <th>Student</th>

            <th>Book</th>

            <th>Borrow Date</th>

            <th>Expected Return</th>

            <th>Actual Return</th>

            <th>Status</th>

            <th>Actions</th>

        </tr>

        <?php if ($result !== null && $result->num_rows > 0): ?>

            <?php while (
                $row =
                $result->fetch_assoc()
            ): ?>

            <tr>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $row["loan_id"]
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $row["registration_number"]
                    );
                    ?>
                </td>

                <td>

                    <?php

                    echo htmlspecialchars(
                        $row["first_name"] .
                        " " .
                        $row["last_name"]
                    );

                    ?>

                </td>

                <td>

                    <?php

                    echo htmlspecialchars(
                        $row["book_code"] .
                        " - " .
                        $row["title"]
                    );

                    ?>

                </td>

                <td>
                    <?php
                    echo $row["borrow_date"];
                    ?>
                </td>

                <td>
                    <?php
                    echo $row["expected_return_date"];
                    ?>
                </td>

                <td>
                    <?php
                    echo $row["actual_return_date"]
                        ?? "Not returned";
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $row["status"]
                    );
                    ?>
                </td>

                <td>

                    <a href="loan_edit.php?id=<?php
                        echo $row["id"];
                    ?>">
                        Update
                    </a>

                    <a href="loans_report.php?id=<?php
                        echo $row["id"];
                    ?>">
                        Report
                    </a>

                </td>

            </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="9">
                    No borrowing records found.
                </td>
            </tr>

        <?php endif; ?>

    </table>

</div>

</body>
</html>
