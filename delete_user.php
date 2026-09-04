<?php
require 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? '';

if (empty($id) || (int)$id !== (int)$_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You can only delete your own account']);
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $_SESSION = [];
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Account deleted']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete account']);
}
