<?php 
require_once 'db-connect.php'; 
?>

<div class="box">
    <div class="table-container">
        <table class="table is-striped is-fullwidth is-hoverable">
            <thead>
                <tr>
                    <th class="has-text-centered">商品ID</th>
                    <th>商品名</th>
                    <th class="has-text-right">在庫数</th>
                    <th class="has-text-centered">ステータス</th>
                    <th class="has-text-centered">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query('select * from product ORDER BY product_id ASC'); 
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $product_id = htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8');
                    $product_name = htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8');
                    $quantity = htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8');
                    $status = (int)$row['status'];
                    
                    echo '<tr>';
                    echo '<td class="has-text-centered">' . $product_id . '</td>';
                    echo '<td>' . $product_name . '</td>';
                    echo '<td class="has-text-right">' . $quantity . '</td>';

                    // ステータス表示
                    if ($status == 1) {
                        echo '<td class="has-text-centered"><span class="tag is-success is-light">在庫有り</span></td>';
                    } else {
                        echo '<td class="has-text-centered"><span class="tag is-danger is-light">削除済み</span></td>';
                    }
                    
                    // 操作セル
                    echo '<td class="has-text-centered">';
                    echo '<form action="stock-delete.php" method="post" style="display:inline;">';
                    echo '<input type="hidden" name="product_id" value="' . $product_id . '">';
                    
                    if ($status == 1) {
                        echo '<button type="submit" class="button is-danger is-small">削除</button>';
                    } else {
                        echo '&mdash;'; 
                    }
                    
                    echo '</form>';
                    echo '</td>';
                    
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>