<?php
session_start();
require_once('config.php');


if (!isset($_GET['movie_id'])) {
    die("未提供電影 ID");
}


$movie_id = intval($_GET['movie_id']);
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin');
// 查詢電影基本資料
$movie_sql = "SELECT * FROM Movies WHERE movie_id = ?";
$stmt = $conn->prepare($movie_sql);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie_result = $stmt->get_result();
$movie = $movie_result->fetch_assoc();
if (!$movie) {
    die("找不到此電影");
}

// 查詢劇照
$stills_sql = "SELECT * FROM MovieStills WHERE movie_id = ?";
$stills = $conn->prepare($stills_sql);
$stills->bind_param("i", $movie_id);
$stills->execute();
$stills_result = $stills->get_result();

// 查詢穿搭
$fashion_sql = "SELECT * FROM MovieFashion WHERE movie_id = ?";
$fashion = $conn->prepare($fashion_sql);
$fashion->bind_param("i", $movie_id);
$fashion->execute();
$fashion_result = $fashion->get_result();

// 查詢評論
$review_sql = "
    SELECT R.review_id,R.user_id, U.username, R.rating, R.review_text, R.created_at
    FROM Reviews R
    JOIN Users U ON R.user_id = U.user_id
    WHERE R.movie_id = ?
    ORDER BY R.created_at DESC";
$review = $conn->prepare($review_sql);
$review->bind_param("i", $movie_id);
$review->execute();
$review_result = $review->get_result();

// 查詢平均評分
$avg_sql = "SELECT ROUND(AVG(rating), 1) AS avg_rating FROM Reviews WHERE movie_id = ?";
$avg_stmt = $conn->prepare($avg_sql);
$avg_stmt->bind_param("i", $movie_id);
$avg_stmt->execute();
$avg_result = $avg_stmt->get_result();
$avg_rating = $avg_result->fetch_assoc()['avg_rating'] ?? '尚無評分';

//串流
$sql = "SELECT s.video_url, s.link_title 
        FROM StreamingLinks s 
        JOIN mov_streaming ms ON s.link_id = ms.link_id 
        WHERE ms.mov_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$streaming_links = $result->fetch_all(MYSQLI_ASSOC);
?>





<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($movie['title']) ?> - 詳細資訊</title>
    <link rel="stylesheet" href="style\movie_detail.css">
    <!--style>
        body { font-family: sans-serif; background: #f0f0f0; padding: 20px; }
        .section { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        img { max-width: 200px; display: block; margin-bottom: 10px; }
        .rating { color: #e67e22; font-weight: bold; }
        .edit-button {
            float: right;
            background-color: #3498db;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }
        .edit-button:hover {
            background-color: #2980b9;
        }

    </style-->
</head>
<body>

<h1><?= htmlspecialchars($movie['title']) ?></h1>

<div class="section">
    <h2>電影資訊
        <?php if ($isAdmin): ?>
            <span class="admin-action">
                <a href="edit_movie.php?movie_id=<?= $movie_id ?>"class="edit-button">修改</a>
            </span>
        <?php endif; ?>

    </h2>
    <p><strong>導演：</strong><?= htmlspecialchars($movie['director']) ?></p>
    <p><strong>上映日期：</strong><?= $movie['release_date'] ?></p>
    <p><strong>簡介：</strong><br><?= nl2br(htmlspecialchars($movie['summary'])) ?></p>
    <?php if (!empty($movie['poster_url'])): ?>
        <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="海報">
    <?php endif; ?>
</div>

<div class="section">
    <h2>劇照
        <?php if ($isAdmin): ?>
            <span class="admin-action">
                <a href="edit_stills.php?movie_id=<?= $movie_id ?>"class="edit-button">修改</a>
            </span>
        <?php endif; ?>

    </h2>
    <?php while ($still = $stills_result->fetch_assoc()): ?>
        <img src="<?= htmlspecialchars($still['image_url']) ?>" alt="劇照">
        <p><?= htmlspecialchars($still['description']) ?></p>
    <?php endwhile; ?>
</div>

<div class="section">
    <h2>穿搭推薦
        <?php if ($isAdmin): ?>
            <span class="admin-action">
                <a href="edit_fashion.php?movie_id=<?= $movie_id ?>"class="edit-button">修改</a>
            </span>
        <?php endif; ?>


    </h2>
    <?php while ($look = $fashion_result->fetch_assoc()): ?>
        <img src="<?= htmlspecialchars($look['look_image_url']) ?>" alt="穿搭圖">
        <p><strong><?= htmlspecialchars($look['look_title']) ?></strong><br>
        <?= htmlspecialchars($look['description']) ?></p>
    <?php endwhile; ?>
</div>

<div class="section">
    <h2>評分與影評</h2>
    <p class="rating">平均評分：<?= $avg_rating ?> / 5</p>

    <?php while ($review = $review_result->fetch_assoc()): ?>
        <div class="review-item">
            <div class="comment-box">
                <strong><?= htmlspecialchars($review['username']) ?></strong>（<?= $review['rating'] ?> 分）<br>
                <?= nl2br(htmlspecialchars($review['review_text'])) ?><br>
                <small><?= $review['created_at'] ?></small>
            </div>

            <div class="comment-box">
               <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] === $review['user_id'] || $isAdmin)): ?>
                    <form method="POST" action="delete_review.php" onsubmit="return confirm('確定刪除這則評論嗎？');" style="display:inline;">
                        <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
                        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
                        <button type="submit">🗑</button>
                    </form>
                <?php endif; ?>

                
            </div>
        </div>
    <?php endwhile; ?>
</div>


<div id="video-carousel">
    <h3>Streaming 預覽 
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
            <a href="edit_streaming.php?movie_id=<?= $movie_id ?>"
               style="margin-left: 20px; font-size: 14px; background: #3498db; color: white; padding: 4px 10px; border-radius: 5px; text-decoration: none;">
                修改
            </a>
        <?php endif; ?>
    </h3>
    <iframe id="streaming-video" src="" frameborder="0" allowfullscreen></iframe>
    <button class="btn-next" onclick="nextVideo()">下一部 ➡️</button>
</div>


<script>
    const videos = <?= json_encode($streaming_links); ?>;
    let currentIndex = 0;

    function loadVideo(index) {
        const iframe = document.getElementById('streaming-video');
        iframe.src = videos[index]['video_url'];
    }

    function nextVideo() {
        currentIndex = (currentIndex + 1) % videos.length;
        loadVideo(currentIndex);
    }

    window.onload = () => {
        if (videos.length > 0) loadVideo(0);
    };
</script>



<?php if (isset($_SESSION['username'])): ?>
<div class="section">
    <h2>撰寫你的評論</h2>
    <form action="review_submit.php" method="post">
        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
        
        <div class="comment-box">
            <label for="rating">評分（0~5）:</label>
            <input type="number" step="0.1" min="0" max="5" name="rating" required><br><br>
        </div>

        <div class="comment-box">
            <label for="review_text">評論內容:</label><br>
            <textarea name="review_text" rows="5" cols="50" required></textarea><br><br>
        </div>
        
        <input type="submit" value="送出評論">
    </form>
</div>
<?php else: ?>
    <div class="section">
        <p>請 <a href="login.php">登入</a> 才能撰寫評論。</p>
    </div>
<?php endif; ?>

<a href="index.php">← 回到電影清單</a>

</body>
</html>
