
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
    <title>使用者登入</title>
    <link rel="stylesheet" href="style/login.css">
</head>

<body>
<div class="container">
    <h2>登入</h2>
    <?php if (isset($_GET['error'])): ?>
        <p class="error-message">登入失敗：帳號或密碼錯誤</p>
    <?php endif; ?>
    <form action="login_process.php" method="post">
        <label for="username">帳號：</label>
        <input type="text" name="username" required><br>
        <label for="password">密碼：</label>
        <input type="password" name="password" required><br>
        <input type="submit" value="登入" class="submit">
    </form>
</div>
</body>
</html>
