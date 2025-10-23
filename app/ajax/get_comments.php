<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../controller/CommentController.php';
require_once __DIR__ . '/../../config/db.php';

$conn = new mysqli('localhost', 'root', '', 'friendify_db');
if ($conn->connect_error) die(json_encode(['status'=>'error','message'=>'DB error']));

$commentController = new CommentController($conn);

$post_id = $_GET['post_id'] ?? null;

if (!$post_id) {
    echo json_encode(['status'=>'error','message'=>'Invalid post id']);
    exit;
}

$comments = $commentController->getComments($post_id);

echo json_encode(['status'=>'success','comments'=>$comments]);