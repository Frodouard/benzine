<?php

session_start();

include "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $registration_number =
        trim($_POST["registration_number"] ?? "");

    $first_name =
        trim($_POST["first_name"] ?? "");

    $last_name =
        trim($_POST["last_name"] ?? "");

    $phone =
        trim($_POST["phone"] ?? "");

    $email =
        trim($_POST["email"] ?? "");

    if (
        empty($registration_number) ||
        empty($first_name) ||
        empty($last_name)
    ) {

        $message = "Registration number, first name and last name are required.";

    } elseif (
        !empty($email) &&
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {

        $message = "Please enter a valid email address.";

    } else {

        try {

            $sql = "INSERT INTO students
                    (registration_number,
                     first_name,
                     last_name,
                     phone,
                     email)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sssss",
                $registration_number,
                $first_name,
                $last_name,
                $phone,
                $email
            );

            $stmt->execute();

            $message = "Student registered successfully.";

        } catch (mysqli_sql_exception $e) {

            error_log("Add student error: " . $e->getMessage());

            if ($e->getCode() == 1062) {

                $message = "Registration failed. This registration number already exists.";

            } else {

                $message = "Registration failed. Please try again.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Register Student</title>

    <link rel="stylesheet"
          href="Style.css">

</head>

<body>

<div class="form-container">

    <h2>Register Student</h2>

    <?php if ($message != ""): ?>

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Registration Number</label>

        <input
            type="text"
            name="registration_number"
            placeholder="2025/SE/001"
            required
        >

        <label>First Name</label>

        <input
            type="text"
            name="first_name"
            required
        >

        <label>Last Name</label>

        <input
            type="text"
            name="last_name"
            required
        >

        <label>Phone</label>

        <input
            type="text"
            name="phone"
        >

        <label>Email</label>

        <input
            type="email"
            name="email"
        >

        <button type="submit">
            Register Student
        </button>

    </form>

</div>

</body>
</html>
