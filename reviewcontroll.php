<?php session_start(); ?>
<?php
    require 'dbconnect.php';
    require 'controllheader.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>レビュー管理画面</title>
</head>
<body>
    <h2>レビュー管理画面</h2>
    <p>
        <?php
        foreach($sql as $row){
        $user_id = $row['user_id'];
        $rating = $row['rating'];
        $comment = $row['comment'];
        echo '<p>投稿者名：' . $user_id . '</p>';
        echo '<p>評価：' . str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . '</p>';
        echo '<p>レビュー内容：' . $comment . '</p>';
        echo '<hr>';
        }
        ?>
        <button type="submit">削除</button>
        <!--↓ここからレビューの削除を行う-->


        <!-- -->
        <form action="top.php">
            <button type="submit">トップへ戻る</button>
        </form>
    </p>
</body>
</html>