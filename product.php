<?php
$sql = $pdo->query('SELECT * FROM product');
foreach($sql as $row){
    $product_id = $row['product_id'];
    $product_name = $row['product_name'];
    $quantity = $row['quantity'];
    $price = $row['price'];
    $product_explanation = $row['product_explanation'];
    $product_picture = $row['product_picture'];
    echo '<h3>' . htmlspecialchars($product_name) . '</h3>';
    echo '<img src="img/' . htmlspecialchars($product_picture) . '" width="150px"><br>';
    echo '<p>価格：' . htmlspecialchars($price) . '円</p>';
    echo '<form action="product-detail.php" method="post" style="display:inline;">';
    echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($product_id) . '">';
    echo '<input type="submit" value="商品詳細を見る">';
    echo '<hr>';
    echo '</form>';
}
?>
<hr>