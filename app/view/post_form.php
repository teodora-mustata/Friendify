<link rel="stylesheet" href="app/css/post_form.css">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed</title>
    <link rel="stylesheet" href="app/css/feed.css">
</head>
<body>
<div class="post-form-container">
    <form action="index.php?page=add_post" method="POST" enctype="multipart/form-data">
        <textarea name="content" placeholder="📝 What's on your mind?" required></textarea>
        <input type="file" name="images[]" accept="image/*" multiple>
        <button type="submit">
            ➕ Post
        </button>
    </form>
</div>
</body>
</html>