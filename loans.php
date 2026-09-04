<?php

session_start();

include "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit();
}

$message = "";

$students = null;
$books = null;

try {

    $students = $conn->query(
        "SELECT id,
                registration_number,
                first_name,
                last_name
         FROM students
         ORDER BY first_name"
    );

    $books = $conn->query(
        "SELECT id,
                book_code,
                title,
                available_quantity
         FROM books
         WHERE available_quantity > 0
         ORDER BY title"
    );

} catch (mysqli_sql_exception $e) {

    error_log("Load students/books error: " . $e->getMessage());

    $message = "Could not load students or books. Please try again later.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $loan_id = trim($_POST["loan_id"] ?? "");
    $student_id = intval($_POST["student_id"] ?? 0);
    $book_id = intval($_POST["book_id"] ?? 0);
    $borrow_date = $_POST["borrow_date"] ?? "";
    $expected_return_date = $_POST["expected_return_date"] ?? "";

    if (empty($loan_id)) {

        $message = "Loan ID is required.";

    } elseif ($student_id < 1 || $book_id < 1) {

        $message = "Please select both a student and a book.";

    } elseif (empty($borrow_date) || empty($expected_return_date)) {

        $message = "Please provide both borrow and expected return dates.";

    } elseif (strtotime($expected_return_date) < strtotime($borrow_date)) {

        $message = "Expected return date cannot be before the borrow date.";

    } else {

        $conn->begin_transaction();

        try {

            $sql = "INSERT INTO loans
                    (loan_id,
                     student_id,
                     book_id,
                     borrow_date,
                     expected_return_date)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "siiss",
                $loan_id,
                $student_id,
                $book_id,
                $borrow_date,
                $expected_return_date
            );

            $stmt->execute();

            if ($conn->affected_rows !== 1) {

                throw new Exception("Could not create the loan record.");
            }

            $sql = "UPDATE books
                    SET available_quantity =
                        available_quantity - 1
                    WHERE id = ?
                    AND available_quantity > 0";

            $update = $conn->prepare($sql);

            $update->bind_param("i", $book_id);

            $update->execute();

            if ($update->affected_rows !== 1) {

                throw new Exception("This book is no longer available.");
            }

            $conn->commit();

            $message = "Book borrowed successfully.";

        } catch (Exception $e) {

            $conn->rollback();

            error_log("Borrow error: " . $e->getMessage());

            if (strpos($e->getMessage(), "Duplicate") !== false) {

                $message = "This Loan ID already exists.";

            } else {

                $message = "Failed to record borrowing: " . $e->getMessage();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Borrow Book</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<div class="form-container">

    <h2>Record Book Loan</h2>

    <?php if ($message != ""): ?>

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Loan ID</label>

        <input
            type="text"
            name="loan_id"
            placeholder="L001"
            required
        >

        <label>Student</label>

        <select name="student_id" required>

            <option value="">
                Select Student
            </option>

            <?php if ($students !== null): ?>

                <?php while (
                    $student =
                    $students->fetch_assoc()
                ): ?>

                    <option
                        value="<?php
                        echo $student["id"];
                        ?>"
                    >

                        <?php

                        echo htmlspecialchars(
                            $student["registration_number"] .
                            " - " .
                            $student["first_name"] .
                            " " .
                            $student["last_name"]
                        );

                        ?>

                    </option>

                <?php endwhile; ?>

            <?php endif; ?>

        </select>

        <label>Book</label>

        <select name="book_id" required>

            <option value="">
                Select Book
            </option>

            <?php if ($books !== null): ?>

                <?php while (
                    $book =
                    $books->fetch_assoc()
                ): ?>

                    <option
                        value="<?php
                        echo $book["id"];
                        ?>"
                    >

                        <?php

                        echo htmlspecialchars(
                            $book["book_code"] .
                            " - " .
                            $book["title"] .
                            " (" .
                            $book["available_quantity"] .
                            " available)"
                        );

                        ?>

                    </option>

                <?php endwhile; ?>

            <?php endif; ?>

        </select>

        <label>Borrow Date</label>

        <input
            type="date"
            name="borrow_date"
            required
        >

        <label>Expected Return Date</label>

        <input
            type="date"
            name="expected_return_date"
            required
        >

        <button type="submit">
            Record Loan
        </button>

    </form>

</div>

</body>
</html>
