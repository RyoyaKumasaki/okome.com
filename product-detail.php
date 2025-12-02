<?php
session_start();

$page_title = '商品画面';
require 'header.php';
require 'menu.php';
require_once 'db-connect.php';
?>

<div class="section">
    <div class="container">

        <a href="top.php" class="button is-light mb-5">← トップ画面へ戻る</a>

        <?php
        // 商品ID取得
        $product_id = $_POST['product_id'] ?? null;

        if (empty($product_id) || !isset($pdo)) {
            echo '<div class="notification is-danger">商品IDが指定されていないか、データベース接続に失敗しています。</div>';
            echo '</div></div>';
            exit;
        }

        // 商品取得
        $sql = $pdo->prepare('SELECT * FROM product WHERE product_id = ?');
        $sql->execute([$product_id]);
        $product = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo '<div class="notification is-danger">商品が見つかりませんでした。</div>';
            echo '</div></div>';
            exit;
        }

        $product_name = $product['product_name'];
        $quantity = $product['quantity'];
        $price = $product['price'];
        $product_explanation = $product['product_explanation'];
        $product_picture = $product['product_picture'];
        $producer_picture = $product['producer_picture'];
        ?>

        <h2 class="title is-3 has-text-centered">
            <?= htmlspecialchars($product_name) ?>
        </h2>

        <div class="columns is-centered">

            <!-- 左側：商品画像 -->
            <div class="column is-5">
                <figure class="image is-4by3">
                    <img src="img/products/<?= htmlspecialchars($product_picture) ?>" alt="商品画像">
                </figure>
            </div>

            <!-- 右側：商品説明とカート -->
            <div class="column is-6">

                <div class="box">

                    <p class="title is-4 has-text-danger">
                        価格：<?= number_format($price) ?>円
                    </p>

                    <p class="subtitle is-6">
                        在庫数：<?= htmlspecialchars($quantity) ?> 個
                    </p>

                    <hr>

                    <!-- カート追加フォーム -->
                    <form action="cart-insert.php" method="post">
                        <div class="field">
                            <label class="label">購入個数</label>
                            <div class="control">
                                <div class="select">
                                    <select name="buy_quantity">
                                        <? if($quantity == 0): ?>
                                            <option value="0">現在在庫切れです</option>
                                        <?php else: ?>
                                        <?php for ($i = 1; $i <= $quantity; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?>個</option>
                                        <?php endfor; ?>
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
                        <div class="control">
                            <button class="button is-primary is-fullwidth mt-3">
                                カートに入れる
                            </button>
                        </div>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">

                        <div class="control">
                            <button class="button is-primary is-fullwidth mt-3">
                                カートに入れる
                            </button>
                        </div>
                    </form>

                </div>

                <!-- 生産者情報 -->
                <div class="box has-background-link-light">
                    <h3 class="title is-5">生産者</h3>
                    <figure class="image is-96x96">
                        <img class="is-rounded" src="img/products/<?= htmlspecialchars($producer_picture) ?>" alt="生産者画像">
                    </figure>
                </div>

            </div>
        </div>

        <!-- 商品説明 -->
        <div class="box mt-6">
            <h3 class="title is-4">商品について</h3>
            <p><?= nl2br(htmlspecialchars($product_explanation ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
        </div>

        <!-- レビュー一覧 -->
        <div class="box mt-6">
            <h3 class="title is-4">レビュー一覧</h3>

            <?php
            $sql = $pdo->prepare('SELECT * FROM review WHERE product_id = ?');
            $sql->execute([$product_id]);

            foreach ($sql as $row):
                $user_id = $row['user_id'] ?? '名無し';
                $rating = $row['rating'] ?? 0;
                $comment = $row['comment'] ?? '';
            ?>
                <div class="box">
                    <p class="has-text-weight-bold">投稿者：<?= htmlspecialchars($user_id) ?></p>
                    <p class="has-text-warning">
                        評価：
                        <?= str_repeat('★', $rating) ?>
                        <?= str_repeat('☆', 5 - $rating) ?>
                    </p>
                    <p><?= nl2br(htmlspecialchars($comment)) ?></p>
                </div>
            <?php endforeach; ?>

        </div>

    </div>
</div>

<?php require 'footer.php'; ?>
