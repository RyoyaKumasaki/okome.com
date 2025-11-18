<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';
?>

<h1>購入履歴</h1>

<?php if (isset($_SESSION['customer'])) : ?>
    <?php
    $customer = $_SESSION['customer'];
    ?>

    <p>ユーザーID:<?= htmlspecialchars($customer['name']) ?></p>
    <p>住所:<?= htmlspecialchars($customer['address']) ?></p>
    <hr>

    <?php
    $pdo = new PDO($connect, USER, PASS);
    $sql = $pdo->prepare('SELECT * FROM `Order` WHERE user_id=? ORDER BY order_id DESC');
    $sql->execute([$customer['user_id']]);
    ?>

    <?php foreach ($sql as $row) : ?>
        <h3>購入日：<?= htmlspecialchars($row['created_at']) ?></h3>
        <table>
            <tr>
                <th>商品番号</th>
                <th>商品名</th>
                <th>価格</th>
                <th>個数</th>
                <th>小計</th>
            </tr>
            <?php
            $products = $pdo->prepare('
                SELECT * FROM purchase_detail 
                JOIN product ON purchase_detail.product_id = product.product_id 
                WHERE purchase_id=?');
            $products->execute([$row['id']]);
            $total = 0;
            foreach ($products as $product) :
                $subtotal = $product['price'] * $product['count'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($product['product_id']); ?></td>
                    <td><?= htmlspecialchars($product['product_name']); ?></td>
                    <td><?= htmlspecialchars($product['price']); ?>円</td>
                    <td><?= htmlspecialchars($product['count']); ?></td>
                    <td><?= htmlspecialchars($subtotal); ?>円</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" style="text-align:right;"><strong>合計</strong></td>
                <td><strong><?= htmlspecialchars($total); ?>円</strong></td>
            </tr>
        </table>
        <hr>
    <?php endforeach; ?>

    <p><a href="top.php">トップ画面へ戻る</a></p>

<?php else : ?>
    <p>ログインしてください。</p>
<?php endif; ?>

<?php require 'footer.php'; ?>
