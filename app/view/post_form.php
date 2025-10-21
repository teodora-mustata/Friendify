<form action="index.php?page=add_post" method="POST" enctype="multipart/form-data">
    <textarea name="content" placeholder="What's on your mind?" required></textarea><br>
    <input type="file" name="images[]" accept="image/*" multiple><br><br>
    <button type="submit">Post</button>
</form>
