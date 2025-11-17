<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<h1>在庫一覧</h1>
<a href="show-stock.php"><button>更新</button></a>
<?php require 'stock-table.php'; ?>
<a href="stock-add.php">
    <button>商品追加</button>
</a>
<a href="top.php">
    <button>トップページへ戻る</button>
</a>
<?php require_once 'footer.php'?>