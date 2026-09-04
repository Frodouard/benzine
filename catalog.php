<?php
require_once __DIR__ . '/inc/functions.php';
$products = getProducts();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="micky-shop-catalog.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Name', 'Category', 'Price', 'Description', 'Image']);
foreach ($products as $p) {
    fputcsv($out, [$p['id'], $p['name'], $p['category_name'] ?? '', $p['price'], $p['description'] ?? '', $p['image'] ?? '']);
}
fclose($out);
exit;
