<?php
require_once __DIR__ . '/inc/functions.php';
session_destroy();
header('Location: index.php');
exit;
