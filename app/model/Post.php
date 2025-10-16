<?php
class Post {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createPost($user_id, $content, $images = []) {
        $stmt = $this->conn->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $user_id, $content);
        $stmt->execute();
        $post_id = $this->conn->insert_id;

        foreach ($images as $img) {
            $hash = hash('sha256', $img['data']);

            $stmt_check = $this->conn->prepare("SELECT id FROM images WHERE hash = ?");
            $stmt_check->bind_param("s", $hash);
            $stmt_check->execute();
            $result = $stmt_check->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $image_id = $row['id'];
            } else {
                $stmt_img = $this->conn->prepare("INSERT INTO images (image, mime_type, hash) VALUES (?, ?, ?)");
                $empty = "";
                $mimeType = $img['mime'];
                $stmt_img->bind_param("bss", $empty, $mimeType, $hash);
                $stmt_img->send_long_data(0, $img['data']);
                $stmt_img->execute();
                $image_id = $this->conn->insert_id;
            }

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
                    'username' => $row['username'],
                    'content' => $row['content'],
                    'created_at' => $row['created_at'],
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
}
?>