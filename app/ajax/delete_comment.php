<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../controller/CommentController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$comment_id = $_POST['comment_id'] ?? null;

if (!$comment_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing comment ID']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'friendify_db');
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$commentController = new CommentController($conn);
$response = $commentController->deleteComment($comment_id, $user_id);

echo json_encode($response);