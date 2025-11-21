<?php require 'controllheader.php'; ?>
<?php require 'admin-menu.php'; ?>
<h1>在庫一覧</h1>
<a href="stock-show.php"><button>更新</button></a>
<?php require 'stock-table.php'; ?>
<a href="stock-register.php">
    <button>商品追加</button>
</a>
<a href="controlltop.php">
    <button>トップページへ戻る</button>
</a>
<?php require_once 'footer.php'; ?>