<!-- header.php -->
<?php session_start(); ?>

<div style="text-align: right; padding: 10px; background-color: #eee;">
    <?php if (isset($_SESSION['username'])): ?>
        👤 歡迎，<?= htmlspecialchars($_SESSION['username']) ?>！
        <a href="logout.php" style="margin-left: 10px;">登出</a>
    <?php else: ?>
        <a href="login.php">登入</a> | <a href="register.php">註冊</a>
    <?php endif; ?>
</div>