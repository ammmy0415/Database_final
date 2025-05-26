<?php
// config.php — 在本機測試時用這個版本
$servername = "localhost";
$username = "root";
$password = "00000000"; // 預設 MAMP 密碼
$dbname = "movie";

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn->set_charset("utf8mb4")) {
    die("無法設定編碼: " . $conn->error);
}

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}
?>
