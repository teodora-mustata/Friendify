<?php
require_once __DIR__ . '/../model/User.php';

class UserController {
    private $userModel;

    public function __construct($conn) {
        $this->userModel = new User($conn);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->userModel->register($_POST['username'], $_POST['email'], $_POST['password']);
            header("Location: index.php?page=login");
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->userModel->login($_POST['username'], $_POST['password'])) {
                header("Location: index.php?page=feed");
            } else {
                echo "<p>Username and password don't match. Please try again.'</p>";
            }
        }
    }

    public function logout() {
        $this->userModel->logout();
        header("Location: index.php?page=login");
    }
}
?>
