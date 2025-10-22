<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../controller/PostController.php';

$conn = new mysqli('localhost', 'root', '', 'friendify_db');
if ($conn->connect_error) die("DB failed: " . $conn->connect_error);

$postController = new PostController($conn);
$user_id = $_SESSION['user_id'] ?? null;

$posts = $postController->getAllPosts();
?>

<link rel="stylesheet" href="app/css/feed.css">

<?php include 'post_form.php'; ?>

<div id="feed-container">
    <?php foreach ($posts as $post): ?>
        <?php
            $likesCount = $postController->getLikesCount($post['post_id']);
            $userLiked = $postController->hasUserLiked($post['post_id'], $user_id);
        ?>
        <div class="post" data-id="<?= $post['post_id'] ?>">
            
            <?php if ($post['user_id'] == $user_id): ?>
            <button class="delete-btn" data-post-id="<?= $post['post_id'] ?>">🗑️ Delete</button>
            <?php endif; ?>

            <p><strong><?= htmlspecialchars($post['username']) ?></strong></p>

            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

            <?php if (!empty($post['images'])): ?>
                <div class="post-images">
                    <?php foreach ($post['images'] as $img): ?>
                        <?php $src = 'data:' . $img['mime'] . ';base64,' . base64_encode($img['data']); ?>
                        <img src="<?= $src ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="post-footer">
                <p>Posted on <?= $post['created_at'] ?></p>
                <button class="like-btn" data-post-id="<?= $post['post_id'] ?>">
                    <span class="like-text"><?= $userLiked ? '💔 Unlike' : '❤️ Like' ?></span>
                    <span class="like-count-wrapper">(<span class="like-count"><?= $likesCount ?></span>)</span>
                </button>
            </div>

        </div>
    <?php endforeach; ?>
</div>


<script>
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', async e => {
        e.preventDefault();
        const postId = btn.dataset.postId;
        const formData = new FormData();
        formData.append('post_id', postId);

        try {
            const res = await fetch('app/ajax/toggle_like.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            console.log(data);

            if (data.status && typeof data.likes !== 'undefined') {
                const likeText = btn.querySelector('.like-text');
                const likeCount = btn.querySelector('.like-count');

                if (likeText) likeText.textContent = (data.status === 'liked' ? '💔 Unlike' : '❤️ Like');
                if (likeCount) likeCount.textContent = data.likes;
            }
        } catch(err) {
            console.error('Error:', err);
        }
    });
});

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async e => {
        e.preventDefault();
        const postId = btn.dataset.postId;

        if (!confirm("Are you sure you want to delete this post?")) return;

        const formData = new FormData();
        formData.append('post_id', postId);

        try {
            const res = await fetch('app/ajax/delete_post.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.status === 'success') {
                btn.closest('.post').remove();
            } else {
                alert(data.message || 'Error deleting post');
            }
        } catch(err) {
            console.error('Error:', err);
        }
    });
});

</script>
