<?php
require_once __DIR__ . '/../model/User.php';

class UserController {
    private $userModel;

    public function __construct($conn) {
        $this->userModel = new User($conn);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $password_confirm = trim($_POST['password_confirm']);

            if ($password !== $password_confirm) {
                echo "<p>Passwords do not match.</p>";
                return;
            }

            $success = $this->userModel->register($username, $email, $password);

            if ($success) {
                header("Location: index.php?page=login");
                exit;
            } else {
                echo "<p>Username or email already exists. Please choose another.</p>";
            }
        }
}

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            if ($this->userModel->login($username, $password)) {
                header("Location: index.php?page=feed");
                exit;
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
