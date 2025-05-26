<?php
session_start();
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 權限檢查：是否有登入
if (!isset($_SESSION['user_id'])) {
    die("請先登入才能留言");
}

// 檢查是否傳入必要欄位
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['movie_id'], $_POST['rating'], $_POST['review_text'])) {
        die("缺少必要欄位");
    }

    $user_id = intval($_SESSION['user_id']);
    $movie_id = intval($_POST['movie_id']);
    $rating = floatval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    // ✅ 1. 插入主表 Reviews
    $stmt = $conn->prepare("INSERT INTO Reviews (user_id, movie_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $user_id, $movie_id, $rating, $review_text);
    $stmt->execute();

    // 取得 review_id
    $review_id = $conn->insert_id;

    // ✅ 2. 插入關聯表 mov_re
    $stmt2 = $conn->prepare("INSERT INTO mov_re (movie_id, review_id, user_id) VALUES (?, ?, ?)");
    $stmt2->bind_param("iii", $movie_id, $review_id, $user_id);
    $stmt2->execute();

    // ✅ 3. 導回電影頁面
    header("Location: movie_detail.php?movie_id=$movie_id");
    exit();
}
?>
