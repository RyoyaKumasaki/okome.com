<?php
session_start();

// DB 接続
require_once 'db-connect.php';

// タイトル（header.php で使う用）
$page_title = '商品詳細';

// header, menu 読み込み
require 'header.php';
require 'menu.php';

// 商品 ID を取得
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// 商品情報を取得
$stmt = $pdo->prepare('SELECT * FROM product WHERE product_id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<section class='section'><div class='container'><p>商品が見つかりません。</p></div></section>";
    require 'footer.php';
    exit;
}
?>

<section class="section">
    <div class="container">

        <h1 class="title is-3 has-text-centered">
            <?= htmlspecialchars($product['product_name']) ?>
        </h1>

        <div class="columns is-centered">

            <!-- 左側：画像 -->
            <div class="column is-4">
                <figure class="image">
                    <img class="product-image"
                         src="img/<?= htmlspecialchars($product['product_image']) ?>"
                         alt="<?= htmlspecialchars($product['product_name']) ?>">
                </figure>
            </div>

            <!-- 右側：商品情報 -->
            <div class="column is-6">

                <div class="box">

                    <p class="price">
                        ￥<?= number_format($product['price']) ?>
                    </p>

                    <hr>

                    <h2 class="title is-5">商品説明</h2>
                    <p>
                        <?= nl2br(htmlspecialchars($product['product_explanation'] ?? '')) ?>
                    </p>

                    <hr>

                    <form action="cart-add.php" method="post">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

                        <div class="field">
                            <label class="label">個数</label>
                            <div class="control">
                                <input class="input" type="number" name="amount" value="1" min="1">
                            </div>
                        </div>

                        <div class="control">
                            <button class="button is-primary is-medium is-fullwidth">
                                カートに追加
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>

        <div class="has-text-centered">
            <a href="product-list.php" class="button is-light">← 商品一覧へ戻る</a>
        </div>

    </div>
</section>

<?php require 'footer.php'; ?>
