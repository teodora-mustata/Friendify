<?php
require_once __DIR__ . '/../controller/UserController.php';
require_once __DIR__ . '/../../config/db.php';

$userController = new UserController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo "Invalid CSRF token.";
        exit;
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($userController->userModel->login($username, $password)) {
        echo "success";
    } else {
        echo "Username and password don't match.";
    }
    exit;
}


session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="app/css/auth.css">
</head>
<body>
<div class="auth-container">
    <form id="loginForm" action="login.php" method="POST">
        <h2>Authenticate</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
