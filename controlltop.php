<?php session_start(); ?>
<?php if(!isset($_SESSION['admin'])) :
    header("Location: admin-login-input.php");
    exit;
?>
<?php endif; ?>
<?php $page_title = '管理者トップ画面'; ?>
<?php require 'controllheader.php'; ?>
<?php require 'admin-menu.php'; ?>

<section class="section pt-4 pb-2">
    <div class="container is-max-desktop">
        <div class="level is-mobile">
            <div class="level-left">
                <p class="subtitle is-6 has-text-grey">
                    ログイン中: <strong><?= htmlspecialchars($_SESSION['admin']['name'] ?? '管理者') ?></strong>
                </p>
            </div>
            <div class="level-right">
                <h1 class="title is-4 has-text-right"><?= htmlspecialchars($page_title) ?></h1>
            </div>
        </div>
        <hr class="mt-2 mb-6">
    </div>
</section>

<section class="section pt-0">
    <div class="container is-max-desktop">
        
        <h3 class="title is-4">管理機能</h3>
        
        <div class="box mb-4 has-background-primary-light">
            <p class="title is-5">在庫管理</p>
            <p class="subtitle is-6 mb-3">商品在庫の確認・編集を行います。</p>
            <form action="stock-show.php" method="post">
                <button type="submit" class="button is-primary is-fullwidth">在庫管理へ</button>
            </form>
        </div>

        <div class="box mb-6 has-background-info-light">
            <p class="title is-5">レビュー管理</p>
            <p class="subtitle is-6 mb-3">ユーザーレビューの確認・削除を行います。</p>
            <form action="reviewcontroll.php" method="post">
                <button type="submit" class="button is-info is-fullwidth">レビュー管理へ</button>
            </form>
        </div>

        <h3 class="title is-4">アカウント検索</h3>
        <div class="box is-narrow-container has-text-centered">
            <form action="account-management.php" method="post">
                <div class="field has-addons is-grouped is-grouped-centered">
                    <div class="control is-expanded">
                        <input class="input is-medium" type="text" name="user_name" placeholder="ユーザーIDを入力">
                    </div>
                    <div class="control">
                        <button type="submit" class="button is-dark is-medium">
                            <span>検索</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <hr class="mt-6">

        <div class="has-text-centered">
            <form action="admin-logout-input.php" method="post">
                <button type="submit" class="button is-danger is-outlined is-medium">
                    ログアウト
                </button>
            </form> 
        </div>

    </div>
</section>

<?php require 'footer.php'; ?>