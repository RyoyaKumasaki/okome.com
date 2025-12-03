<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>

<div class="container is-max-desktop p-5 mt-5 has-background-white has-text-centered">
    <div class="box">
        <h1 class="title is-4 mb-4">ログアウト確認</h1>
        
        <p class="subtitle is-5 mb-5">
            現在ログイン中です。本当にログアウトしますか？
        </p>
        
        <div class="buttons is-centered">
            <a href="logout-output.php" class="button is-danger is-large is-rounded">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                <span>ログアウトする</span>
            </a>
            
            <a href="javascript:history.back()" class="button is-light is-large is-rounded">
                キャンセル
            </a>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>