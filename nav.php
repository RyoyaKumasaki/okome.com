<a href="product.php">商品</a>
<a href="favorite-show.php">お気に入り</a>
<a href="history.php">購入履歴</a>
<a href="cart-show.php">カート</a>
<a href="purchase-input.php">購入</a>
<a href="login-input.php">ログイン</a>
<a href="logout-input.php">ログアウト</a>
<a href="customer-input.php">会員登録</a>
<hr>
<form action="top.php" method="post">
    <div class="field level">
        <div class="control has-icons-left level-left">
            <span class="icon is-small is-left">
                <i class="fas fa-search"></i>
            </span>
            <input class="input" type="text" style="width: 600px;" name="product_name" placeholder="商品検索">
            <input type="submit" class="button is-outlined" value="検索">
        </div>
        <div class="has-text-right has-icon-right level-right">
            <a href="mypage.php" class=" is-size-4"><i class="far fa-user-circle"></i></a>
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