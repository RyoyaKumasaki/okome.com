<h2 class="has-text-left is-size-3">商品一覧</h2>
<div class="columns is-desktop is-multiline">
<?php
// ★修正点: $results が存在するかチェックし、表示するデータソースを決定する

if (isset($results) && !empty($results)) {
    // 検索結果 ($results) がある場合
    $data_to_display = $results;
} elseif (isset($results) && empty($results)) {
    // 検索結果が空の場合
    echo '<p class="notification is-warning">該当する商品は見つかりませんでした。</p>';
    $data_to_display = [];
} else {
    // 検索が行われていない（通常のページロード）場合、全商品を取得
    // status = 1 (アクティブ) の商品のみを取得
    $sql_all = $pdo->query('SELECT * FROM product WHERE status = 1'); 
    $data_to_display = $sql_all->fetchAll();
}

foreach($data_to_display as $row) : // ★データソースを $data_to_display に統一★
    $product_id = $row['product_id'];
    $product_name = $row['product_name'] ?? '';
    $quantity = $row['quantity'] ?? 0;
    $price = $row['price'] ?? 0;
    $product_explanation = $row['product_explanation'] ?? '';
    $product_picture = $row['product_picture'] ?? '';
?>
    <div class="column is-one-third-desktop is-half-tablet">
        <div class="card has-text-centered p-4 is-flex is-flex-direction-column" style="min-height: 280px;">
            <h3 class="title is-5"> <?= htmlspecialchars($product_name); ?> </h3>
            
            <div class="card-image is-flex is-justify-content-center mb-3">
                <figure class="image is-64x64"> 
                    <img src="img/products/<?= htmlspecialchars($product_picture); ?>" alt="<?= htmlspecialchars($product_name); ?>">
                </figure>
            </div>
            <p class="subtitle is-6 has-text-weight-bold mt-auto">価格：<?= number_format($price); ?>円</p>
            
            <form action="product-detail.php" method="post" class="mt-auto">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id); ?>">
                <input type="submit" class="button is-primary is-fullwidth" value="商品詳細を見る">
            </form>
        </div>
    </div>
<?php endforeach; ?>
</div>
<hr>