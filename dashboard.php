<?php

session_start();

include "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit();
}

$students = 0;
$books = 0;
$borrowed = 0;
$message = "";

try {

    $students = $conn->query(
        "SELECT COUNT(*) AS total
         FROM students"
    )->fetch_assoc()["total"];

    $books = $conn->query(
        "SELECT COUNT(*) AS total
         FROM books"
    )->fetch_assoc()["total"];

    $borrowed = $conn->query(
        "SELECT COUNT(*) AS total
         FROM loans
         WHERE status = 'Borrowed'"
    )->fetch_assoc()["total"];

} catch (mysqli_sql_exception $e) {

    error_log("Dashboard error: " . $e->getMessage());

    $message = "Could not load dashboard statistics. Please make sure the database is set up correctly (import database.sql).";
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Library Dashboard</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<nav>

    <h2>Library System</h2>

    <a href="dashboard.php">
        Dashboard
    </a>

    <a href="rstudents_add.php">
        Students
    </a>

    <a href="books.php">
        Books
    </a>

    <a href="loans.php">
        Borrow Book
    </a>

    <a href="loans-list.php">
        Loans
    </a>

    <a href="logout.php">
        Logout
    </a>

</nav>

<div class="dashboard">

    <h1>
        Welcome,
        <?php
        echo htmlspecialchars(
            $_SESSION["full_name"]
        );
        ?>
    </h1>

    <?php if ($message != ""): ?>

        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <div class="cards">

        <div class="card">

            <h3>Students</h3>

            <p>
                <?php echo $students; ?>
            </p>

        </div>

        <div class="card">

            <h3>Books</h3>

            <p>
                <?php echo $books; ?>
            </p>

        </div>

        <div class="card">

            <h3>Borrowed Books</h3>

            <p>
                <?php echo $borrowed; ?>
            </p>

        </div>

    </div>

</div>

</body>
</html>
