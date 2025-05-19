<?php
session_start();
require_once('config.php');

if (!isset($_GET['id'])) {
    die("未提供電影 ID");
}
$movie_id = intval($_GET['id']);

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
    SELECT R.review_id, U.username, R.rating, R.review_text, R.created_at
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
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($movie['title']) ?> - 詳細資訊</title>
    <style>
        body { font-family: sans-serif; background: #f0f0f0; padding: 20px; }
        .section { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        img { max-width: 200px; display: block; margin-bottom: 10px; }
        .rating { color: #e67e22; font-weight: bold; }
    </style>
</head>
<body>

<h1><?= htmlspecialchars($movie['title']) ?></h1>

<div class="section">
    <h2>電影資訊</h2>
    <p><strong>導演：</strong><?= htmlspecialchars($movie['director']) ?></p>
    <p><strong>上映日期：</strong><?= $movie['release_date'] ?></p>
    <p><strong>簡介：</strong><br><?= nl2br(htmlspecialchars($movie['summary'])) ?></p>
    <?php if (!empty($movie['poster_url'])): ?>
        <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="海報">
    <?php endif; ?>
</div>

<div class="section">
    <h2>劇照</h2>
    <?php while ($still = $stills_result->fetch_assoc()): ?>
        <img src="<?= htmlspecialchars($still['image_url']) ?>" alt="劇照">
        <p><?= htmlspecialchars($still['description']) ?></p>
    <?php endwhile; ?>
</div>

<div class="section">
    <h2>穿搭推薦</h2>
    <?php while ($look = $fashion_result->fetch_assoc()): ?>
        <img src="<?= htmlspecialchars($look['look_image_url']) ?>" alt="穿搭圖">
        <p><strong><?= htmlspecialchars($look['look_title']) ?></strong><br>
        <?= htmlspecialchars($look['description']) ?></p>
    <?php endwhile; ?>
</div>

<div class="section">
    <h2>評分與影評</h2>
    <p class="rating">平均評分：<?= $avg_rating ?> / 5</p>
    <ul>
    <?php while ($rev = $review_result->fetch_assoc()): ?>
    <li>
        <strong><?= htmlspecialchars($rev['username']) ?></strong>（<?= $rev['rating'] ?> 分）<br>
        <?= nl2br(htmlspecialchars($rev['review_text'])) ?><br>
        <small><?= $rev['created_at'] ?></small>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['username'] === $rev['username']): ?>
        <br>
        <a href="delete_review.php?review_id=<?= urlencode($rev['review_id']) ?>&movie_id=<?= urlencode($movie_id) ?>"
        onclick="return confirm('你確定要刪除這則評論嗎？');"
        style="color: red;">🗑 刪除</a>
        <?php endif; ?>
    </li>
    <hr>
<?php endwhile; ?>

    </ul>
</div>

<?php if (isset($_SESSION['username'])): ?>
<div class="section">
    <h2>撰寫你的評論</h2>
    <form action="review_submit.php" method="post">
        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
        <label for="rating">評分（0~5）:</label>
        <input type="number" step="0.1" min="0" max="5" name="rating" required><br><br>

        <label for="review_text">評論內容:</label><br>
        <textarea name="review_text" rows="5" cols="50" required></textarea><br><br>

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
