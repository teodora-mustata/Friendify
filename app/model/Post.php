<?php
require_once __DIR__ . '/Image.php';

class Post {
    private $conn;
    private $imageModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->imageModel = new Image($conn);
    }

    public function createPost($user_id, $content, $images = []) {
        $stmt = $this->conn->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $user_id, $content);
        $stmt->execute();
        $post_id = $this->conn->insert_id;

        foreach ($images as $img) {
            $image_id = $this->imageModel->saveImage($img['data'], $img['mime']);

            $stmt_link = $this->conn->prepare("INSERT INTO post_images (post_id, image_id) VALUES (?, ?)");
            $stmt_link->bind_param("ii", $post_id, $image_id);
            $stmt_link->execute();
        }

        return true;
    }

    public function getAllPosts() {
        $sql = "
            SELECT 
                posts.id AS post_id,
                posts.content,
                posts.created_at,
                users.username,
                (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS likes,
                images.image,
                images.mime_type
            FROM posts
            JOIN users ON posts.user_id = users.id
            LEFT JOIN post_images ON posts.id = post_images.post_id
            LEFT JOIN images ON post_images.image_id = images.id
            ORDER BY posts.created_at DESC
        ";


        $result = $this->conn->query($sql);
        $posts = [];

        while ($row = $result->fetch_assoc()) {
            $id = $row['post_id'];
            if (!isset($posts[$id])) {
                $posts[$id] = [
                    'post_id'   => $id,
                    'username' => $row['username'],
                    'content' => $row['content'],
                    'created_at' => $row['created_at'],
                    'likes' => $row['likes'],
                    'images' => []
                ];
            }

            if ($row['image']) {
                $posts[$id]['images'][] = [
                    'data' => $row['image'],
                    'mime' => $row['mime_type']
                ];
            }
        }

        return $posts;
    }

    public function hasUserLiked($post_id, $user_id) {
    $stmt = $this->conn->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

    public function toggleLike($post_id, $user_id) {
        if ($this->hasUserLiked($post_id, $user_id)) {
            $stmt = $this->conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $post_id, $user_id);
            $stmt->execute();
            return 'unliked';
        } else {
            $stmt = $this->conn->prepare("INSERT INTO likes (post_id, user_id, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $post_id, $user_id);
            $stmt->execute();
            return 'liked';
        }
}

    public function getLikesCount($post_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

}
?>
