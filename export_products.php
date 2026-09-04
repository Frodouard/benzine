<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$pdo = getDb();
$products = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC')->fetchAll();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="products.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Name', 'Category', 'Price', 'Description', 'Image', 'Created']);
foreach ($products as $p) {
    fputcsv($out, [$p['id'], $p['name'], $p['category_name'] ?? '', $p['price'], $p['description'] ?? '', $p['image'] ?? '', $p['created_at']]);
}
fclose($out);
exit;
