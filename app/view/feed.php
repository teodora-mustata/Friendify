<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}
echo "<h1>Welcome, " . htmlspecialchars($_SESSION['username']) . " ??</h1>";
echo '<a href="index.php?page=logout">Logout</a>';
?>
