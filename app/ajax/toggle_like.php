<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../controller/PostController.php';
require_once __DIR__ . '/../../config/db.php';

$conn = new mysqli('localhost', 'root', '', 'friendify_db');
if ($conn->connect_error) die("DB failed: " . $conn->connect_error);

$postController = new PostController($conn);

header('Content-Type: application/json');
ob_clean();

$postController->toggleLike(); 
?>