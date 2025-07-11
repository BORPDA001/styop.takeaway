<?php
require '../db.php';
require_once  '../posts.php';
$user_id = $_SESSION['user']['id'] ?? 0;
$result = mysqli_query($con, "SELECT * FROM posts WHERE video_path != '' ORDER BY RAND()");
$videos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $videos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Reels</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #000;
            color: #fff;
            overflow: hidden;
        }
        .reels-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }
        video {
            max-height: 70vh;
            width: auto;
            border-radius: 12px;
            background: #000;
            box-shadow: 0 0 20px rgba(255,255,255,0.1);
        }
        .video-info {
            margin-top: 1rem;
            max-width: 90%;
        }
        .video-info h5 {
            font-weight: bold;
        }
        .video-info p {
            color: #bbb;
            font-size: 0.95rem;
        }
        .arrow-btn {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            padding: 12px;
            border-radius: 50%;
            color: white;
            font-size: 1.5rem;
            transition: background 0.2s ease-in-out;
        }
        .arrow-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        #prev-btn {
            top: 20px;
        }
        #next-btn {
            bottom: 20px;
        }
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
        }
    </style>
</head>
<body>

<div class="reels-wrapper">

    <a href="web.php?action=index" class="btn btn-outline-light btn-sm back-btn">
        <i class="bi bi-arrow-left"></i> Գլխավոր
    </a>

    <button id="prev-btn" class="arrow-btn" title="Նախորդ"><i class="bi bi-arrow-up"></i></button>

    <video id="reel-video" controls autoplay muted playsinline>
        <source id="video-source" src="" type="video/mp4">
        Ձեր դիտարկիչը չի աջակցում վիդեոներ։
    </video>

    <div class="video-info mt-3">
        <h5 id="video-title">Վերնագիր</h5>
        <p id="video-desc">Նկարագրություն</p>
    </div>

    <button id="next-btn" class="arrow-btn" title="Հաջորդ"><i class="bi bi-arrow-down"></i></button>

</div>

<script>
    const videos = <?= json_encode($videos, JSON_UNESCAPED_UNICODE) ?>;
    let currentIndex = 0;

    const videoEl = document.getElementById('reel-video');
    const sourceEl = document.getElementById('video-source');
    const titleEl = document.getElementById('video-title');
    const descEl = document.getElementById('video-desc');

    function loadVideo(index) {
        const video = videos[index];
        sourceEl.src = `/styop/styop.takeaway/public_html/${video.video_path}`;
        videoEl.load();
        titleEl.textContent = video.title;
        descEl.textContent = video.content.length > 150 ? video.content.substring(0, 150) + '...' : video.content;
    }

    document.getElementById('next-btn').addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % videos.length;
        loadVideo(currentIndex);
    });

    document.getElementById('prev-btn').addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + videos.length) % videos.length;
        loadVideo(currentIndex);
    });

    loadVideo(currentIndex);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
