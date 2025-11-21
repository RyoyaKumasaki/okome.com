<?php require_once 'db-connect.php';?>
<table>
    <tr>
        <th>商品ID</th>
        <th>商品名</th>
        <th>在庫数</th>
        <th>ステータス</th>
        <th>操作</th>
    </tr>
    <?php
    $stmt = $pdo->query('select * from product');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<form action="stock-delete.php" method="post">';
        echo '<td>' . ($row['status'] == 1 ? '在庫有り' : '削除済み') . '</td>';
        echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8') . '">';
        if ($row['status'] == 1) {
            echo '<td><input type="submit" value="削除"></td>';
        }
        echo '</tr>';
    }
    ?>
    </form>
</table>
