<?php
$page_title = 'パスワード変更完了';
require 'header.php';
require 'menu.php';
?>

<div class="container is-max-desktop p-5 mt-5 has-background-white has-text-centered">
    <div class="box">
        <h1 class="title is-3 has-text-success mb-5">
            <span class="icon is-large"><i class="fas fa-check-circle"></i></span>
            <span>パスワードの変更が完了しました</span>
        </h1>
        
        <p class="subtitle is-5 mb-5">
            下記からログイン画面に戻り、新しいパスワードでログインしてください。
        </p>

        <hr>
        
        <form action="login-input.php" method="get">
            <button type="submit" class="button is-primary is-large is-rounded">
                <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
                <span>ログイン画面に戻る</span>
            </button>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>