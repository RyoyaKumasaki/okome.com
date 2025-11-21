<?php session_start(); ?>
<?php require 'controllheader.php'; ?>
<?php require 'admin-menu.php'; ?>
<?php $page_title = '管理者トップ画面'; ?>
    <h2>管理画面</h2>
    <form action="stock-show.php" method="post">
        <button type="submit">在庫管理</button>
    </form>
    <form action="" method="post">
        <button type="submit">レビュー管理</button>
    </form>
    <h2>アカウント検索</h2>
    <form action="account-management.php" method="post">
    <input type="textbox" name="user_name" placeholder="ユーザーID検索">
    <button type="submit">検索</button>
    </form>
    <form action="logout-input.php" method="post">
    <button type="submit">ログアウト</button>
    </form> 
</body>
</html>
<?php require 'footer.php'; ?>