<?php
session_start();
require_once __DIR__ . '/../controller/PostController.php';
require_once __DIR__ . '/../../config/db.php';

$conn = new mysqli('localhost', 'root', '', 'friendify_db');
if ($conn->connect_error) die("DB failed: " . $conn->connect_error);

$postController = new PostController($conn);

$user_id = $_SESSION['user_id'] ?? null;
$post_id = $_POST['post_id'] ?? null;

header('Content-Type: application/json');

if (!$user_id || !$post_id) {
    echo json_encode(['status' => false]);
    exit;
}

$status = $postController->postModel->toggleLike($post_id, $user_id);

$likesCount = $postController->getLikesCount($post_id);

echo json_encode([
    'status' => $status,
    'likes' => $likesCount
]);
