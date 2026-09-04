<?php

session_start();

include "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit();
}

$result = null;
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $registration_number =
        trim($_POST["registration_number"] ?? "");

    if (empty($registration_number)) {

        $message = "Please enter a registration number.";

    } else {

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
                    ON loans.student_id =
                       students.id

                    INNER JOIN books
                    ON loans.book_id =
                       books.id

                    WHERE students.registration_number = ?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param("s", $registration_number);

            $stmt->execute();

            $result = $stmt->get_result();

        } catch (mysqli_sql_exception $e) {

            error_log("Search loans error: " . $e->getMessage());

            $message = "Search failed. Please try again later.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Search Borrowing Record</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<div class="form-container">

    <h2>Search Borrowing Record</h2>

    <?php if ($message != ""): ?>

        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>
            Student Registration Number
        </label>

        <input
            type="text"
            name="registration_number"
            placeholder="2025/SE/001"
            required
        >

        <button type="submit">
            Search
        </button>

    </form>

    <?php if ($result !== null): ?>

        <?php if ($result->num_rows > 0): ?>

            <?php while (
                $row =
                $result->fetch_assoc()
            ): ?>

                <div class="patient-result">

                    <h3>

                        <?php

                        echo htmlspecialchars(
                            $row["first_name"] .
                            " " .
                            $row["last_name"]
                        );

                        ?>

                    </h3>

                    <p>
                        Registration Number:
                        <?php
                        echo htmlspecialchars(
                            $row["registration_number"]
                        );
                        ?>
                    </p>

                    <p>
                        Book:
                        <?php

                        echo htmlspecialchars(
                            $row["book_code"] .
                            " - " .
                            $row["title"]
                        );

                        ?>
                    </p>

                    <p>
                        Borrow Date:
                        <?php
                        echo $row["borrow_date"];
                        ?>
                    </p>

                    <p>
                        Expected Return:
                        <?php
                        echo $row["expected_return_date"];
                        ?>
                    </p>

                    <p>
                        Status:
                        <?php
                        echo htmlspecialchars(
                            $row["status"]
                        );
                        ?>
                    </p>

                    <a href="loan_edit.php?id=<?php
                        echo $row["id"];
                    ?>">
                        Update Return
                    </a>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <p class="error">
                No borrowing records found.
            </p>

        <?php endif; ?>

    <?php endif; ?>

</div>

</body>
</html>
