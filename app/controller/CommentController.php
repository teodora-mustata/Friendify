<?php
require_once __DIR__ . '/../model/Comment.php';

class CommentController {
    private $conn;
    private $commentModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->commentModel = new Comment($conn);
    }

    public function addComment($post_id, $user_id, $content) {
        if (empty($content)) {
            return ['status' => 'error', 'message' => 'Comment cannot be empty'];
        }

        $success = $this->commentModel->addComment($post_id, $user_id, $content);

        if ($success) {
            return ['status' => 'success', 'comment' => $this->commentModel->getCommentsByPost($post_id)];
        } else {
            return ['status' => 'error', 'message' => 'Failed to add comment'];
        }
    }

    public function deleteComment($comment_id, $user_id) {
        $comment = $this->commentModel->getCommentById($comment_id);

        if (!$comment) {
            return ['status' => 'error', 'message' => 'Comment not found'];
        }

        if ($comment['user_id'] != $user_id) {
            return ['status' => 'error', 'message' => 'Not authorized'];
        }

        $success = $this->commentModel->deleteComment($comment_id, $user_id);

        if ($success) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete comment'];
        }
    }

    public function getComments($post_id) {
        return $this->commentModel->getCommentsByPost($post_id);
    }
}
?>
