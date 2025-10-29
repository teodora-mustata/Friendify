<?php
require_once 'Model.php';

class Comment extends Model {

    public function addComment($post_id, $user_id, $content) {
        $stmt = $this->conn->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $post_id, $user_id, $content);
        return $stmt->execute();
    }

    public function deleteComment($comment_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $comment_id, $user_id);
        return $stmt->execute();
    }

    public function getCommentById($comment_id) {
        $stmt = $this->conn->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getCommentsByPost($post_id) {
        $stmt = $this->conn->prepare("
            SELECT comments.*, users.username 
            FROM comments
            JOIN users ON comments.user_id = users.id
            WHERE post_id = ?
            ORDER BY created_at ASC
        ");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        return $comments;
    }
}
?>
