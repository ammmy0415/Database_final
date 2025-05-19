<?php include('header.php'); ?>

<?php

require_once('config.php'); // 連接資料庫

// 取得所有電影與其平均評分（若有）
$sql = "
    SELECT M.movie_id, M.title, M.director, M.release_date,
           ROUND(AVG(R.rating), 1) AS avg_rating
    FROM Movies M
    LEFT JOIN Reviews R ON M.movie_id = R.movie_id
    GROUP BY M.movie_id
    ORDER BY M.release_date DESC;
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>電影清單</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border-bottom: 1px solid #ccc; text-align: left; }
        th { background: #333; color: white; }
        a { text-decoration: none; color: #0066cc; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h1>🎬 電影清單</h1>

<table>
    <thead>
        <tr>
            <th>電影名稱</th>
            <th>導演</th>
            <th>上映日期</th>
            <th>平均評分</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><a href="movie_detail.php?id=<?= $row['movie_id'] ?>">
                <?= htmlspecialchars($row['title']) ?>
            </a></td>
            <td><?= htmlspecialchars($row['director']) ?></td>
            <td><?= htmlspecialchars($row['release_date']) ?></td>
            <td><?= $row['avg_rating'] ?? '尚無評分' ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
