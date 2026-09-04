<?php
require_once __DIR__ . '/../inc/functions.php';
requireAdmin();
$pdo = getDb();
$orders = $pdo->query(
    "SELECT o.*, u.name AS customer_name,
        GROUP_CONCAT(CONCAT(oi.quantity, ' x ', p.name) ORDER BY oi.id SEPARATOR '; ') AS items
     FROM orders o
     JOIN users u ON o.customer_id = u.id
     LEFT JOIN order_items oi ON oi.order_id = o.id
     LEFT JOIN products p ON p.id = oi.product_id
     GROUP BY o.id
     ORDER BY o.created_at DESC"
)->fetchAll();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="orders.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Customer', 'Products', 'Total', 'Address', 'Phone', 'Email', 'Status', 'Date']);
foreach ($orders as $o) {
    fputcsv($out, [$o['id'], $o['customer_name'], $o['items'], $o['total_amount'], $o['delivery_address'], $o['contact_phone'], $o['contact_email'], $o['status'], $o['created_at']]);
}
fclose($out);
exit;
