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
    $video_path = null;
    error_log(print_r($_FILES, true));
    if (isset($_FILES['video']) && $_FILES['video']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['video']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Վիդեոյի վերբեռնումը ձախողվեց։';
        } else {
            $allowed_types = ['video/mp4', 'video/webm', 'video/ogg'];
            $mime_type = mime_content_type($_FILES['video']['tmp_name']);

            if (!in_array($mime_type, $allowed_types)) {
                $errors[] = 'Թույլատրվում են միայն MP4, WebM կամ OGG վիդեոներ։';
            } else {
                $upload_dir = __DIR__ . '/../uploads/videos/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $ext = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('video_') . '.' . $ext;
                $target_path = $upload_dir . $filename;

                if (move_uploaded_file($_FILES['video']['tmp_name'], $target_path)) {
                    $video_path = 'uploads/videos/' . $filename;
                } else {
                    $errors[] = 'Չհաջողվեց պահպանել վիդեոն։';
                }
            }
        }
    }

    if (!empty($errors)) {
        return ['status' => false, 'errors' => $errors];
    }

    $title_safe = mysqli_real_escape_string($con, $title);
    $content_safe = mysqli_real_escape_string($con, $content);
    $video_safe = $video_path ? "'" . mysqli_real_escape_string($con, $video_path) . "'" : "NULL";
    $user_id = intval($_SESSION['user']['id']);

    $sql = "INSERT INTO posts (user_id, title, content, video_path, created_at, updated_at) 
            VALUES ($user_id, '$title_safe', '$content_safe', $video_safe, NOW(), NOW())";

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

    $newVideoPath = '';
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['video/mp4', 'video/webm'];
        $mimeType = mime_content_type($_FILES['video']['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'Թույլատրված են միայն MP4 կամ WEBM վիդեո ֆայլեր։';
        } else {
            $ext = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
            $newName = uniqid('video_', true) . '.' . $ext;
            $uploadDir = realpath(__DIR__ . '/../public_html/uploads/videos');

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fullPath = $uploadDir . '/' . $newName;
            if (!move_uploaded_file($_FILES['video']['tmp_name'], $fullPath)) {
                $errors[] = 'Վիդեոյի վերբեռնումը ձախողվեց։';
            } else {
                if (!empty($existing['video_path'])) {
                    $oldFullPath = __DIR__ . '/../public_html/' . $existing['video_path'];
                    if (file_exists($oldFullPath)) {
                        unlink($oldFullPath);
                    }
                }
                $newVideoPath = 'uploads/videos/' . $newName;
            }
        }
    }

    if (!empty($errors)) {
        return ['status' => false, 'errors' => $errors];
    }

    $title_safe = mysqli_real_escape_string($con, $title);
    $content_safe = mysqli_real_escape_string($con, $content);
    $video_sql = $newVideoPath ? ", video_path='" . mysqli_real_escape_string($con, $newVideoPath) . "'" : "";

    $sql = "UPDATE posts SET title='$title_safe', content='$content_safe', updated_at=NOW() $video_sql
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

function likePost($con, $user_id, $post_id) {
    $stmt = $con->prepare("SELECT 1 FROM post_likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $con->prepare("DELETE FROM post_likes WHERE user_id = ? AND post_id = ?");
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();
        return 'unliked';
    } else {
        $stmt = $con->prepare("INSERT INTO post_likes (user_id, post_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();
        return 'liked';
    }
}

function getPostLikesCount($con, int $post_id) {
    $stmt = $con->prepare("SELECT COUNT(*) AS count FROM post_likes WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'] ?? 0;
}
function hasUserLikedPost($con, int $user_id, int $post_id) {
    $stmt = $con->prepare("SELECT 1 FROM post_likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}
