<?php

session_start();

include "config.php";

$message = "";
$is_error = true;

if (!empty($_SESSION["flash_message"])) {

    $message = $_SESSION["flash_message"];

    $is_error = false;

    unset($_SESSION["flash_message"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $message = "Please enter both email and password.";

    } else {

        try {

            $sql = "SELECT * FROM users WHERE email = ?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param("s", $email);

            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1) {

                $user = $result->fetch_assoc();

                if (password_verify($password, $user["password"])) {

                    session_regenerate_id(true);

                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["full_name"] = $user["full_name"];

                    header("Location: dashboard.php");

                    exit();
                } else {

                    $message = "Incorrect email or password.";
                }
            } else {

                $message = "Incorrect email or password.";
            }

        } catch (mysqli_sql_exception $e) {

            error_log("Login error: " . $e->getMessage());

            $message = "Something went wrong. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Library Login</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<div class="form-container">

    <h2>Librarian Login</h2>

    <?php if ($message != ""): ?>

        <p class="<?php echo $is_error ? "error" : "message"; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

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
            required
        >

        <button type="submit">
            Login
        </button>

    </form>

    <p>
        Don't have an account?
        <a href="register.php">
            Register
        </a>
    </p>

</div>

</body>
</html>
