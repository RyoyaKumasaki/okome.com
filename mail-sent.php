<?php
$page_title = 'メール送信完了';
require 'header.php';
require 'menu.php';
?>

<section class="section">
    <div class="container is-max-desktop">
        <div class="box has-text-centered p-6">
            
            <h1 class="title is-4 has-text-primary">メール送信完了</h1>

            <div class="notification is-warning is-light mb-5 p-5">
                <p class="subtitle is-5">
                    パスワード再設定用のメールを送信しました。
                </p>
                <hr class="my-3">
                <p class="has-text-danger has-text-weight-bold">
                    ご注意：パスワードの再設定はまだ完了していません。
                </p>
                <p class="is-size-6 mt-3">
                    届いたメールに記載されているリンクをクリックし、パスワード再設定を完了させてください。
                </p>
            </div>

            <form action="login-input.php" method="get">
                <div class="control">
                    <button type="submit" class="button is-link is-medium">ログイン画面に戻る</button>
                </div>
            </form>

        </div>
    </div>
</section>

<?php require 'footer.php'; ?>