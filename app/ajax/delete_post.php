<?php
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controller/PostController.php';
require_once __DIR__ . '/../../config/db.php';

$conn = new mysqli('localhost', 'root', '', 'friendify_db');
if ($conn->connect_error) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit;
}

$postController = new PostController($conn);

$user_id = $_SESSION['user_id'] ?? null;
$post_id = $_POST['post_id'] ?? null;
if (!$user_id || !$post_id) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}
$post = $postController->getPostById($post_id);
if (!$post || $post['user_id'] != $user_id) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit;
}
$postController->deletePost($post_id);
ob_end_clean();
echo json_encode(['status' => 'success']);
exit;
