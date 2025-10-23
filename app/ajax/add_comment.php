<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../controller/CommentController.php';
require_once __DIR__ . '/../../config/db.php';

$conn = new mysqli('localhost', 'root', '', 'friendify_db');
if ($conn->connect_error) die(json_encode(['status'=>'error','message'=>'DB connection failed']));

$commentController = new CommentController($conn);

$user_id = $_SESSION['user_id'] ?? null;
$post_id = $_POST['post_id'] ?? null;
$content = trim($_POST['content'] ?? '');

if($user_id && $post_id && $content){
    $added = $commentController->addComment($post_id, $user_id, $content);
    if($added){
        echo json_encode(['status'=>'success']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Failed to add comment']);
    }
} else {
    echo json_encode(['status'=>'error','message'=>'Invalid request']);
}