<?php
session_start();
require 'db-connect.php';
?>
<?php
$name = $address = $login_name = $password = $mail = $telephone_number = '';
if (isset($_SESSION['customer'])) {
    $user_id = $_SESSION['customer']['user_id'];
    $mail = $_SESSION['customer']['mail'];
    $name = $_SESSION['customer']['name'];
    $address = $_SESSION['customer']['address'];
    $login_name = $_SESSION['customer']['login_name'];
} else {
    header("Location: login-input.php");
    exit;
}
?>
<?php
require 'header.php';
require 'menu.php';
?>

<div class="container is-max-desktop p-4"> <div class="card mb-5">
        <header class="card-header">
            <p class="card-header-title has-text-left is-size-4">ユーザー情報</p>
        </header>
        <div class="card-content">
            <div class="content">
                <table class="table is-narrow is-fullwidth"> <tr>
                        <th>ユーザー名：</th>
                        <td><?= htmlspecialchars($name) ?></td>
                    </tr>
                    <tr>
                        <th>ユーザーID：</th>
                        <td><?= htmlspecialchars($login_name) ?></td>
                    </tr>
                    <tr>
                        <th>メールアドレス：</th>
                        <td><?= htmlspecialchars($mail) ?></td>
                    </tr>
                    <tr>
                        <th>住所：</th>
                        <td><?= htmlspecialchars($address) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <footer class="card-footer">
            <form action="mypage-login.php" method="get" class="card-footer-item">
                <button type="submit" class="button is-link is-light is-fullwidth">ユーザー情報を変更</button>
            </form>
            <form action="history.php" class="card-footer-item">
                <button type="submit" class="button is-link is-light is-fullwidth">購入履歴を見る</button>
            </form>
            <!-- <a href="history.php" class="card-footer-item button is-info is-light">購入履歴を見る</a> -->
        </footer>
    </div>
    <div class="card mb-5">
        <header class="card-header">
            <p class="card-header-title has-text-left is-size-4">レビュー投稿履歴</p>
        </header>
        <div class="card-content">
            <div class="content">
                <?php
                if(isset($_SESSION['customer'])) {
                    $sql = $pdo->prepare('select pd.product_name, rv.comment, rv.product_id 
                                            from review rv 
                                            join product pd 
                                            on rv.product_id = pd.product_id where rv.user_id = ?');
                    // $sql = $pdo->prepare('SELECT * FROM review WHERE user_id = ?');
                    $sql->execute([$_SESSION['customer']['user_id']]);
                    $reviews = $sql->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($reviews)) { // レビューが一件もない場合
                        echo '<p>投稿したレビューはありません。</p>';
                    } else { // レビューがある場合
                        foreach ($reviews as $row) {
                            // コメントが空でないことを確認してから表示 (念のため)
                            if (!empty($row['comment'])) {
                                // ここで商品名なども表示できるよう、JOINでデータを拡張するのが理想ですが、ここではレビューコメントのみを表示します。
                                echo '<div class="box p-3 mb-3">';
                                echo '<p><strong>[商品ID: ' . htmlspecialchars($row['product_name']) . ']</strong></p>';
                                echo '<p>' . nl2br(htmlspecialchars($row['comment'])) . '</p>';
                                echo '</div>';
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <form action="logout-input.php" method="get">
        <button type="submit" class="button is-danger is-small">ログアウト</button>
    </form>

</div>

<?php require 'footer.php'; ?>