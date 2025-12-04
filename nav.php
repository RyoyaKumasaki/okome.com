<form action="top.php" method="post" class="mt-5">
    <?php 
    // POSTされたデータがある場合のみ、検索ロジックを実行する
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
        $selected_kg = $_POST['capacity_kg'] ?? '';
        require 'product-search.php';
        $search_keyword = htmlspecialchars($_POST['product_name']);
        $capacity_text = !empty($selected_kg) ? " 容量: {$selected_kg}kg" : " (容量指定なし)";

        echo '<div class="notification is-info is-light mb-4">';
        echo '<p class="has-text-centered">';
        echo '<span class="icon is-small is-left">';
        echo '<i class="fas fa-search"></i>';
        echo '</span>';
        echo ' 商品一覧が更新されました！ ';
        echo "検索条件: 「{$search_keyword}」{$capacity_text}";
        echo '</p>';
        echo '</div>';
    }
    ?>
    <div class="field mb-4">
        <p class="is-size-6 mr-3 has-text-weight-semibold">容量で絞り込み:</p>
        <div class="control is-flex is-align-items-left">
            <label class="radio mr-4">
                <input type="radio" name="capacity_kg" value="" <?= (!isset($_POST['capacity_kg']) || $_POST['capacity_kg'] === '') ? 'checked' : '' ?>>
                指定なし
            </label>
            <label class="radio mr-4">
                <input type="radio" name="capacity_kg" value="1" <?= (isset($_POST['capacity_kg']) && $_POST['capacity_kg'] === '1') ? 'checked' : '' ?>>
                1kg
            </label>
            <label class="radio mr-4">
                <input type="radio" name="capacity_kg" value="5" <?= (isset($_POST['capacity_kg']) && $_POST['capacity_kg'] === '5') ? 'checked' : '' ?>>
                5kg
            </label>
            <label class="radio">
                <input type="radio" name="capacity_kg" value="10" <?= (isset($_POST['capacity_kg']) && $_POST['capacity_kg'] === '10') ? 'checked' : '' ?>>
                10kg
            </label>
        </div>
    </div>
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