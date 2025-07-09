<?php
$errors = $_SESSION['post_errors'] ?? [];
$old = $_SESSION['post_old'] ?? [];
unset($_SESSION['post_errors'], $_SESSION['post_old']);
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8" />
    <title>Նոր գրառում</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; }
        .container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input[type="text"], textarea { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 16px; }
        button { background: #4CAF50; color: white; padding: 12px 20px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; }
        button:hover { background: #45a049; }
        .error { background: #ffe0e0; color: #b30000; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
        a { text-decoration: none; color: #333; }
    </style>
</head>
<body>

<div class="container">
    <h2>Ավելացնել նոր գրառում</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/routes/web.php?action=post_create" method="post">
        <input type="hidden" name="action" value="post_create">
        <input type="text" name="title" placeholder="Վերնագիր" value="<?= htmlspecialchars($old['title'] ?? '') ?>" required>
        <textarea name="content" rows="6" placeholder="Բովանդակություն" required><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
        <button type="submit">Պահպանել</button>
    </form>
    <p><a href="/routes/web.php?action=posts_list">Վերադառնալ ցուցակին</a></p>
</div>

</body>
</html>
