
<?php
session_start();
require_once('config.php');

// 若未登入則導回登入頁
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 檢查必要參數
if (!isset($_GET['review_id']) || !isset($_GET['movie_id'])) {
    header("Location: index.php");
    exit();
}

$review_id = intval($_GET['review_id']);
$movie_id = intval($_GET['movie_id']);
$user_id = $_SESSION['user_id'];

// 驗證這篇評論是否屬於目前登入者
$check_sql = "SELECT * FROM Reviews WHERE review_id = ? AND user_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// 若是自己的評論才允許刪除
if ($result->num_rows === 1) {
    $del_sql = "DELETE FROM Reviews WHERE review_id = ?";
    $del_stmt = $conn->prepare($del_sql);
    $del_stmt->bind_param("i", $review_id);
    $del_stmt->execute();
}

// 無論是否成功，回到原本電影頁面
header("Location: movie_detail.php?id=" . $movie_id);
exit();
?>
