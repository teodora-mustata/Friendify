<?php
require_once __DIR__ . '/../model/User.php';

class UserController {
    private $userModel;

    public function __construct($conn) {
        $this->userModel = new User($conn);
    }

public function register() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        if ($password !== $password_confirm) {
            echo "<p>Passwords do not match. Please try again.</p>";
            return;
        }

        $this->userModel->register($username, $email, $password);

        header("Location: index.php?page=login");
    }
}

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->userModel->login($_POST['username'], $_POST['password'])) {
                header("Location: index.php?page=feed");
            } else {
                echo "<p>Username and password don't match. Please try again.</p>";
            }
        }
    }

    public function logout() {
        $this->userModel->logout();
        header("Location: index.php?page=login");
    }
}
?>
