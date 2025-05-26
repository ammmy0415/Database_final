<?php
session_start();
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("你沒有權限操作");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['still_id']) && isset($_POST['movie_id'])) {
    $still_id = intval($_POST['still_id']);
    $movie_id = intval($_POST['movie_id']);

    // 先手動刪除關聯表 mov_still 裡的記錄
    $stmt = $conn->prepare("DELETE FROM mov_still WHERE still_id = ?");
    $stmt->bind_param("i", $still_id);
    if (!$stmt->execute()) {
        die("❌ 無法刪除 mov_still 關聯：" . $stmt->error);
    }

    // 再刪除 MovieStills
    $stmt = $conn->prepare("DELETE FROM MovieStills WHERE still_id = ?");
    $stmt->bind_param("i", $still_id);
    if (!$stmt->execute()) {
        die("❌ 刪除 MovieStills 失敗：" . $stmt->error);
    }

    header("Location: edit_stills.php?movie_id=$movie_id");
    exit();
} else {
    echo "請求資料不完整";
}
