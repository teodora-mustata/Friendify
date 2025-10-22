<?php
require_once __DIR__ . '/../controller/UserController.php';
require_once __DIR__ . '/../../config/db.php';

$userController = new UserController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($userController->userModel->login($username, $password)) {
        echo "success";
    } else {
        echo "Username and password don't match.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="app/css/auth.css">
</head>
<body>
<div class="auth-container">
    <form id="loginForm" action="login.php" method="POST">
        <h2>Authenticate</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p id="loginError" style="color:red;"></p>
    </form>
    <a href="index.php?page=register">Don't have an account? Create one here.</a>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('app/view/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if(data.trim() === "success") {
            window.location.href = "index.php?page=feed";
        } else {
            document.getElementById('loginError').innerText = data;
        }
    });
});
</script>
</body>
</html>
