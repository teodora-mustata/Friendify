<?php
require_once __DIR__ . '/../model/Post.php';
require_once __DIR__ . '/../model/Like.php';

class PostController {
    private $postModel;

    public function __construct($conn) {
        $this->conn = $conn;      
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

    public function toggleLike() {
        session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        $post_id = $_POST['post_id'] ?? null;

        header('Content-Type: application/json');

        if (!$user_id || !$post_id) {
            echo json_encode(['status' => false]);
            return;
        }

        $stmt = $this->conn->prepare("SELECT id FROM likes WHERE user_id=? AND post_id=?");
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt_del = $this->conn->prepare("DELETE FROM likes WHERE user_id=? AND post_id=?");
            $stmt_del->bind_param("ii", $user_id, $post_id);
            $stmt_del->execute();
            $status = 'unliked';
        } else {
            $stmt_ins = $this->conn->prepare("INSERT INTO likes (user_id, post_id, created_at) VALUES (?, ?, NOW())");
            $stmt_ins->bind_param("ii", $user_id, $post_id);
            $stmt_ins->execute();
            $status = 'liked';
        }

        $likesCount = $this->getLikesCount($post_id);

        echo json_encode(['status' => $status, 'likes' => $likesCount]);
    }


    public function getLikesCount($post_id) {
    return $this->postModel->getLikesCount($post_id);
}

    public function hasUserLiked($post_id, $user_id) {
        return $this->postModel->hasUserLiked($post_id, $user_id);
    }


}
?>