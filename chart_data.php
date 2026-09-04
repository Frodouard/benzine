<?php
require_once __DIR__ . '/inc/functions.php';
$pdo = getDb();
$categories = $pdo->query(
    "SELECT c.name AS name, COUNT(p.id) AS count
     FROM categories c
     LEFT JOIN products p ON p.category_id = c.id
     GROUP BY c.id, c.name
     ORDER BY count DESC, c.name ASC"
)->fetchAll();
$total = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$categoryCount = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
header('Content-Type: application/json');
echo json_encode([
    'total' => (int)$total,
    'categories' => $categories,
    'categoryCount' => (int)$categoryCount,
]);
