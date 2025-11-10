<h2>
    ランキング
</h2>
<?php
require_once 'db-connect.php';
$sql = "
SELECT p.product_id, p.product_name, p.quantity, p.price, p.status, p.product_explanation, p.product_picture, p.producer_picture
FROM `LAA1607615-okome`.`product` p
JOIN (
  SELECT product_id
  FROM `LAA1607615-okome`.`review`
  GROUP BY product_id
  ORDER BY AVG(rating) DESC
  LIMIT 3
) top_products ON p.product_id = top_products.product_id;
";
$sql = $pdo->prepare($sql);
$sql->execute();
foreach ($sql as $row) {
    echo '<h3>' . htmlspecialchars($row['product_name']) . '</h3>';
    echo '<img src="images/' . htmlspecialchars($row['product_picture']) . '" width="150px"><br>';
    echo '<p>価格：' . htmlspecialchars($row['price']) . '円</p>';
    echo '<form action="product-detail.php" method="post" style="display:inline;">';
    echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($row['product_id']) . '">';
    echo '<input type="submit" value="商品詳細を見る">';
    echo '<hr>';
    echo '</form>';
}
?>
