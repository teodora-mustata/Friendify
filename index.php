<?php
require_once 'config/db.php';
require_once 'app/controller/UserController.php';

$page = $_GET['page'] ?? 'login';
$controller = new UserController($conn);

switch ($page) {
    case 'register':
        include 'app/view/register.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $controller->register();
        break;
    case 'login':
        include 'app/view/login.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $controller->login();
        break;
    case 'logout':
        $controller->logout();
        break;
    case 'feed':
        include 'app/view/feed.php';
        break;
    default:
        include 'app/view/login.php';
}
?>
