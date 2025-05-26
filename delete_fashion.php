<?php
session_start();
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 權限檢查（可選）
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("你沒有權限訪問此頁面");
}

// 確保傳入 fashion_id 與 movie_id
if (!isset($_POST['fashion_id']) || !isset($_POST['movie_id'])) {
    die("缺少必要參數");
}

$fashion_id = intval($_POST['fashion_id']);
$movie_id = intval($_POST['movie_id']);


// 🔸 1. 先刪除關聯表 mov_fashion 中對應資料
$stmt1 = $conn->prepare("DELETE FROM mov_fashion WHERE fashion_id = ?");
$stmt1->bind_param("i", $fashion_id);
$stmt1->execute();

// 🔸 2. 再刪除主表 MovieFashion 中的該筆穿搭資料
$stmt2 = $conn->prepare("DELETE FROM MovieFashion WHERE fashion_id = ?");
$stmt2->bind_param("i", $fashion_id);
$stmt2->execute();

// 🔸 3. 導回電影詳細頁面
header("Location: movie_detail.php?movie_id=" . $movie_id);
exit();
?>
