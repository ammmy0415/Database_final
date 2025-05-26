<?php
session_start();
require_once('config.php');

// 僅限 Admin 可新增電影
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("權限不足，無法新增電影。");
}

// 若送出表單，執行新增動作
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $director = trim($_POST['director']);
    $release_date = $_POST['release_date'];

    if ($title && $release_date) {
        $stmt = $conn->prepare("INSERT INTO Movies (title, director, release_date) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $director, $release_date);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        $error = "請填寫必要欄位：電影名稱與上映日期";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>新增電影</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        form { background: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: auto; }
        label { display: block; margin-top: 15px; }
        input[type="text"], input[type="date"] { width: 100%; padding: 8px; }
        input[type="submit"] { margin-top: 20px; padding: 10px 20px; background-color: #333; color: white; border: none; }
        .error { color: red; }
    </style>
</head>
<body>

<h2>🎬 新增電影</h2>

<?php if (!empty($error)): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <label>電影名稱（必填）</label>
    <input type="text" name="title" required>

    <label>導演</label>
    <input type="text" name="director">

    <label>上映日期（必填）</label>
    <input type="date" name="release_date" required>

    <input type="submit" value="新增電影">
</form>

</body>
</html>
