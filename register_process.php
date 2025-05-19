
<?php
session_start();
require_once('config.php');

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// 檢查帳號是否已存在
$check_sql = "SELECT * FROM Users WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    header('Location: register.php?error=帳號已存在');
    exit();
}

// 註冊帳號（預設角色為 User，未加密密碼）
$insert_sql = "INSERT INTO Users (username, email, password_hash, role, created_at) VALUES (?, ?, ?, 'User', NOW())";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("sss", $username, $email, $password);
$insert_stmt->execute();

if ($insert_stmt->affected_rows === 1) {
    $_SESSION['username'] = $username;
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['role'] = 'User';
    header('Location: index.php');
} else {
    header('Location: register.php?error=註冊失敗');
}
?>
