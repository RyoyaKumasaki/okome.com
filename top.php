<?php session_start(); ?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<form action="product-detail.php" method="post">
    <div class="field">
        <p class="control has-icons-left">
            <input class="input" type="text" style="width: 600px;" name="product_id" placeholder="商品検索">
            <span class="icon is-small is-left">
                <i class="fas fa-search"></i>
            </span>
            <input type="submit" class="button is-outlined" value="検索">
            <a href="mypage.php"><i class="far fa-user-circle is-medium"></i></a>
            <!-- has-icon-right -->
            <!-- <a href="mypage.php"><img src="img/guest.png" width="150px"></a> -->
        </p>
    </div>
</form>
<?php require 'ranking.php'; ?>
<?php require 'product.php'; ?>
<?php require 'AI-concierge.php'; ?>
<?php require 'footer.php'; ?>
