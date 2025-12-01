<?php $page_title = '在庫情報'; ?>
<?php require 'controllheader.php'; ?>
<?php require 'admin-menu.php'; ?>

<section class="section">
    <div class="container is-max-desktop">
        <h1 class="title is-3 has-text-centered mb-5">在庫一覧</h1>

        <div class="level is-mobile mb-4">
            
            <div class="level-left">
                <div class="level-item">
                    <a href="stock-show.php" class="button is-info is-outlined is-small">
                        <span class="icon"><i class="fas fa-sync-alt"></i></span>
                        <span>更新</span>
                    </a>
                </div>
            </div>
            
            <div class="level-right">
                <div class="level-item">
                    <a href="stock-register.php" class="button is-success is-small">
                        <span class="icon"><i class="fas fa-plus"></i></span>
                        <span>商品追加</span>
                    </a>
                </div>
            </div>
        </div>

        <?php require 'stock-table.php'; ?>
        
        <div class="has-text-centered mt-5">
            <a href="controlltop.php" class="button is-link is-light">管理者トップへ戻る</a>
        </div>
        
    </div>
</section>

<?php require_once 'footer.php'; ?>