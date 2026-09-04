<?php
require 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$fullname  = trim($_POST['fullname'] ?? '');
$username  = trim($_POST['username'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';
$confirm   = $_POST['confirm'] ?? '';
$phone     = trim($_POST['phone'] ?? '');
$gender    = trim($_POST['gender'] ?? '');
$terms     = isset($_POST['terms']);

$errors = [];

if (empty($fullname))               $errors[] = 'Full name is required';
if (strlen($username) < 3)          $errors[] = 'Username must be at least 3 characters';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
if (strlen($password) < 6)          $errors[] = 'Password must be at least 6 characters';
if ($password !== $confirm)         $errors[] = 'Passwords do not match';
if (!$terms)                        $errors[] = 'You must accept the Terms';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
    exit;
}

$pdo = getDB();

$stmt = $pdo->prepare("SELECT id, email, username FROM users WHERE email = ? OR username = ?");
$stmt->execute([$email, $username]);
$existing = $stmt->fetch();

if ($existing) {
    $field = strtolower($existing['email']) === strtolower($email) ? 'email' : 'username';
    $msg = $field === 'email' ? 'Email already registered' : 'Username already taken';
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, password, phone, gender, registered_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->execute([
    htmlspecialchars($fullname),
    htmlspecialchars($username),
    htmlspecialchars($email),
    password_hash($password, PASSWORD_DEFAULT),
    htmlspecialchars($phone),
    htmlspecialchars($gender),
]);

$userId = $pdo->lastInsertId();

$_SESSION['user_id']   = $userId;
$_SESSION['username']  = $username;
$_SESSION['fullname']  = $fullname;

echo json_encode([
    'success'  => true,
    'message'  => 'Registration successful! Redirecting to profile...',
    'redirect' => 'profile.php'
]);
