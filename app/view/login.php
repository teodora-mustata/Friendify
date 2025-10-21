<link rel="stylesheet" href="app/css/auth.css">
<div class="auth-container">
    <form action="index.php?page=login" method="POST">
        <h2>Authenticate</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <a href="index.php?page=register">Don't have an account? Create one here.</a>
</div>
