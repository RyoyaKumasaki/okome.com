<?php session_start(); ?>
<?php $page_title = '会員情報'; ?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>

<section class="section">
    <div class="container is-max-desktop">
        <div class="box">
            
            <?php
            // 変数の初期化 ( Bulma クラス適用のため、HTML構造に影響しない PHP のみを使用 )
            $name = $address = $login_name = $password = $mail = $telephone_number = '';
            $is_logged_in = isset($_SESSION['customer']);

            if ($is_logged_in) {
                // ログイン中の場合（会員情報編集）
                $user_id = $_SESSION['customer']['user_id'];
                $mail = $_SESSION['customer']['mail'];
                $name = $_SESSION['customer']['name'];
                $address = $_SESSION['customer']['address'];
                $login_name = $_SESSION['customer']['login_name'];
                $telephone_number = $_SESSION['customer']['telephone_number'] ?? ''; // 電話番号がセッションにない可能性を考慮

                echo '<h1 class="title is-3 has-text-centered">会員情報編集</h1>';
                echo '<p class="subtitle is-6 has-text-centered mb-5">必要な情報を編集し、「確定」ボタンを押してください。</p>';
                $form_action = 'customer-output.php'; // 編集処理への遷移
            } else {
                // 未ログインの場合（新規会員登録）
                echo '<h1 class="title is-3 has-text-centered">新規会員登録</h1>';
                echo '<p class="subtitle is-6 has-text-centered mb-5">アカウント作成のため、必要事項を入力し、「確定」ボタンを押してください。</p>';
                $form_action = 'customer-output.php'; // 新規登録処理への遷移
            }

            // ----------------------------------------------------
            // フォームの描画
            // ----------------------------------------------------
            ?>
            <form action="<?= $form_action ?>" method="post">
                
                <div class="field">
                    <label class="label">お名前</label>
                    <div class="control">
                        <input class="input" type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">ご住所</label>
                    <div class="control">
                        <input class="input" type="text" name="address" value="<?= htmlspecialchars($address) ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">ログイン名</label>
                    <div class="control">
                        <input class="input" type="text" name="login_name" value="<?= htmlspecialchars($login_name) ?>" required>
                    </div>
                </div>

                <?php if (!$is_logged_in): ?>
                <div class="field">
                    <label class="label">パスワード</label>
                    <div class="control">
                        <input class="input" type="password" name="password" value="" required>
                    </div>
                </div>
                <?php endif; ?>

                <div class="field">
                    <label class="label">メールアドレス</label>
                    <div class="control">
                        <input class="input" type="email" name="mail" value="<?= htmlspecialchars($mail) ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">電話番号</label>
                    <div class="control">
                        <input class="input" type="tel" name="telephone_number" value="<?= htmlspecialchars($telephone_number); ?>" pattern="[0-9]{3,4}-[0-9]{2,4}-[0-9]{3,4}" placeholder="例: 03-1234-5678">
                    </div>
                </div>

                <div class="field mt-6 has-text-centered">
                    <div class="control">
                        <input class="button is-primary is-medium is-fullwidth" type="submit" value="確定">
                    </div>
                </div>
            </form>
            
            <?php if ($is_logged_in): ?>
            <hr>
            <div class="has-text-centered">
                <p class="mb-3">
                    <a href="forgot-password.php" class="has-text-info">パスワードを変更したい方はこちら</a>
                </p>
                <form action="mypage.php" method="get">
                    <button type="submit" class="button is-link is-light is-small">マイページへ戻る</button>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php require 'footer.php'; ?>