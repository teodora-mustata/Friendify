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
            $commentsCount = $postController->getCommentsCount($post['post_id']);
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

                <div class="post-actions">
                        <button class="see-comments-btn" data-post-id="<?= $post['post_id'] ?>">💬 Comments (<?= $commentsCount ?>)</button>
                        <button class="like-btn" data-post-id="<?= $post['post_id'] ?>">
                            <span class="like-text"><?= $userLiked ? '💔 Unlike' : '❤️ Like' ?></span>
                            (<span class="like-count"><?= $likesCount ?></span>)
                        </button>
                </div>

            </div>
        </div>
    <?php endforeach; ?>

    <div id="comments-modal" class="comments-modal">
        <div class="comments-content">
            <span class="close-modal">&times;</span>
            <h3>Comments</h3>
            <div id="comments-list"></div>

            <form id="add-comment-form">
                <input type="hidden" name="post_id" id="modal-post-id">
                <textarea name="content" placeholder="Write a comment..." required></textarea>
                <button type="submit">Post Comment</button>
            </form>
        </div>
    </div>

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

const modal = document.getElementById('comments-modal');
const commentsList = document.getElementById('comments-list');
const modalPostId = document.getElementById('modal-post-id');
const userId = <?= $user_id ?? 0 ?>;

document.querySelectorAll('.see-comments-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const postId = btn.dataset.postId;
        modalPostId.value = postId;
        commentsList.innerHTML = 'Loading...';

        try {
            const res = await fetch('app/ajax/get_comments.php?post_id=' + postId);
            const data = await res.json();

            if(data.status === 'success'){
                commentsList.innerHTML = '';
                data.comments.forEach(c => {
                    const div = document.createElement('div');
                    div.classList.add('comment');
                    div.style.display = 'flex';
                    div.style.justifyContent = 'space-between';
                    div.style.alignItems = 'center';
                    div.innerHTML = `<span><strong>${c.username}:</strong> ${c.content}</span>
                        ${c.user_id == <?= $user_id ?? 0 ?> ? '<button class="delete-comment" data-id="'+c.id+'">🗑️</button>' : ''}`;
                    commentsList.appendChild(div);
                });
            } else {
                commentsList.innerHTML = `<p>${data.message}</p>`;
            }

            modal.style.display = 'block';
        } catch(err) { console.error(err); }
    });
});

document.querySelector('.close-modal').addEventListener('click', () => { modal.style.display = 'none'; });
window.addEventListener('click', e => { if(e.target == modal) modal.style.display = 'none'; });

document.getElementById('add-comment-form').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
        const res = await fetch('app/ajax/add_comment.php', { method: 'POST', body: formData });
        const data = await res.json();

        if(data.status === 'success'){
            const div = document.createElement('div');
            div.classList.add('comment');
            const username = '<?= $_SESSION['username'] ?? 'You' ?>';
            div.innerHTML = `<strong>${username}:</strong> ${formData.get('content')}`;
            commentsList.appendChild(div);

            const postBtn = document.querySelector(`.see-comments-btn[data-post-id='${formData.get('post_id')}']`);
            if(postBtn){
                let current = parseInt(postBtn.textContent.match(/\d+/)?.[0] || 0);
                postBtn.textContent = `💬 Comments (${current + 1})`;
            }

            e.target.reset();
        } else {
            alert(data.message);
        }
    } catch(err){ console.error(err); }
});
</script>