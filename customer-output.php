<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php $page_title = '会員情報'; ?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>

<section class="section">
    <div class="container is-max-desktop">
        
        <?php
        // ログイン名の重複チェック（自分自身を除く）
        if (isset($_SESSION['customer'])) {
            $user_id = $_SESSION['customer']['user_id'];
            $sql = $pdo->prepare('SELECT * FROM customer_user WHERE user_id!=? AND login_name=?');
            $sql->execute([$user_id, $_POST['login_name']]);
        } else {
            // 新規登録時の重複チェック
            $sql = $pdo->prepare('SELECT * FROM customer_user WHERE login_name=?');
            $sql->execute([$_POST['login_name']]);
        }

        if (empty($sql->fetchAll())) {
            // ----------------------------------------------------
            // A. ログイン名が重複していない場合（登録/更新成功）
            // ----------------------------------------------------
            
            if (isset($_SESSION['customer'])) {
                // 既存ユーザーの更新処理 (処理ロジックは省略)
                $sql = $pdo->prepare('UPDATE customer_user SET mail=?, name=?, 
                                     address=?, login_name=?, telephone_number=? WHERE user_id=?');
                $sql->execute([
                    $_POST['mail'], $_POST['name'], $_POST['address'],
                    $_POST['login_name'], $_POST['telephone_number'], $user_id]);
                
                // セッションの更新 (省略)
                $_SESSION['customer'] = [
                    'user_id'=>$user_id, 'mail'=>$_POST['mail'],
                    'name'=>$_POST['name'], 'address'=>$_POST['address'],
                    'login_name'=>$_POST['login_name'], 'telephone_number'=>$_POST['telephone_number']];
                
                $message = 'お客様情報を更新しました。';
                $is_success = true;
                $link_text = 'マイページに戻る';
                $link_url = 'mypage.php';

            } else {
                // 新規ユーザーの登録処理 (処理ロジックは省略)
                $sql = $pdo->prepare('INSERT INTO customer_user
                    (mail, password, name, address, login_name, telephone_number)
                    VALUES (:mail, :password, :name, :address, :login_name, :telephone_number)');
                
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                $sql->bindParam(':mail', $_POST['mail'], PDO::PARAM_STR);
                $sql->bindParam(':password', $password_hash, PDO::PARAM_STR);
                $sql->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
                $sql->bindParam(':address', $_POST['address'], PDO::PARAM_STR);
                $sql->bindParam(':login_name', $_POST['login_name'], PDO::PARAM_STR);
                $sql->bindParam(':telephone_number', $_POST['telephone_number'], PDO::PARAM_STR); 
                $sql->execute();

                $message = 'お客様情報を登録しました。';
                $is_success = true;
                $link_text = 'ログイン画面に進む';
                $link_url = 'login-input.php';
            }
            ?>
            
            <div class="box has-background-success">
                <div class="notification is-success is-light has-text-centered p-5">
                    <h1 class="title is-4"><?= $message ?></h1>
                    <p class="mt-4"><a href="<?= $link_url ?>" class="button is-success is-outlined"><?= $link_text ?></a></p>
                </div>
            </div>
            
        <?php } else { 
            // ----------------------------------------------------
            // B. ログイン名が重複した場合（失敗）
            // ----------------------------------------------------
            $message = 'ログイン名がすでに使用されていますので、変更してください。';
            $link_text = isset($_SESSION['customer']) ? '情報編集画面に戻る' : '新規登録画面に戻る';
            $link_url = isset($_SESSION['customer']) ? 'customer-input.php' : 'customer-input.php'; 
            ?>
            
            <div class="box">
                <div class="notification is-danger is-light has-text-centered p-5">
                    <h1 class="title is-4">登録/更新エラー</h1>
                    <p class="subtitle is-6"><?= $message ?></p>
                    <p class="mt-4"><a href="<?= $link_url ?>" class="button is-danger is-outlined"><?= $link_text ?></a></p>
                </div>
            </div>
            
        <?php } ?>

    </div>
</section>

<?php require 'footer.php'; ?>