<?php

session_start();

include "config.php";

$message = "";

if (isset($_SESSION["user_id"])) {

    header("Location: dashboard.php");

    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        empty($full_name) ||
        empty($email) ||
        empty($password)
    ) {

        $message = "Please fill in all fields.";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {

            $sql = "INSERT INTO users
                    (full_name, email, password)
                    VALUES (?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param("sss", $full_name, $email, $hashed_password);

            $stmt->execute();

            $_SESSION["flash_message"] = "Registration successful. You can now log in.";

            header("Location: login.php");

            exit();

        } catch (mysqli_sql_exception $e) {

            error_log("Registration error: " . $e->getMessage());

            if ($e->getCode() == 1062) {

                $message = "Registration failed. This email is already registered.";

            } else {

                $message = "Registration failed. Please try again.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Librarian Registration</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<div class="form-container">

    <h2>Librarian Registration</h2>

    <?php if ($message != ""): ?>

        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Full Name</label>

        <input
            type="text"
            name="full_name"
            required
        >

        <label>Email</label>

        <input
            type="email"
            name="email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            minlength="6"
            required
        >

        <button type="submit">
            Register
        </button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">Login</a>
    </p>

</div>

</body>
</html>
