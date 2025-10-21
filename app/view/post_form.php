<link rel="stylesheet" href="app/css/post_form.css">

<div class="post-form-container">
    <form action="index.php?page=add_post" method="POST" enctype="multipart/form-data">
        <textarea name="content" placeholder="📝 What's on your mind?" required></textarea>
        <input type="file" name="images[]" accept="image/*" multiple>
        <button type="submit">
            ➕ Post
        </button>
    </form>
</div>
