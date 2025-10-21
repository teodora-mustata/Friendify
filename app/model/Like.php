<?php
class Like {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function hasUserLiked($post_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function addLike($post_id, $user_id) {
        if (!$this->hasUserLiked($post_id, $user_id)) {
            $stmt = $this->conn->prepare("INSERT INTO likes (post_id, user_id, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $post_id, $user_id);
            $stmt->execute();
        }
    }

    public function removeLike($post_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
    }

    public function countLikes($post_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }
}
?>
