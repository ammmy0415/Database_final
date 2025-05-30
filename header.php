<!-- header.php -->
<?php session_start(); ?>
<link rel="stylesheet" href="style/header.css">

<div class="header-bar">
    <?php if (isset($_SESSION['username'])): ?>
        <span class="username">👤 歡迎，<?= htmlspecialchars($_SESSION['username']) ?>！</span>
        <a href="logout.php">登出</a>
    <?php else: ?>
        <a href="login.php" style="margin-right: 15px">登入</a>|<a href="register.php">註冊</a>
    <?php endif; ?>
</div>
