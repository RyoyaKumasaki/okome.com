<?php require_once 'db-connect.php';?>
<table>
    <tr>
        <th>商品ID</th>
        <th>商品名</th>
        <th>在庫数</th>
    </tr>
    <?php
    $stmt = $pdo->query('select * from product');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '</tr>';
    }
