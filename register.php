<?php

session_start();

require_once "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        empty($full_name) ||
        empty($email) ||
        empty($password)
    ) {

        $message = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message =
            "Please enter a valid email address.";

    } elseif (strlen($password) < 8) {

        $message =
            "Password must be at least 8 characters long.";

    } else {

        try {

            $check = $conn->prepare(
                "SELECT id
                 FROM users
                 WHERE email = ?"
            );

            $check->bind_param(
                "s",
                $email
            );

            $check->execute();

            $result = $check->get_result();

            if ($result->num_rows > 0) {

                $message =
                    "Email already exists.";

            } else {

                $hashed_password =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                $stmt = $conn->prepare(
                    "INSERT INTO users
                    (
                        full_name,
                        email,
                        password
                    )
                    VALUES (?, ?, ?)"
                );

                $stmt->bind_param(
                    "sss",
                    $full_name,
                    $email,
                    $hashed_password
                );

                $stmt->execute();

                $message =
                    "Registration successful. You can login now.";

            }

        } catch (mysqli_sql_exception $e) {

            if ($e->getCode() == 1062) {

                $message =
                    "Email already exists.";

            } else {

                $message =
                    "Registration failed. Please try again.";

            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Staff Registration</title>

<link rel="stylesheet"
      href="css/style.css">

</head>

<body>

<div class="auth-container">

<h1>Inventory System</h1>

<h2>Staff Registration</h2>

<?php if ($message): ?>

<div class="message">
<?= htmlspecialchars($message) ?>
</div>

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
    required
>

<button type="submit">
Register
</button>

</form>

<p>
Already registered?

<a href="login.php">
Login
</a>

</p>

</div>

</body>

</html>