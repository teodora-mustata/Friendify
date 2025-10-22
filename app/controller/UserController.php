<?php
require_once __DIR__ . '/../model/User.php';

class UserController {
    public $userModel;

    public function __construct($conn) {
        $this->userModel = new User($conn);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            if ($this->userModel->login($username, $password)) {
                echo "success";
            } else {
                echo "Username and password don't match.";
            }
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $password_confirm = trim($_POST['password_confirm']);

            if ($password !== $password_confirm) {
                echo "Passwords do not match.";
                return;
            }

            $success = $this->userModel->register($username, $email, $password);

            if ($success) {
                echo "success";
            } else {
                echo "Username or email already exists.";
            }
        }
    }



    public function logout() {
        $this->userModel->logout();
        header("Location: index.php?page=login");
    }
}
?>
