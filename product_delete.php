<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$id = intval($_GET['id'] ?? 0);
if ($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: products.php');
exit;
