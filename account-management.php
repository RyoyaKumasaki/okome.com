<?php
session_start();
require 'db-connect.php';
$page_title = 'アカウント管理';
require 'controllheader.php';
require 'admin-menu.php';

// ----------------------------------------------------
// 1. ステータス変更処理 (ボタンが押された場合のみ実行)
// ----------------------------------------------------
// $login_name は POST で受け取っている前提
$login_name = $_POST['user_name'] ?? null;
$new_status = $_POST['new_status'] ?? null;

if ($login_name && $new_status !== null) {
    try {
        // ステータスを整数として扱う
        $new_status_int = (int)$new_status; 
        
        // UPDATE クエリを準備（FROM は不要）
        $adminButton = $pdo->prepare("UPDATE customer_user SET status = :status WHERE login_name = :login_name");
        
        // 実行
        $adminButton->execute([
            ':status' => $new_status_int,
            ':login_name' => $login_name
        ]);

        // ステータス更新成功後、ユーザーに最新の情報を表示するために、リダイレクトまたは再読み込み処理を推奨。
        // ここでは、直後の DB 検索で最新情報を取得できるため、このまま進めます。

    } catch (PDOException $e) {
        // 必要に応じてエラー処理を追加
        echo "<div class='notification is-danger'>ステータス更新中にエラーが発生しました。</div>";
        // エラー詳細: echo $e->getMessage();
    }
}

// ----------------------------------------------------
// 2. ユーザー情報の検索
// ----------------------------------------------------
// POSTデータから検索対象のユーザー名を取得
$search_login_name = $_POST['user_name'] ?? null;

// 検索対象がない場合は処理を中断 (検索結果の表示は行わない)
if (!$search_login_name) {
    echo '<div class="notification is-info is-light container is-max-desktop mt-4">検索対象のユーザー名が指定されていません。</div>';
    require 'footer.php';
    exit;
}

$sql = "SELECT * FROM customer_user
        WHERE login_name = :login_name";

$stmt = $pdo->prepare($sql);
$stmt->execute([':login_name' => $search_login_name]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ユーザーが見つからない場合の処理
if (!$user) {
    echo '<div class="notification is-warning is-light container is-max-desktop mt-4">ユーザー「' . htmlspecialchars($search_login_name) . '」は見つかりませんでした。</div>';
    require 'footer.php';
    exit;
}

// ユーザー情報が見つかった場合の表示
?>

<div class="container is-max-desktop mt-4 mb-5">
    <p class="title is-3">検索結果</p>
    
    <div class="level mb-4">
        <p class="subtitle is-3 level-left">氏名：<?= htmlspecialchars($user['name']); ?></p>
        
        <form action="account-management.php" method="post" class="level-right">
            <input type="hidden" name="user_name" value="<?= htmlspecialchars($user['login_name']); ?>"> 
            
            <?php
            $current_status = $user['status'];
            
            // 状態表示: 0=削除済み/休止, 1=アクティブ
            if($current_status == 0) : 
                // 現在 0 (削除済み) なら、復元ボタンを表示
            ?>
                <input type="hidden" name="new_status" value="1">
                <input type="submit" class="button is-link is-light is-medium" value="復元">
            <?php else : 
                // 現在 1 (アクティブ) なら、削除ボタンを表示
            ?>
                <input type="hidden" name="new_status" value="0">
                <input type="submit" class="button is-danger is-light is-medium" value="削除">
            <?php endif; ?>
        </form>
    </div>
    
    <div class="box mb-5">
        <p><strong>ログイン名：</strong><?= htmlspecialchars($user['login_name']); ?></p>
        <p><strong>メールアドレス：</strong><?= htmlspecialchars($user['mail']); ?></p>
        <p><strong>住所：</strong><?= htmlspecialchars($user['address']); ?></p>
        <div class="field">
            <label class="label is-small">電話番号</label>
                <div class="control">
                    <input 
                        class="input is-static" 
                        type="tel" 
                        value="<?= htmlspecialchars($user['telephone_number']); ?>" 
                        readonly
                    >
                </div>
        </div>
        <p><strong>アカウント状態：</strong><span class="tag is-<?= ($user['status'] == 1) ? 'success' : 'warning' ?>"><?= ($user['status'] == 1) ? 'アクティブ' : '削除済み/休止' ?></span></p>
    </div>
</div>

<section class="section pt-0">
    <div class="container is-max-desktop">
        
        <p class="title is-3">購入履歴</p>
        
        <?php
        // ユーザーIDは $user['user_id'] を使用
        $sql = $pdo->prepare('SELECT * FROM `order` WHERE user_id=? ORDER BY order_id DESC');
        $sql->execute([$user['user_id']]);
        ?>

        <?php if ($sql->rowCount() > 0) : ?>
            <?php foreach ($sql as $row) : ?>
                <div class="box mb-5">
                    <h3 class="title is-5 has-text-primary">
                        購入日：<?= htmlspecialchars($row['date']) ?> (注文ID: <?= htmlspecialchars($row['order_id']) ?>)
                    </h3>
                    
                    <div class="table-container">
                        <table class="table is-striped is-fullwidth is-hoverable">
                            <thead>
                                <tr>
                                    <th class="has-text-centered">商品番号</th>
                                    <th>商品名</th>
                                    <th class="has-text-right">価格</th>
                                    <th class="has-text-centered">個数</th>
                                    <th class="has-text-right">小計</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $products = $pdo->prepare('
                                    SELECT * FROM order_detail 
                                    JOIN product ON order_detail.product_id = product.product_id 
                                    WHERE order_id=?');
                                $products->execute([$row['order_id']]); 
                                $total = 0;
                                foreach ($products as $product) :
                                    $subtotal = $product['price'] * $product['count'];
                                    $total += $subtotal;
                                ?>
                                    <tr>
                                        <td class="has-text-centered"><?= htmlspecialchars($product['product_id']); ?></td>
                                        <td><?= htmlspecialchars($product['product_name']); ?></td>
                                        <td class="has-text-right"><?= number_format($product['price']); ?>円</td>
                                        <td class="has-text-centered"><?= htmlspecialchars($product['count']); ?></td>
                                        <td class="has-text-right"><?= number_format($subtotal); ?>円</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="has-text-right is-size-5"><strong>合計</strong></td>
                                    <td class="has-text-right is-size-5 has-text-weight-bold has-text-danger"><?= number_format($total); ?>円</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
             <div class="notification is-info is-light has-text-centered">
                 このユーザーの購入履歴はありません。
             </div>
        <?php endif; ?>

        <div class="has-text-centered mt-6">
            <a href="controlltop.php" class="button is-link is-light">トップ画面へ戻る</a>
        </div>
    </div>
</section>
<?php require 'footer.php'; ?>