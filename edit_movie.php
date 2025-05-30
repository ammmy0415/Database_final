<?php
session_start();
require_once 'config.php'; // 替換成你的資料庫連線檔案

// 確認是否為管理員
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("你沒有權限訪問此頁面");
}

// 確認有 movie_id
if (!isset($_GET['movie_id'])) {
    die("未提供電影 ID");
}

$movie_id = intval($_GET['movie_id']);

// 如果是表單送出（POST），則更新資料
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $director = $_POST['director'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $summary = $_POST['summary'];
    $poster_url = $_POST['poster_url'];

    $stmt = $conn->prepare("UPDATE Movies SET title=?, director=?, genre=?, release_date=?, summary=?, poster_url=? WHERE movie_id=?");
    $stmt->bind_param("ssssssi", $title, $director, $genre, $release_date, $summary, $poster_url, $movie_id);
    $stmt->execute();

    echo "<p class='success-msg'>✅ 電影資料已更新成功！</p>";
}

// 查詢電影資料
$stmt = $conn->prepare("SELECT * FROM Movies WHERE movie_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("找不到該電影");
}

$movie = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <title>修改電影資訊 - <?= htmlspecialchars($movie['title']) ?></title>
    <link rel="stylesheet" href="style/edit_movie.css" />
</head>
<body>

<div class="container">
    <h2>✏️ 修改電影資訊</h2>
    <form method="post" class="edit-form">
        <label>標題：</label>
        <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required />

        <label>導演：</label>
        <input type="text" name="director" value="<?= htmlspecialchars($movie['director']) ?>" />

        <label>類型：</label>
        <input type="text" name="genre" value="<?= htmlspecialchars($movie['genre']) ?>" />

        <label>上映日期：</label>
        <input type="date" name="release_date" value="<?= $movie['release_date'] ?>" />

        <label>簡介：</label>
        <textarea name="summary" rows="5"><?= htmlspecialchars($movie['summary']) ?></textarea>

        <label>海報連結：</label>
        <input type="text" name="poster_url" value="<?= htmlspecialchars($movie['poster_url']) ?>" />

        <button type="submit" class="btn-submit">儲存變更</button>
    </form>

    <p><a href="movie_detail.php?movie_id=<?= $movie_id ?>" class="back-link">⬅️ 回到電影頁面</a></p>
</div>
</body>
</html>