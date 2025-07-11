<?php
require __DIR__ . '/../db.php';
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "Սխալ ID։";
    exit;
}
$post = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM posts WHERE id = $id"));
if (!$post) {
    echo "Գրառումը չի գտնվել։";
    exit;
}
$page_title = "Գրառման դիտում";
?>
<!doctype html>
<html lang="hy">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="web.php?action=posts_list" class="btn btn-secondary mb-4">&larr; Վերադառնալ</a>
    <div class="card shadow-sm rounded-4">
        <div class="card-body">
            <h2 class="card-title"><?= htmlspecialchars($post['title']) ?></h2>
            <p class="text-muted mb-2"><small>Ստեղծվել է՝ <?= $post['created_at'] ?></small></p>
            <hr>
            <p class="card-text"><?= nl2br(htmlspecialchars($post['content'])) ?></p>

            <?php if (!empty($post['video_path'])): ?>
                <div class="mt-4">
                    <video width="50%" controls>
                        <source src="/styop/styop.takeaway/public_html/<?= htmlspecialchars($post['video_path']) ?>" type="video/mp4" />
                        Ձեր դիտարկիչը չի աջակցում վիդեոներ:
                    </video>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>
