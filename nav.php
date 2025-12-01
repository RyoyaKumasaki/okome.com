<form action="top.php" method="post" class="mt-5">
    <?php 
    // POSTされたデータがある場合のみ、検索ロジックを実行する
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
        require 'product-search.php';
    }
    ?>

    <div class="field level">
        <div class="control has-icons-left level-left">
            <span class="icon is-small is-left">
                <i class="fas fa-search"></i>
            </span>
            <input class="input mr-2" type="text" style="width: 600px;" name="product_name" placeholder="商品検索">
            <input type="submit" class="button is-light is-active" value="検索">
        </div>
        <div class="has-text-right has-icon-right level-right">
            <a href="cart-show.php" class=" is-size-4"><i class="fas fa-shopping-cart mr-3"></i></a>
            <a href="mypage.php" class=" is-size-4"><i class="far fa-user-circle mr-2"></i></a>
            <?php if(isset($_SESSION['customer'])) : ?>
                <label>こんにちは、<?=$_SESSION['customer']['name']; ?>さん</label>
            <?php else : ?>
                <label>こんにちは、ゲストさん</label>
            <?php endif; ?>
        </div>
            <!-- has-icon-right -->
            <!-- <a href="mypage.php"><img src="img/guest.png" width="150px"></a> -->
    </div>
</form>