<!-- index.php -->
<?php include('header.php'); ?>
<?php
//session_start();
require_once('config.php');

// 判斷是否為 Admin
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin');

// 搜尋功能處理
$search = $_GET['search'];
if (!empty($search)) {
    $search_param = '%' . $search . '%';
    $stmt = $conn->prepare("
        SELECT M.movie_id, M.title, M.director, M.release_date,
               ROUND(AVG(R.rating), 1) AS avg_rating
        FROM Movies M
        LEFT JOIN Reviews R ON M.movie_id = R.movie_id
        WHERE M.title LIKE ? OR M.director LIKE ?
        GROUP BY M.movie_id
        ORDER BY M.release_date DESC;
    ");
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // 原本未搜尋的情況
    $sql = "
        SELECT M.movie_id, M.title, M.director, M.release_date,
               ROUND(AVG(R.rating), 1) AS avg_rating
        FROM Movies M
        LEFT JOIN Reviews R ON M.movie_id = R.movie_id
        GROUP BY M.movie_id
        ORDER BY M.release_date DESC;
    ";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>電影清單</title>
    <link rel="stylesheet" href="style/index.css">
    <!--style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ccc; text-align: left; }
        th { background: #333; color: white; }
        a { text-decoration: none; color: #0066cc; }
        a:hover { text-decoration: underline; }
        .btn-delete { color: red; margin-left: 10px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
        .add-button {
            padding: 8px 16px;
            background-color: #0066cc;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
    </style-->
</head>
<body>

<div class="top-bar">
    <h1>🎬 電影清單</h1>
    <?php if ($isAdmin): ?>
        <a class="add-button" href="add_movie.php">➕ 新增電影</a>
    <?php endif; ?>
</div>

<table>
    <thead>
        <tr>
            <th>電影名稱</th>
            <th>導演</th>
            <th>上映日期</th>
            <th>平均評分</th>
            <?php if ($isAdmin): ?>
                <th>操作</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="<?= $isAdmin ? 5 : 4 ?>" style="text-align: center; color: #888;">
                    找不到相關電影或導演
                </td>
            </tr>
        <?php endif; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>
                <a href="movie_detail.php?movie_id=<?= $row['movie_id'] ?>">
                    <?= htmlspecialchars($row['title']) ?>
                </a>
            </td>
            <td><?= htmlspecialchars($row['director']) ?></td>
            <td><?= htmlspecialchars($row['release_date']) ?></td>
            <td><?= $row['avg_rating'] ?? '尚無評分' ?></td>
            <?php if ($isAdmin): ?>
            <td>
                <a class="btn-delete" href="delete_movie.php?id=<?= $row['movie_id'] ?>"
                   onclick="return confirm('確定要刪除此電影嗎？')">刪除</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
