<?php
require 'config.php';

header('Content-Type: application/json');

$users = getUsers();

// Remove password before sending
$safeUsers = array_map(function($user) {
    unset($user['password']);
    return $user;
}, $users);

echo json_encode([
    'success' => true,
    'users'   => $safeUsers
]);
?>