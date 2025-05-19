
<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id'])) {
    die("請先登入才能評論");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $movie_id = intval($_POST['movie_id']);
    $rating = floatval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    if ($rating < 0 || $rating > 5) {
        die("評分必須在 0 到 5 之間");
    }

    $sql = "INSERT INTO Reviews (user_id, movie_id, rating, review_text, created_at)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iids", $user_id, $movie_id, $rating, $review_text);
    $stmt->execute();

    // 重新導回電影頁
    header("Location: movie_detail.php?id=" . $movie_id);
    exit();
}
?>
