<?php
session_start();
require 'db-connect.php';
$page_title = 'ログイン';
require 'controllheader.php';
require 'admin-menu.php';

?>
<section class="section">
    <div class="container is-max-desktop">
        <div class="box">
            <h1 class="title is-3 has-text-centered">管理者ログイン</h1>
            
            <form action="admin-login-output.php" method="post">
                
                <div class="field">
                    <label class="label" for="login_name">ログイン名</label>
                    <div class="control">
                        <input class="input" type="text" id="login_name" name="login_name" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="password">パスワード</label>
                    <div class="control">
                        <input class="input" type="password" id="password" name="password" required>
                    </div>
                </div>

                <div class="field mt-5 is-grouped is-grouped-centered">
                    <div class="control">
                        <input class="button is-primary is-medium" type="submit" value="ログイン">
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>
<?php require 'footer.php'; ?>