<?php

session_start();

include "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $book_code = trim($_POST["book_code"] ?? "");
    $title = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $quantity = intval($_POST["quantity"] ?? 0);

    if (
        empty($book_code) ||
        empty($title) ||
        empty($author)
    ) {

        $message = "Book code, title and author are required.";

    } elseif ($quantity < 1) {

        $message = "Quantity must be at least 1.";

    } else {

        try {

            $sql = "INSERT INTO books
                    (book_code, title, author,
                     category, quantity,
                     available_quantity)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssssii",
                $book_code,
                $title,
                $author,
                $category,
                $quantity,
                $quantity
            );

            $stmt->execute();

            $message = "Book added successfully.";

        } catch (mysqli_sql_exception $e) {

            error_log("Add book error: " . $e->getMessage());

            if ($e->getCode() == 1062) {

                $message = "Book code already exists.";

            } else {

                $message = "Failed to add book. Please try again.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Add Book</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<div class="form-container">

    <h2>Add Book</h2>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <label>Book Code</label>

        <input
            type="text"
            name="book_code"
            placeholder="B005"
            required
        >

        <label>Book Title</label>

        <input
            type="text"
            name="title"
            required
        >

        <label>Author</label>

        <input
            type="text"
            name="author"
            required
        >

        <label>Category</label>

        <input
            type="text"
            name="category"
        >

        <label>Quantity</label>

        <input
            type="number"
            name="quantity"
            min="1"
            required
        >

        <button type="submit">
            Add Book
        </button>

    </form>

</div>

</body>
</html>
