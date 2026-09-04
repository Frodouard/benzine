<?php
require 'config.php';

// Destroy session
$_SESSION = [];
session_destroy();

// Redirect to login page
header('Location: login.html');
exit;
?>