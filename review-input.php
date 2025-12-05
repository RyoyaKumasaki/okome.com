<?php session_start(); ?>
<?php $page_title='商品レビュー'; ?>
<?php require 'header.php'?>
<?PHP require 'menu.php'?>
<?php require 'db-connect.php' ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="container is-max-desktop p-5">
    <?php
    if (!isset($_POST['product_id'])) {
        echo '<div class="notification is-danger">商品情報がありません。トップページからやり直してください。</div>';
        require 'footer.php';
        exit;
    }
    
    if (!isset($_SESSION['customer']['user_id'])) {
        echo '<div class="notification is-warning">レビューを投稿するには<a href="login-input.php">ログイン</a>が必要です。</div>';
        require 'footer.php';
        exit;
    }


    $product_id = $_POST['product_id'];

    $sql = $pdo->prepare('select * from product where product_id = ?');
    $sql->execute([$product_id]);
    $product = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo '<div class="notification is-danger">指定された商品が見つかりません。</div>';
        require 'footer.php';
        exit;
    }
    ?>

    <h2 class="title is-4 has-text-centered">商品レビューを投稿する</h2>
    
    <div class="box">
        <div class="media">
            <div class="media-left">
                <!-- 商品画像 -->
                <figure class="image is-96x96">
                    <img src="img/products/<?= htmlspecialchars($product['product_picture']); ?>" 
                         alt="<?= htmlspecialchars($product['product_name']); ?>" 
                         style="max-width:96px; height:auto; object-fit: cover;">
                </figure>
            </div>
            <div class="media-content">
                <p class="title is-5"><?= htmlspecialchars($product['product_name']); ?></p>
                <p class="subtitle is-6">商品ID: <?= htmlspecialchars($product['product_id']); ?></p>
            </div>
        </div>

        <hr>

        <form action="review-output.php" method="post">
            
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
            
            <div class="field">
                <label class="label">評価点 (1〜5)</label>
                <div class="control">
                    <div class="rating-stars is-flex is-align-items-center">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="mr-2">
                            <input type="radio" name="rating" value="<?= $i ?>" required style="display: none;">
                            <span class="icon is-medium has-text-warning star-icon" data-rating="<?= $i ?>">
                                <i class="fas fa-star"></i>
                            </span>
                        </label>
                    <?php endfor; ?>
                    <span id="rating-text" class="ml-3 is-size-5 has-text-weight-bold has-text-info">評価を選択してください</span>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label">コメント</label>
                <div class="control">
                    <textarea class="textarea" name="comment" rows="4" placeholder="商品の良い点、悪い点などを具体的にご記入ください。"></textarea>
                </div>
            </div>

            <div class="field is-grouped is-justify-content-flex-end">
                <div class="control">
                    <input type="submit" value="レビューを投稿" class="button is-primary is-medium">
                </div>
            </div>
    <div class="mt-5 has-text-centered">
        <a href="top.php" class="button is-link is-light">
            <span class="icon"><i class="fas fa-home"></i></span>
            <span>トップ画面へ戻る</span>
        </a>
    </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.star-icon');
    const ratingText = document.getElementById('rating-text');
    const form = document.querySelector('form');

    // フォーム送信時にラジオボタンが選択されているか確認
    form.addEventListener('submit', (e) => {
        if (!document.querySelector('input[name="rating"]:checked')) {
            alert('評価点を選択してください。');
            e.preventDefault();
        }
    });

    stars.forEach(star => {
        const rating = parseInt(star.dataset.rating);
        const radio = star.previousElementSibling;

        star.parentNode.addEventListener('mouseover', () => {
            highlightStars(rating, true);
        });

        star.parentNode.addEventListener('mouseout', () => {
            const checkedStar = document.querySelector('input[name="rating"]:checked');
            if (checkedStar) {
                const checkedRating = parseInt(checkedStar.value);
                highlightStars(checkedRating, false);
            } else {
                resetStars();
            }
        });

        // 星をクリックしたときの処理 (ラジオボタンの変更)
        star.parentNode.addEventListener('click', () => {
            radio.checked = true;
            highlightStars(rating, false);
        });
        
        // ラジオボタンの状態を監視
        radio.addEventListener('change', () => {
            highlightStars(rating, false);
        });
    });

    function highlightStars(count, isHover) {
        stars.forEach((star, index) => {
            if (index < count) {
                // 選択またはホバーされた星を塗りつぶし
                star.querySelector('i').classList.add('has-text-warning');
                star.querySelector('i').classList.remove('has-text-grey-light');
            } else {
                // 未選択の星を灰色にする
                star.querySelector('i').classList.remove('has-text-warning');
                star.querySelector('i').classList.add('has-text-grey-light');
            }
        });
        
        // テキストの更新 (ホバー中は更新しない)
        if (!isHover) {
            ratingText.textContent = `${count}点`;
        }
    }
    
    function resetStars() {
        stars.forEach(star => {
            star.querySelector('i').classList.remove('has-text-warning');
            star.querySelector('i').classList.add('has-text-grey-light');
        });
        ratingText.textContent = '評価を選択してください';
    }
    
    // 初期状態をリセット
    resetStars();
});
</script>

<?php require 'footer.php'; ?>