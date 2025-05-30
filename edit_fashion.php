<?php 
session_start();
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🔐 權限檢查
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("你沒有權限訪問此頁面");
}

// ✅ 取得 movie_id
if (!isset($_GET['movie_id'])) {
    die("未提供電影 ID");
}
$movie_id = intval($_GET['movie_id']);

// ✅ 更新已有穿搭推薦
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['fashion'] as $fashion_id => $data) {
        $look_title = $data['look_title'];
        $look_image_url = $data['look_image_url'];
        $description = $data['description'];

        $stmt = $conn->prepare("UPDATE MovieFashion SET look_title=?, look_image_url=?, description=? WHERE fashion_id=? AND movie_id=?");
        $stmt->bind_param("sssii", $look_title, $look_image_url, $description, $fashion_id, $movie_id);
        $stmt->execute();
    }
    echo "<p class='success-msg'>✅ 穿搭推薦已更新成功！</p>";
}

// ✅ 新增推薦並同步到關聯表
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $look_title = $_POST['new_look_title'];
    $look_image_url = $_POST['new_look_image_url'];
    $description = $_POST['new_description'];

    // 插入主表
    $stmt = $conn->prepare("INSERT INTO MovieFashion (movie_id, look_title, look_image_url, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $movie_id, $look_title, $look_image_url, $description);
    $stmt->execute();

    // 拿到新增的 fashion_id
    $new_fashion_id = $conn->insert_id;

    // 插入關聯表
    $stmt2 = $conn->prepare("INSERT INTO mov_fashion (mov_id, fashion_id) VALUES (?, ?)");
    $stmt2->bind_param("ii", $movie_id, $new_fashion_id);
    $stmt2->execute();

    echo "<p class='success-msg'>✅ 已新增穿搭推薦並同步寫入關聯表！</p>";
}

// ✅ 撈出該電影的所有穿搭 Look
$stmt = $conn->prepare("SELECT * FROM MovieFashion WHERE movie_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <title>編輯電影穿搭推薦</title>
    <link rel="stylesheet" href="style/edit_fashion.css" />
</head>
<body>

<div class="container">
    <h2>👕 編輯電影穿搭推薦</h2>

    <?php while ($fashion = $result->fetch_assoc()): ?>
    <form method="post" action="edit_fashion.php?movie_id=<?= $movie_id ?>" class="fashion-form">
        <div class="fashion-item">
            <img src="<?= htmlspecialchars($fashion['look_image_url']) ?>" alt="穿搭圖" class="fashion-img">

            <label>Look 標題：</label>
            <input type="text" name="fashion[<?= $fashion['fashion_id'] ?>][look_title]" value="<?= htmlspecialchars($fashion['look_title']) ?>">

            <label>圖片連結：</label>
            <input type="text" name="fashion[<?= $fashion['fashion_id'] ?>][look_image_url]" value="<?= htmlspecialchars($fashion['look_image_url']) ?>">

            <label>描述：</label>
            <textarea name="fashion[<?= $fashion['fashion_id'] ?>][description]" rows="3"><?= htmlspecialchars($fashion['description']) ?></textarea>

            <button type="submit" name="update" class="btn-update">💾 儲存變更</button>
        </div>
    </form>

    <form method="post" action="delete_fashion.php" onsubmit="return confirm('確定要刪除這筆推薦嗎？');" class="delete-form">
        <input type="hidden" name="fashion_id" value="<?= $fashion['fashion_id'] ?>">
        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
        <button type="submit" class="btn-delete">🗑 刪除推薦</button>
    </form>
    <?php endwhile; ?>

    <hr>

    <h2>➕ 新增穿搭推薦</h2>
    <form method="post" class="fashion-form">
        <div class="fashion-item">
            <label>Look 標題：</label>
            <input type="text" name="new_look_title" size="50" required>

            <label>圖片連結：</label>
            <input type="text" name="new_look_image_url" size="80" required>

            <label>描述：</label>
            <textarea name="new_description" rows="3" cols="60" required></textarea>

            <button type="submit" name="add">新增穿搭推薦</button>
        </div>
    </form>


    <p><a href="movie_detail.php?movie_id=<?= $movie_id ?>" class="back-link">⬅️ 回到電影頁面</a></p>
</div>

</body>
</html>