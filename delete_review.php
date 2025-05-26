<?php
session_start();
require_once('config.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ 權限檢查：是否登入
if (!isset($_SESSION['user_id'])) {
    die("未登入");
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';

// ✅ 表單送出檢查
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['review_id']) || !isset($_POST['movie_id'])) {
        die("缺少必要參數");
    }

    $review_id = intval($_POST['review_id']);
    $movie_id = intval($_POST['movie_id']);
    $user_id = $_SESSION['user_id'];

    // 驗證這篇評論是否屬於目前登入者
    $check_sql = "SELECT * FROM Reviews WHERE review_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $review_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // 是否為管理員
    $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';

    // 若是自己的評論或是身分為管理員才允許刪除
    if ($result->num_rows === 1 || $is_admin) {
        $del_mov_re_sql = "DELETE FROM mov_re WHERE review_id = ?";
        $del_mov_stmt = $conn->prepare($del_mov_re_sql);
        $del_mov_stmt->bind_param("i", $review_id);
        $del_mov_stmt->execute();
        
        $del_rev_user_sql = "DELETE FROM rev_user WHERE review_id = ?";
        $del_re_stmt = $conn->prepare($del_rev_user_sql);
        $del_re_stmt->bind_param("i", $review_id);
        $del_re_stmt->execute();

        $del_sql = "DELETE FROM Reviews WHERE review_id = ?";
        $del_stmt = $conn->prepare($del_sql);
        $del_stmt->bind_param("i", $review_id);
        $del_stmt->execute();
    }

    // ✅ 返回電影詳細頁
    header("Location: movie_detail.php?movie_id=$movie_id");
    exit();
}

echo "無效的請求方式";
?>