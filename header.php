<!-- header.php -->
<?php session_start(); ?>
<!--link rel="stylesheet" href="style/header.css"-->

<div class="header-bar" style="
    display: flex;
    padding: 12px 20px;
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 0.95rem;
    color: #333;
    margin-bottom: 20px;">
    <div class="left" style="
        flex: 1;
        display: flex;
        justify-content: flex-start;
        align-items: center;">
        <form class="search-form" action="index.php" method="get">
            <input type="text" name="search" placeholder="搜尋電影或導演">
            <button type="submit">🔍</button>
        </form>
    </div>

    <div class="right" style="
        display: flex;
        align-items: center;
        gap: 10px;">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="username">👤 歡迎，<?= htmlspecialchars($_SESSION['username']) ?>！</span>
            <a href="logout.php">登出</a>
        <?php else: ?>
            <a href="login.php">登入</a> | <a href="register.php">註冊</a>
        <?php endif; ?>
    </div>
</div>
