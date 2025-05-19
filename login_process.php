
<?php
session_start();
require_once('config.php');

$username = $_POST['username'];
$password = $_POST['password'];

// 假設密碼未加密（測試階段），實際應該用 password_hash
$sql = "SELECT * FROM Users WHERE username = ? AND password_hash = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);  // 未加密的處理
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    header('Location: index.php');
} else {
    header('Location: login.php?error=1');
}
?>
