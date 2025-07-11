<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require '../auth.php';
require '../db.php';
require '../posts.php';
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
    }

    else if ($action === 'login') {
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
    }
    else if ($action === 'add_comment') {
        header('Content-Type: application/json');
        $user_id = $_SESSION['user']['id'] ?? 0;
        $post_id = intval($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if ($user_id <= 0) {
            echo json_encode(['error' => 'Մուտք գործեք մեկնաբանելու համար։']);
            exit;
        }

        if ($post_id <= 0 || empty($content)) {
            echo json_encode(['error' => 'Դատարկ տվյալներ։']);
            exit;
        }

        if (addComment($con, $user_id, $post_id, $content)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Չհաջողվեց ավելացնել մեկնաբանություն։']);
        }
        exit;
    }

    else if ($action === 'logout') {
        logout();
        header('Location: web.php?action=login_form');
        exit;
    }
    else if ($action === 'post_create') {
        $result = createPost($con, $_POST['title'] ?? '', $_POST['content'] ?? '');
        if ($result['status']) {
            header('Location: web.php?action=posts_list');
        } else {
            $_SESSION['post_errors'] = $result['errors'];
            $_SESSION['post_old'] = $_POST;
            header('Location: web.php?action=post_create_form');
        }
        exit;
    }
    else if ($action === 'post_update') {
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
    }
    else if ($action === 'post_like') {
        $user_id = $_SESSION['user']['id'] ?? 0;
        $post_id = intval($_POST['post_id'] ?? 0);
        if ($user_id > 0 && $post_id > 0) {
            $result = likePost($con, $user_id, $post_id);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    else if ($action === 'post_delete') {
        $id = intval($_POST['id'] ?? 0);
        deletePost($con, $id);
        header('Location: web.php?action=posts_list');
        exit;
    }

    else {
        http_response_code(404);
        echo "Անհայտ POST գործողություն։";
        exit;
    }
}
else {
    if ($action === 'register_form') {
        require '../views/register_form.php';
        exit;
    }

    else if ($action === 'login_form') {
        require '../views/login_form.php';
        exit;
    }

    else if ($action === 'index') {
        require '../index.php';
        exit;
    }

    else if ($action === 'posts_list') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $post = getPostById($con, $id);
            if (!$post) {
                header('Location: web.php?action=posts_list');
                exit;
            }
            require '../views/post_single.php';
            exit;
        } else {
            require '../views/posts_list.php';
            exit;
        }
    }

    else if ($action === 'reels') {
        require '../views/reels.php';
        exit;
    }
    else if ($action === 'post_create_form') {
        require '../views/post_create_form.php';
        exit;
    }

    else if ($action === 'post_edit_form') {
        $id = intval($_GET['id'] ?? 0);
        $post = getPostById($con, $id);
        if (!$post) {
            header('Location: web.php?action=posts_list');
            exit;
        }
        require '../views/post_edit_form.php';
        exit;
    }

    else if ($action === 'post_view') {
        require '../views/post_view.php';
        exit;
    }

    else {
        header('Location: web.php?action=login_form');
        exit;
    }
}
