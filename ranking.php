<h2 class="has-text-left is-size-3">
    ランキング
</h2>
<?php
require_once 'db-connect.php';
$sql = "
SELECT 
    p.product_id, 
    p.product_name, 
    p.quantity, 
    p.price, 
    p.status, 
    p.product_explanation, 
    p.product_picture, 
    p.producer_picture,
    AVG(r.rating) as avg_rating
FROM `LAA1607615-okome`.`product` p
JOIN (
    SELECT product_id
    FROM `LAA1607615-okome`.`review`
    GROUP BY product_id
    ORDER BY AVG(rating) DESC
    LIMIT 3
) top_products ON p.product_id = top_products.product_id
LEFT JOIN `LAA1607615-okome`.`review` r ON p.product_id = r.product_id
GROUP BY p.product_id;
";
$sql = $pdo->prepare($sql);
$sql->execute();
function displayStars($rating) {
    $rating = round($rating * 2) / 2; // 0.5刻みに丸める
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - ceil($rating);
    
    $stars = '';
    // 満星
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '★';
    }
    // 半星
    if ($halfStar) {
        $stars .= "\u{2BA}"    }
    // 空星
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '☆';
    }
    return $stars;
}

foreach ($sql as $row) {
    echo '<h3>' . htmlspecialchars($row['product_name']) . '</h3>';
    echo '<img src="img/' . htmlspecialchars($row['product_picture']) . '" width="150px"><br>';
    echo '<p>価格：' . htmlspecialchars($row['price']) . '円</p>';
    echo '<p style="color: #FFD700;">評価：' . displayStars($row['avg_rating']) . ' (' . number_format($row['avg_rating'], 1) . ')</p>';    
    echo '<form action="product-detail.php" method="post" style="display:inline;">';
    echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($row['product_id']) . '">';
    echo '<input type="submit" value="商品詳細を見る">';
    echo '<hr>';
    echo '</form>';
}
?>
