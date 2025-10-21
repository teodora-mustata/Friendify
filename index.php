<?php
require_once __DIR__ . '/app/model/Image.php';
require_once __DIR__ . '/app/model/Post.php';
require_once __DIR__ . '/app/controller/PostController.php';
require_once __DIR__ . '/app/controller/UserController.php';


$conn = new mysqli('localhost', 'root', '', 'friendify_db');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$controller = new UserController($conn);

$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            include 'app/view/register.php';
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            include 'app/view/login.php';
        }
        break;

    case 'logout':
        $controller->logout();
        break;

    case 'feed':
        include 'app/view/feed.php';
        break;

    case 'add_post':
        $postController = new PostController($conn);
        $postController->addPost();
        break;

    case 'like_post':
        $post_id = $_POST['post_id'] ?? null;
        if ($post_id) {
            $conn->query("UPDATE posts SET likes = likes + 1 WHERE id = $post_id");
        }
        header("Location: index.php?page=feed");
        exit;

    case 'toggle_like':
        $postController = new PostController($conn);
        $postController->toggleLike();
        exit;


    default:
        include 'app/view/login.php';
}
?>
