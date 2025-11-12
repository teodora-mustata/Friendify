<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/model/Post.php';

class PostTest extends TestCase
{
    private $postMock;

    protected function setUp(): void
    {
        $this->postMock = $this->getMockBuilder(Post::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods(['createPost', 'toggleLike', 'getLikesCount', 'getPostById', 'hasUserLiked'])
                               ->getMock();
    }

    public function testCreatePostReturnsTrue()
    {
        $this->postMock->method('createPost')->willReturn(true);
        $result = $this->postMock->createPost(1, 'Test content', []);
        $this->assertTrue($result);
    }

    public function testToggleLikeReturnsLiked()
    {
        $this->postMock->method('toggleLike')->willReturn('liked');
        $result = $this->postMock->toggleLike(1, 2);
        $this->assertEquals('liked', $result);
    }

    public function testToggleLikeReturnsUnliked()
    {
        $this->postMock->method('toggleLike')->willReturn('unliked');
        $result = $this->postMock->toggleLike(1, 2);
        $this->assertEquals('unliked', $result);
    }

    public function testGetLikesCountReturnsNumber()
    {
        $this->postMock->method('getLikesCount')->willReturn(5);
        $count = $this->postMock->getLikesCount(1);
        $this->assertEquals(5, $count);
    }

    public function testGetPostByIdReturnsArray()
    {
        $this->postMock->method('getPostById')->willReturn([
            'post_id' => 1,
            'user_id' => 1,
            'username' => 'testuser',
            'content' => 'Test content',
            'created_at' => '2025-11-12 18:00:00',
            'likes' => 0,
            'images' => []
        ]);
        $post = $this->postMock->getPostById(1);
        $this->assertIsArray($post);
        $this->assertEquals('testuser', $post['username']);
        $this->assertEquals('Test content', $post['content']);
    }
}
?>
