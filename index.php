<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/controller/UserController.php';
require_once __DIR__ . '/app/controller/PostController.php';

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

    default:
        include 'app/view/login.php';
}
?>
