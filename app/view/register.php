<?php
require_once __DIR__ . '/../controller/UserController.php';
require_once __DIR__ . '/../../config/db.php';

$userController = new UserController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    if ($password !== $password_confirm) {
        echo "Passwords do not match.";
        exit;
    }

    $success = $userController->userModel->register($username, $email, $password);
    if ($success) {
        echo "success";
    } else {
        echo "Username or email already exists.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="app/css/auth.css">
</head>
<body>
<div class="auth-container">
    <form id="registerForm" action="register.php" method="POST">
        <h2>Create Account</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirm" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
        <p id="registerError" style="color:red;"></p>
    </form>
    <a href="index.php?page=login">Already have an account? Login here.</a>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('app/view/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if(data.trim() === "success") {
            window.location.href = "index.php?page=login";
        } else {
            document.getElementById('registerError').innerText = data;
        }
    });
});
</script>
</body>
</html>
