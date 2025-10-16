<?php
require_once __DIR__ . '/../model/Post.php';

class PostController {
    private $postModel;

    public function __construct($conn) {
        $this->postModel = new Post($conn);
    }

    public function addPost() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content']);
            $images = [];

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $imgData = file_get_contents($tmp_name);
                    $mimeType = $_FILES['images']['type'][$key];
                    $images[] = [
                        'data' => $imgData,
                        'mime' => $mimeType
                    ];
                }
            }

            $this->postModel->createPost($_SESSION['user_id'], $content, $images);
            header("Location: index.php?page=feed");
            exit;
        }
    }

    public function getAllPosts() {
        return $this->postModel->getAllPosts();
    }
}
?>