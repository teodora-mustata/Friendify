<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/controller/UserController.php';
require_once __DIR__ . '/../app/controller/CommentController.php';
require_once __DIR__ . '/../app/model/Like.php';
require_once __DIR__ . '/../app/model/Post.php';

class CoreFunctionalityTest extends TestCase
{
    private $conn;
    private $userController;
    private $commentController;
    private $likeModel;
    private $tempUserId;
    private $tempPostId;

    protected function setUp(): void
    {
        $this->conn = new mysqli('localhost', 'root', '', 'friendify_db');
        $this->userController = new UserController($this->conn);
        $this->commentController = new CommentController($this->conn);
        $this->likeModel = new Like($this->conn);

        // create temporary user
        $username = 'tempuser_' . uniqid();
        $email = $username . '@example.com';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();
        $this->tempUserId = $this->conn->insert_id;

        // create temporary post
        $content = 'Temporary test post';
        $stmt = $this->conn->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $this->tempUserId, $content);
        $stmt->execute();
        $this->tempPostId = $this->conn->insert_id;
    }

    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM comments WHERE post_id = {$this->tempPostId}");
        $this->conn->query("DELETE FROM likes WHERE post_id = {$this->tempPostId}");
        $this->conn->query("DELETE FROM posts WHERE id = {$this->tempPostId}");
        $this->conn->query("DELETE FROM users WHERE id = {$this->tempUserId}");
    }

    public function testCreateUser()
    {
        $this->assertNotNull($this->tempUserId, 'Temporary user was created successfully.');
    }

    public function testCreatePost()
    {
        $this->assertNotNull($this->tempPostId, 'Temporary post was created successfully.');
    }

    public function testAddCommentToPost()
    {
        $result = $this->commentController->addComment($this->tempPostId, $this->tempUserId, 'Temporary comment');
        $this->assertEquals('success', $result['status']);
    }

    public function testAddLikeToPost()
    {
        $this->likeModel->addLike($this->tempPostId, $this->tempUserId);
        $this->assertTrue($this->likeModel->hasUserLiked($this->tempPostId, $this->tempUserId));
    }

    public function testRemoveLikeFromPost()
    {
        $this->likeModel->addLike($this->tempPostId, $this->tempUserId);
        $this->likeModel->removeLike($this->tempPostId, $this->tempUserId);
        $this->assertFalse($this->likeModel->hasUserLiked($this->tempPostId, $this->tempUserId));
    }
}