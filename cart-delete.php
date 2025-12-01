<?php session_start(); ?>
<?php require 'header.php'?>
<?php require 'menu.php'?>
<?php require_once 'db-connect.php'?>

<div class="container is-max-desktop p-4">
    <?php
    // cart_detail_id が POST されているか確認
    if (!isset($_POST['cart_detail_id'])) {
        echo '<div class="notification is-danger">削除対象の商品が指定されていません。</div>';
    } else {
        try {
            // 削除処理の実行
            $sql = $pdo->prepare('DELETE FROM cart_detail WHERE cart_detail_id = ?');
            $sql->execute([$_POST['cart_detail_id']]);
            
            // 削除成功メッセージを Bulma の通知で表示
            echo '<div class="has-text-danger has-text-centered is-size-5 has-text-weight-bold p-3">';
            echo '商品をカートから削除しました。';
            echo '</div>';

        } catch (PDOException $e) {
            // データベースエラー時の表示
            echo '<div class="notification is-danger">';
            echo 'エラーが発生しました: データベースから商品を削除できませんでした。';
            echo '</div>';
            // エラーログなどへの記録を推奨
        }
    }
    ?>

    <hr>
    
    <?php require 'cart.php'; ?>
    
</div>

<?php require 'footer.php'?>