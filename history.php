<?php
session_start();
?>
<?php if (!isset($_SESSION['customer'])) : ?>
<?php
header("Location: login-input.php");
exit;
?>
<?php else : ?>
    <?php
    require 'db-connect.php';
    $page_title = '購入履歴'; // ページタイトルを追加
    require 'header.php';
    require 'menu.php';
    $customer = $_SESSION['customer'];
    ?>
    
<section class="section">
    <div class="container is-max-desktop">
        
        <h1 class="title is-3 has-text-centered">購入履歴</h1>
        
        <div class="box mb-5 has-background-light">
            <p class="subtitle is-6 mb-1">
                <span class="has-text-weight-bold">ユーザー名 (ID):</span> <?= htmlspecialchars($customer['name']) ?>
            </p>
            <p class="subtitle is-6 mb-0">
                <span class="has-text-weight-bold">住所:</span> <?= htmlspecialchars($customer['address']) ?>
            </p>
        </div>

        <?php
        $sql = $pdo->prepare('SELECT * FROM `order` WHERE user_id=? ORDER BY order_id DESC');
        $sql->execute([$customer['user_id']]);
        ?>

        <?php if ($sql->rowCount() > 0) : ?>
            <?php foreach ($sql as $row) : ?>
                <div class="box mb-5">
                    <h3 class="title is-5 has-text-primary">
                        購入日：<?= htmlspecialchars($row['date']) ?> (注文ID: <?= htmlspecialchars($row['order_id']) ?>)
                    </h3>
                    
                    <div class="table-container">
                        <table class="table is-striped is-fullwidth is-hoverable">
                            <thead>
                                <tr>
                                    <th class="has-text-centered">商品番号</th>
                                    <th>商品名</th>
                                    <th></th>
                                    <th class="has-text-right">価格</th>
                                    <th class="has-text-centered">個数</th>
                                    <th class="has-text-right">小計</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $products = $pdo->prepare('
                                    SELECT * FROM order_detail 
                                    JOIN product ON order_detail.product_id = product.product_id 
                                    WHERE order_id=?');
                                $products->execute([$row['order_id']]); 
                                $total = 0;
                                foreach ($products as $product) :
                                    $subtotal = $product['price'] * $product['count'];
                                    $total += $subtotal;
                                ?>
                                    <tr>
                                        <td class="has-text-centered"><?= htmlspecialchars($product['product_id']); ?></td>
                                        <td><?= htmlspecialchars($product['product_name']); ?></td>
                                        <td class="has-text-centered">
                                        <form action="review-input.php" method="post">
                                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                                        <input type="submit" class="button" value="レビュー投稿">
                                        </form>
                                        </td>
                                        <td class="has-text-right"><?= number_format($product['price']); ?>円</td>
                                        <td class="has-text-centered"><?= htmlspecialchars($product['count']); ?></td>
                                        <td class="has-text-right"><?= number_format($subtotal); ?>円</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="has-text-right is-size-5"><strong>合計</strong></td>
                                    <td class="has-text-right is-size-5 has-text-weight-bold has-text-danger"><?= number_format($total); ?>円</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
             <div class="notification is-info is-light has-text-centered">
                購入履歴はありません。
             </div>
        <?php endif; ?>

        <div class="has-text-centered mt-6">
            <a href="top.php" class="button is-link is-light">トップ画面へ戻る</a>
        </div>
        
    </div>
</section>

<?php endif; ?>

<?php require 'footer.php'; ?>