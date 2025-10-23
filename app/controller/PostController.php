<?php
require_once __DIR__ . '/../model/Post.php';
require_once __DIR__ . '/../model/Like.php';

class PostController {
    private $postModel;
    private $conn;

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
        $user_id = $_SESSION['user_id'] ?? null;
        $post_id = $_POST['post_id'] ?? null;

        header('Content-Type: application/json');

        if (!$user_id || !$post_id) {
            echo json_encode(['status' => false]);
            return;
        }

        $status = $this->postModel->toggleLike($post_id, $user_id);

        $likesCount = $this->postModel->getLikesCount($post_id);

        echo json_encode([
            'status' => $status,
            'likes' => $likesCount
        ]);
    }

    public function getLikesCount($post_id) {
    return $this->postModel->getLikesCount($post_id);
    }

    public function hasUserLiked($post_id, $user_id) {
        return $this->postModel->hasUserLiked($post_id, $user_id);
    }

    public function getPostById($post_id) {
        return $this->postModel->getPostById($post_id);
    }

    public function deletePost($post_id) {
        return $this->postModel->deletePost($post_id);
    }

    public function getCommentsCount($post_id){
        $stmt = $this->conn->prepare("SELECT COUNT(*) as c FROM comments WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res['c'] ?? 0;
    }
}
?>