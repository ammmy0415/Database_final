<?php
session_start();
require_once 'config.php';


// ⚠️ 僅限 Admin 存取
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("你沒有權限訪問此頁面");
}

// ⚠️ 檢查是否有 movie_id
if (!isset($_GET['movie_id'])) {
    die("未提供電影 ID");
}
$movie_id = intval($_GET['movie_id']);

// ✅ 處理更新（修改）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['streaming'] as $link_id => $data) {
        $title = $data['link_title'];
        $url = $data['video_url'];
        $stmt = $conn->prepare("UPDATE StreamingLinks SET link_title = ?, video_url = ? WHERE link_id = ?");
        $stmt->bind_param("ssi", $title, $url, $link_id);
        $stmt->execute();
    }
    header("Location: edit_streaming.php?movie_id=$movie_id");
    exit;
}

// ✅ 處理新增
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $new_title = $_POST['new_title'];
    $new_url = $_POST['new_url'];

    // 插入 StreamingLinks
    $stmt = $conn->prepare("INSERT INTO StreamingLinks (movie_id, link_title, video_url) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $movie_id, $new_title, $new_url);
    $stmt->execute();
    $new_link_id = $stmt->insert_id;

    // 插入關聯表
    $stmt = $conn->prepare("INSERT INTO mov_streaming (mov_id, link_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $movie_id, $new_link_id);
    $stmt->execute();

    header("Location: edit_streaming.php?movie_id=$movie_id");
    exit;
}

// ✅ 處理刪除
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    // 先刪除關聯表
    $stmt = $conn->prepare("DELETE FROM mov_streaming WHERE link_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    // 再刪除主表
    $stmt = $conn->prepare("DELETE FROM StreamingLinks WHERE link_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: edit_streaming.php?movie_id=$movie_id");
    exit;
}

// ✅ 抓取該電影所有 streaming 連結
$stmt = $conn->prepare("SELECT s.link_id, s.link_title, s.video_url 
                        FROM StreamingLinks s
                        JOIN mov_streaming ms ON s.link_id = ms.link_id
                        WHERE ms.mov_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$streaming_links = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>編輯 Streaming</title>
</head>
<body>
    <h2>編輯電影串流連結</h2>

    <form method="POST">
        <?php foreach ($streaming_links as $link): ?>
            <div style="margin-bottom: 15px;">
                標題：
                <input type="text" name="streaming[<?= $link['link_id'] ?>][link_title]" value="<?= htmlspecialchars($link['link_title']) ?>" required>
                影片網址：
                <input type="text" name="streaming[<?= $link['link_id'] ?>][video_url]" value="<?= htmlspecialchars($link['video_url']) ?>" required>
                <button type="submit" name="delete_id" value="<?= $link['link_id'] ?>" onclick="return confirm('確定要刪除這筆資料？')">❌ 刪除</button>
            </div>
        <?php endforeach; ?>
        <button type="submit" name="update">💾 儲存修改</button>
    </form>

    <hr>

    <h3>新增新的串流影片</h3>
    <form method="POST">
        標題：<input type="text" name="new_title" required>
        影片網址：<input type="text" name="new_url" required>
        <button type="submit" name="add">➕ 新增</button>
    </form>

    <hr>
    <a href="movie_detail.php?movie_id=<?= $movie_id ?>" 
    style="display: inline-block; margin-top: 20px; background-color: #555; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none;">
        ← 回到電影頁面
    </a>

    
</body>
</html>
