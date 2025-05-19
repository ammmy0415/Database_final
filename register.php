
<?php
session_start();
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>使用者註冊</title>
</head>
<body>
    <h2>註冊新帳號</h2>
    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;">註冊失敗：<?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>
    <form action="register_process.php" method="post">
        <label for="username">帳號：</label>
        <input type="text" name="username" required><br>
        <label for="email">電子郵件：</label>
        <input type="email" name="email" required><br>
        <label for="password">密碼：</label>
        <input type="password" name="password" required><br>
        <input type="submit" value="註冊">
    </form>
    <p><a href="login.php">已有帳號？點此登入</a></p>
</body>
</html>
