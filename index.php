<?php
require_once 'config.php';

$page = $_GET['page'] ?? 'feed';

switch ($page) {
    case 'login':
        include 'app/view/login.php';
        break;
    case 'register':
        include 'app/view/register.php';
        break;
    default:
        include 'app/view/feed.php';
}
?>
