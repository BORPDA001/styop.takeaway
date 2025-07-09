<?php

function getAllPosts($con) {
    $result = mysqli_query($con, "SELECT * FROM posts ORDER BY created_at DESC");
    $posts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
    return $posts;
}

function getPostById($con, int $id) {
    $id = intval($id);
    if ($id <= 0) {
        return null;
    }
    $sql = "SELECT * FROM posts WHERE id=$id";
    $result = mysqli_query($con, $sql);
    return mysqli_fetch_assoc($result);
}

function createPost($con, string $title, string $content) {
    $errors = [];
    $title = trim($title);
    $content = trim($content);

    if ($title === '') {
        $errors[] = 'Վերնագիրը պարտադիր է։';
    }
    if ($content === '') {
        $errors[] = 'Բովանդակությունը պարտադիր է։';
    }
    if (!isset($_SESSION['user']['id'])) {
        $errors[] = 'Օգտատերը մուտք գործած չէ։';
    }

    if (!empty($errors)) {
        return ['status' => false, 'errors' => $errors];
    }

    $title_safe = mysqli_real_escape_string($con, $title);
    $content_safe = mysqli_real_escape_string($con, $content);
    $user_id = intval($_SESSION['user']['id']);

    $sql = "INSERT INTO posts (user_id, title, content, created_at, updated_at) 
            VALUES ($user_id, '$title_safe', '$content_safe', NOW(), NOW())";
    if (mysqli_query($con, $sql)) {
        return ['status' => true];
    } else {
        return ['status' => false, 'errors' => ['Չհաջողվեց պահպանել գրառումը՝ ' . mysqli_error($con)]];
    }
}

function updatePost($con, int $id, string $title, string $content) {
    $errors = [];
    $id = intval($id);
    $title = trim($title);
    $content = trim($content);
    $user_id = intval($_SESSION['user']['id'] ?? 0);

    if ($id <= 0 || $user_id <= 0) {
        $errors[] = 'Անհայտ գրառում կամ օգտատեր։';
    }
    $existing = getPostById($con, $id);
    if (!$existing || $existing['user_id'] != $user_id) {
        $errors[] = 'Դու իրավունք չունես խմբագրել այս գրառումը։';
    }

    if ($title === '') {
        $errors[] = 'Վերնագիրը պարտադիր է։';
    }
    if ($content === '') {
        $errors[] = 'Բովանդակությունը պարտադիր է։';
    }

    if (!empty($errors)) {
        return ['status' => false, 'errors' => $errors];
    }

    $title_safe = mysqli_real_escape_string($con, $title);
    $content_safe = mysqli_real_escape_string($con, $content);

    $sql = "UPDATE posts SET title='$title_safe', content='$content_safe', updated_at=NOW() 
            WHERE id=$id AND user_id=$user_id";
    if (mysqli_query($con, $sql)) {
        return ['status' => true];
    } else {
        return ['status' => false, 'errors' => ['Չհաջողվեց թարմացնել գրառումը՝ ' . mysqli_error($con)]];
    }
}

function deletePost($con, int $id) {
    $id = intval($id);
    $user_id = intval($_SESSION['user']['id'] ?? 0);
    if ($id <= 0 || $user_id <= 0) {
        return false;
    }
    $post = getPostById($con, $id);
    if (!$post || $post['user_id'] != $user_id) {
        return false;
    }

    $sql = "DELETE FROM posts WHERE id=$id AND user_id=$user_id";
    return mysqli_query($con, $sql);
}
