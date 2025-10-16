<form action="index.php?page=add_post" method="POST" enctype="multipart/form-data">
    <textarea name="content" placeholder="What's on your mind?" required></textarea><br>
    <input type="file" name="images[]" accept="image/*" multiple><br><br>
    <button type="submit">Post</button>
</form>

<div class="posts">
    <?php
    require_once __DIR__ . '/../controller/PostController.php';
    require_once __DIR__ . '/../../config/db.php';

    $conn = new mysqli('localhost', 'root', '', 'friendify_db');
    $postController = new PostController($conn);
    $posts = $postController->getAllPosts();

    foreach ($posts as $post) {
        echo '<div class="post">';
        echo '<p><strong>' . htmlspecialchars($post['username']) . '</strong></p>';
        echo '<p>' . nl2br(htmlspecialchars($post['content'])) . '</p>';

        foreach ($post['images'] as $img) {
            $src = 'data:' . $img['mime'] . ';base64,' . base64_encode($img['data']);
            echo '<img src="' . $src . '" style="max-width:100%;border-radius:8px;">';
        }

        echo '<p style="color:gray;font-size:12px;">Posted on ' . $post['created_at'] . '</p>';
        echo '</div>';
    }
    ?>
</div>
