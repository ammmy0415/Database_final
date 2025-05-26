<?php
session_start();
require_once 'config.php'; // 替換為你自己的資料庫連線

// 確認管理員身份
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("你沒有權限訪問此頁面");
}

// 確認有 movie_id
if (!isset($_GET['movie_id'])) {
    die("未提供電影 ID");
}

$movie_id = intval($_GET['movie_id']);

// === 修改現有劇照 ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['stills'] as $still_id => $stillData) {
        $image_url = $stillData['image_url'];
        $description = $stillData['description'];

        $stmt = $conn->prepare("UPDATE MovieStills SET image_url=?, description=? WHERE still_id=? AND movie_id=?");
        $stmt->bind_param("ssii", $image_url, $description, $still_id, $movie_id);
        $stmt->execute();
    }
    echo "<p>✅ 劇照已更新！</p>";
}

// === 新增劇照 ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $image_url = $_POST['new_image_url'];
    $description = $_POST['new_description'];

    $stmt = $conn->prepare("INSERT INTO MovieStills (movie_id, image_url, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $movie_id, $image_url, $description);
    $stmt->execute();

    // 取得剛剛新增的 still_id（主鍵）
    $new_still_id = $conn->insert_id;

    // 新增到關聯表 mov_still
    $stmt = $conn->prepare("INSERT INTO mov_still (movie_id, still_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $movie_id, $new_still_id);
    $stmt->execute();

    echo "<p>✅ 新劇照已新增！</p>";
}

// 查詢該電影的劇照
$stmt = $conn->prepare("SELECT * FROM MovieStills WHERE movie_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stills_result = $stmt->get_result();
?>

<h2>🎞️ 編輯電影劇照</h2>

<?php while ($still = $stills_result->fetch_assoc()): ?>
    <form method="post" action="edit_stills.php?movie_id=<?= $movie_id ?>">
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
            <img src="<?= htmlspecialchars($still['image_url']) ?>" alt="劇照" width="200"><br>
            <label>圖片連結：</label><br>
            <input type="text" name="stills[<?= $still['still_id'] ?>][image_url]" value="<?= htmlspecialchars($still['image_url']) ?>" size="80"><br>
            <label>描述：</label><br>
            <textarea name="stills[<?= $still['still_id'] ?>][description]" rows="3" cols="60"><?= htmlspecialchars($still['description']) ?></textarea><br>
            <button type="submit" name="update" value="<?= $still['still_id'] ?>">💾 儲存單筆</button>
        </div>
    </form>

    <!-- ✅ 獨立的刪除表單 -->
    <form method="post" action="delete_still.php" onsubmit="return confirm('確定要刪除這張劇照嗎？');" style="margin-bottom: 20px;">
        <input type="hidden" name="still_id" value="<?= $still['still_id'] ?>">
        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
        <button type="submit" style="color:red;">🗑 刪除劇照</button>
    </form>
<?php endwhile; ?>


<hr>

<h3>➕ 新增劇照</h3>
<form method="post">
    <label>圖片連結：</label><br>
    <input type="text" name="new_image_url" size="80" required><br>
    <label>描述：</label><br>
    <textarea name="new_description" rows="3" cols="60" required></textarea><br>
    <button type="submit" name="add">新增劇照</button>
</form>

<p><a href="movie_detail.php?movie_id=<?= $movie_id ?>">⬅️ 回到電影頁面</a></p>
