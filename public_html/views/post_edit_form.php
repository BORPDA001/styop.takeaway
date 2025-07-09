<?php
require __DIR__ . '/../db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: web.php?action=posts_list');
    exit;
}

$result = mysqli_query($con, "SELECT * FROM posts WHERE id=$id LIMIT 1");
$post = mysqli_fetch_assoc($result);

if (!$post) {
    header('Location: web.php?action=posts_list');
    exit;
}

$errors = $_SESSION['post_errors'] ?? [];
$old = $_SESSION['post_old'] ?? [];
unset($_SESSION['post_errors'], $_SESSION['post_old']);

$title = $old['title'] ?? $post['title'];
$content = $old['content'] ?? $post['content'];
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8" />
    <title>Խմբագրել գրառում</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container py-5">
    <h1>Խմբագրել գրառում</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/routes/web.php?action=post_update">
        <input type="hidden" name="id" value="<?= $id ?>" />
        <div class="mb-3">
            <label for="title" class="form-label">Վերնագիր</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Բովանդակություն</label>
            <textarea class="form-control" id="content" name="content" rows="6" required><?= htmlspecialchars($content) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Պահպանել փոփոխությունները</button>
        <a href="web.php?action=posts_list" class="btn btn-secondary ms-2">Հետ</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
