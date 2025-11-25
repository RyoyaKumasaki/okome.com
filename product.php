<h2 class="has-text-left is-size-3">商品一覧</h2>
<div class="columns is-desktop is-multiline">
<?php
$sql = $pdo->query('SELECT * FROM product');
foreach($sql as $row) : ?>
<?php
    $product_id = $row['product_id'];
    $product_name = $row['product_name'];
    $quantity = $row['quantity'];
    $price = $row['price'];
    $product_explanation = $row['product_explanation'];
    $product_picture = $row['product_picture'];
?>
    <div class="column is-one-third-desktop is-half-tablet">
        <div class="card has-text-centered p-4">
            <h3> <?= htmlspecialchars($product_name); ?> </h3>
            <img src="img/products/<?= htmlspecialchars($product_picture); ?>" width="150px"><br>
            <p>価格：<?= htmlspecialchars($price); ?>円</p>
            <form action="product-detail.php" method="post" class="mt-4">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id); ?>">
                <input type="submit" class="button is-primary" value="商品詳細を見る">
            </form>
        </div>
    </div>
<?php endforeach; ?>
</div>
<hr>

