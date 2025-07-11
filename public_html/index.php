<?php

$page_title = "Title";
require 'db.php';
$result = mysqli_query($con, "SELECT * FROM posts ORDER BY created_at DESC");
$logged_in = isset($_SESSION['user']);
?>
<!doctype html>
<html lang="hy">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        footer {
            font-size: 0.875rem;
        }
        .disabled-link {
            pointer-events: none;
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">
            💻 <?= $logged_in ? htmlspecialchars($_SESSION['user']['name']) . ' Panel' : 'Styop Project' ?>
        </a>

        <?php if ($logged_in): ?>
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item">
                    <a href="../routes/web.php?action=reels" class="nav-link">
                        <i class="bi bi-camera-reels"></i> Reels
                    </a>
                </li>
            </ul>

            <div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?= htmlspecialchars($_SESSION['user']['name']) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item text-danger" href="../routes/web.php?action=logout"><i class="bi bi-box-arrow-right"></i> Դուրս գալ</a></li>
                </ul>
            </div>
        <?php else: ?>
            <div class="ms-auto">
                <a href="routes/web.php?action=login_form" class="btn btn-outline-light btn-sm">Մուտք</a>
                <a href="routes/web.php?action=register_form" class="btn btn-outline-light btn-sm ms-2">Գրանցվել</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<main class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">
            Բարի գալուստ<?= $logged_in ? ', ' . htmlspecialchars($_SESSION['user']['name']) : '' ?>!
        </h1>
        <p class="lead text-muted">
            Դու <?= $logged_in ? 'մուտք ես գործել հաջողությամբ։ Այստեղ կարող ես ավելացնել քո Post-երը։' : 'կարող ես դիտել գրառումների ցանկը, բայց ստեղծել միայն մուտք գործածները կարող են։' ?>
        </p>
        <hr class="my-4" />
    </div>

    <?php if ($logged_in): ?>
        <div class="text-center mb-4">
            <a href="../routes/web.php?action=post_create_form" class="btn btn-success btn-lg rounded-pill px-5">
                ➕ Ավելացնել նոր գրառում
            </a>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Posts</h2>

            <?php if ($logged_in): ?>
                <div class="text-end mb-3">
                    <a href="../routes/web.php?action=posts_list" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="bi bi-journal-text me-1"></i> Գրառումների Ցուցակ
                    </a>
                </div>
            <?php endif; ?>

            <div class="list-group">
                <?php while ($post = mysqli_fetch_assoc($result)): ?>
                <?php if ($logged_in): ?>
                <a href="../routes/web.php?action=post_view&id=<?= $post['id'] ?>"
                   class="list-group-item list-group-item-action mt-3 rounded shadow-sm">
                    <?php else: ?>
                    <div class="list-group-item mt-3 rounded shadow-sm disabled-link">
                        <?php endif; ?>

                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?= htmlspecialchars($post['title']) ?></h5>
                            <small><?= $post['created_at'] ?></small>
                        </div>
                        <p class="mb-1 text-truncate"><?= htmlspecialchars($post['content']) ?></p>

                        <?php if ($logged_in): ?>
                </a>
                <?php else: ?>
            </div>
        <?php endif; ?>
        <?php endwhile; ?>
        </div>
    </div>
    </div>
</main>

<footer class="text-center mt-5 mb-3 text-muted">
    &copy; <?= date('Y') ?> Styop Project. Բոլոր իրավունքները պաշտպանված են։
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
