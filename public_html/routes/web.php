<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require __DIR__ . '/../auth.php';
require __DIR__ . '/../db.php';
require __DIR__ . '/../posts.php';

$action = $_REQUEST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        $result = register($con, $_POST);
        if ($result['status']) {
            header('Location: web.php?action=login_form');
        } else {
            $_SESSION['register_errors'] = $result['errors'];
            $_SESSION['old'] = $_POST;
            header('Location: web.php?action=register_form');
        }
        exit;
    } else if ($action === 'login') {
        $result = login($con, $_POST);
        if ($result['status']) {
            $_SESSION['user'] = $result['user'];
            header('Location: web.php?action=index');
        } else {
            $_SESSION['login_errors'] = $result['errors'];
            $_SESSION['old'] = $_POST;
            header('Location: web.php?action=login_form');
        }
        exit;
    } else if ($action === 'logout') {
        logout();
        header('Location: web.php?action=login_form');
        exit;
    } else if ($action === 'post_create') {
        $result = createPost($con, $_POST['title'] ?? '', $_POST['content'] ?? '');
        if ($result['status']) {
            header('Location: web.php?action=posts_list');
        } else {
            $_SESSION['post_errors'] = $result['errors'];
            $_SESSION['post_old'] = $_POST;
            header('Location: web.php?action=post_create_form');
        }
        exit;
    } else if ($action === 'post_update') {
        $id = intval($_POST['id'] ?? 0);
        $result = updatePost($con, $id, $_POST['title'] ?? '', $_POST['content'] ?? '');
        if ($result['status']) {
            header('Location: web.php?action=posts_list');
        } else {
            $_SESSION['post_errors'] = $result['errors'];
            $_SESSION['post_old'] = $_POST;
            header('Location: web.php?action=post_edit_form&id=' . $id);
        }
        exit;
    } else if ($action === 'post_delete') {
        $id = intval($_POST['id'] ?? 0);
        deletePost($con, $id);
        header('Location: web.php?action=posts_list');
        exit;
    } else {
        http_response_code(404);
        echo "Անհայտ POST գործողություն։";
        exit;
    }
} else {
    if ($action === 'register_form') {
        require __DIR__ . '/../views/register_form.php';
        exit;
    } else if ($action === 'login_form') {
        require __DIR__ . '/../views/login_form.php';
        exit;
    } else if ($action === 'index') {
        require __DIR__ . '/../index.php';
        exit;
    } else if ($action === 'posts_list') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $post = getPostById($con, $id);
            if (!$post) {
                header('Location: web.php?action=posts_list');
                exit;
            }
            require __DIR__ . '/../views/post_single.php';
            exit;
        } else {
            require __DIR__ . '/../views/posts_list.php';
            exit;
        }
    }
    else if ($action === 'post_create_form') {
        require __DIR__ . '/../views/post_create_form.php';
        exit;
    } else if ($action === 'post_edit_form') {
        $id = intval($_GET['id'] ?? 0);
        $post = getPostById($con, $id);
        if (!$post) {
            header('Location: web.php?action=posts_list');
            exit;
        }
        require __DIR__ . '/../views/post_edit_form.php';
        exit;
    }else if ($action === 'post_view') {
        require __DIR__ . '/../views/post_view.php';
        exit;
    }
    else {
        header('Location: web.php?action=login_form');
        exit;
    }
}
