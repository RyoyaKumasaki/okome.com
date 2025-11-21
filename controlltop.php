<?php session_start(); ?>
<?php require 'dbconnect.php';
      require 'controllheader.php';
      require 'menu.php';
      require 'nav.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理者トップ画面</title>
</head>
<body>
    <h2>管理画面</h2>
    <form action="" method="post">
        <button type="submit">在庫管理</button>
    </form>
    <form action="" method="post">
        <button type="submit">レビュー管理</button>
    </form>
    <h2>アカウント検索</h2>
    <form action="" method="post">
    <input type="textbox" name="user_name" value="ユーザーID検索">
    <button type="submit">検索</button>
    </form>
    <form action="logout-input.php" method="post">
    <button type="submit">ログアウト</button>
    </form> 
</body>
</html>
<?php require 'footer.php'; ?>