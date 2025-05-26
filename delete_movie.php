<?php
session_start();
require_once('config.php');

// 僅允許 Admin 進行刪除
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("權限不足，無法執行此操作。");
}

// 檢查參數是否正確
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $movie_id = intval($_GET['id']);

    // 刪除相關評分
    $stmt1 = $conn->prepare("DELETE FROM Reviews WHERE movie_id = ?");
    $stmt1->bind_param("i", $movie_id);
    $stmt1->execute();

    // 刪除電影資料
    $stmt2 = $conn->prepare("DELETE FROM Movies WHERE movie_id = ?");
    $stmt2->bind_param("i", $movie_id);
    $stmt2->execute();

    // 回到首頁
    header("Location: index.php");
    exit;
} else {
    echo "無效的請求參數。";
}
?>
