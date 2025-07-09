<?php
    require __DIR__ . '/../db.php';
$result = mysqli_query($con, "SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8" />
    <title>Գրառումների ցանկ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container py-5">
    <h1>Գրառումների ցանկ</h1>
    <a href="web.php?action=post_create_form" class="btn btn-success mb-3">Ավելացնել նոր գրառում</a>

    <table class="table table-bordered table-striped">
        <?php while($post = mysqli_fetch_assoc($result)): ?>
        <thead>
        <tr>
            <th>ID</th>
            <th>Վերնագիր</th>
            <th>Բովանդակություն</th>
            <th>Ստեղծման ժամանակ</th>
            <th>Թարմացման ժամանակ</th>
            <?php if ($_SESSION['user']['id'] === $post['user_id']): ?>
            <th>Գործողություններ</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $post['id'] ?></td>
                <td><?= htmlspecialchars($post['title']) ?></td>
                <td><?= htmlspecialchars($post['content']) ?></td>
                <td><?= $post['created_at'] ?></td>
                <td><?= $post['updated_at'] ?></td>
                <td>
                    <?php if ($_SESSION['user']['id'] === $post['user_id']): ?>
                        <a href="web.php?action=post_edit_form&id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary">Փոփոխել</a>
                        <form action="web.php?action=post_delete" method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $post['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Համոզվա՞ծ ես, որ ուզում ես ջնջել։')">Ջնջել</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="web.php?action=index" class="btn btn-secondary mt-3">Վերադառնալ գլխավոր էջ</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
