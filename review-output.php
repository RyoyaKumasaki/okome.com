<?php session_start(); ?>
<?php require 'header.php'?>
<?PHP require 'menu.php'?>
<?php require 'db-connect.php' ?>

<div class="container is-max-desktop p-5 mt-5 has-background-white has-text-centered">

<?php
// PHPロジック部分
try{
    // ユーザーIDはセッションから取得、存在しない場合はエラー
    $user_id = $_SESSION['customer']['user_id'] ?? null;
    
    // ログインチェック（念のため）
    if (!$user_id) {
        // ログインしていない場合の通知
        echo '<div class="notification is-warning">';
        echo '<p>レビュー投稿にはログインが必要です。</p>';
        echo '<a href="login-input.php" class="button is-link is-light mt-3">ログインページへ</a>';
        echo '</div>';
        require 'footer.php';
        exit;
    }

    $product_id = $_POST['product_id'] ?? '';
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = $_POST['comment'] ?? '';

    // レビューの挿入
    $sql = $pdo->prepare('insert into review (product_id, user_id, rating, comment) values (?, ?, ?, ?)');
    $sql->execute([$product_id, $user_id, $rating, $comment]);

    // ------------------------------------------------------------------
    // 成功メッセージ表示
    // ------------------------------------------------------------------
    echo '<div class="box">';
    echo '<h1 class="title is-3 has-text-success mb-5">';
    echo '<span class="icon is-large"><i class="fas fa-check-circle"></i></span>';
    echo '<span> レビューを送信しました</span>';
    echo '</h1>';
    
    echo '<p class="subtitle is-5 mb-5">';
    echo '貴重なご意見ありがとうございました。';
    echo '</p>';
    
    echo '<hr>';

    // トップページへのリンク
    echo '<div class="mt-4">';
    echo '<a href="top.php" class="button is-link is-large is-rounded">';
    echo '<span class="icon"><i class="fas fa-home"></i></span>';
    echo '<span>トップページへ戻る</span>';
    echo '</a>';
    echo '</div>';

    echo '</div>'; // .box 終了

} catch (PDOException $e) {
    // ------------------------------------------------------------------
    // エラーメッセージ表示
    // ------------------------------------------------------------------
    echo '<div class="notification is-danger">';
    echo '<h1 class="title is-4">データベースエラー</h1>';
    echo '<p>レビューの投稿中に予期せぬエラーが発生しました。</p>';
    echo '<p class="is-size-7">詳細: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    echo '</div>';
    
    // トップページへのリンク
    echo '<div class="has-text-centered mt-4">';
    echo '<a href="top.php" class="button is-light is-medium">トップページへ戻る</a>';
    echo '</div>';
}
?>

</div>

<?php require 'footer.php'; ?>