<?php
session_start();
require 'controllheader.php';
require 'admin-menu.php';
require_once 'db-connect.php'; 

// 画像保存先ディレクトリ
$upload_dir = 'img/products/';

$message = '';
$error = '';
$product_name = '';
$quantity = 0;
$price = 0;
$explanation = '';
$producer_pic = '';
$product_picture_filename = '';
$dest_path = '';

// ----------------------------------------------------
// 0. ディレクトリの確認と作成 (処理の最初に実行)
// ----------------------------------------------------
if (!is_dir($upload_dir)) {
    // 0777 はディレクトリの権限。レンタルサーバーの推奨値に合わせてください。
    if (!mkdir($upload_dir, 0777, true)) {
        // ディレクトリ作成に失敗した場合、エラーを設定して以降の処理を中断
        $error = "致命的なエラー：アップロードディレクトリの作成に失敗しました ({$upload_dir})。権限を確認してください。";
    }
}


// ----------------------------------------------------
// 1. フォームが送信された場合の処理
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // POSTデータの取得
    $product_name = $_POST['product_name'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 0);
    $price = (int)($_POST['price'] ?? 0);
    $explanation = $_POST['product_explanation'] ?? '';
    $producer_pic = $_POST['producer_picture'] ?? ''; 

    // ユーザー入力の基本的な検証
    if (empty($product_name) || $price <= 0 || $quantity < 0) {
        $error = "商品名、価格、在庫数を正しく入力してください。";
    }

    // ----------------------------------------------------
    // 1-1. ファイルアップロード処理の開始
    // ----------------------------------------------------
    if (!$error && isset($_FILES['product_picture']) && $_FILES['product_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        
        if ($_FILES['product_picture']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES['product_picture']['tmp_name'];
            $file_name = $_FILES['product_picture']['name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            
            // ファイル名を一意にする
            $product_picture_filename = time() . '_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $product_picture_filename;

            // --- ★修正・デバッグポイント: 権限と存在チェックを追加 ---
            if (!is_writable($upload_dir)) {
                $error = "【パーミッションエラー】アップロード先ディレクトリ({$upload_dir})に書き込み権限がありません。パーミッションを707などに設定してください。";
            } elseif (!is_uploaded_file($file_tmp_path)) {
                $error = "【ファイルエラー】一時ファイルが見つかりません。アップロード設定を確認してください。";
            } else {
                // ファイル移動を実行
                if (!move_uploaded_file($file_tmp_path, $dest_path)) {
                    // ファイル移動が失敗した場合、エラーを設定
                    $error = "ファイルのアップロード（移動）に失敗しました。パスを確認してください。";
                    $product_picture_filename = ''; // DBに登録しないようにクリア
                }
            }
            // --- デバッグポイント終了 ---
            
        } else {
             // ファイル選択時のシステムエラー
             $error = "画像のアップロードに失敗しました（エラーコード: " . $_FILES['product_picture']['error'] . "）。";
        }
    }


    // ----------------------------------------------------
    // 2. データベースへの登録処理（トランザクション）
    // ----------------------------------------------------
    // $error が発生していない場合にのみDB処理を実行
    if (!$error) { 
        $pdo->beginTransaction();
        
        try {
            $sql = '
                INSERT INTO product 
                (product_name, quantity, price, product_explanation, product_picture, producer_picture)
                VALUES (?, ?, ?, ?, ?, ?)
            ';
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                $product_name,
                $quantity,
                $price,
                $explanation,
                $product_picture_filename, // ★成功した場合のファイル名をDBに保存
                $producer_pic
            ]);

            $pdo->commit();
            $message = "【成功】商品「" . htmlspecialchars($product_name) . "」を登録しました。";
            
            // 成功後、フォームをクリアするために変数をリセット
            $product_name = $explanation = $producer_pic = '';
            $quantity = $price = 0;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "データベース登録中にエラーが発生しました。（詳細: " . $e->getMessage() . "）";
            
            // DBエラーの場合、アップロードしたファイルを削除（オプション）
            if (!empty($dest_path) && file_exists($dest_path)) {
                unlink($dest_path);
            }
        }
    }
}
?>
<div class="container is-max-desktop p-5">
    <h2 class="title is-4">商品新規登録と画像アップロード</h2>

    <?php if ($message): ?>
        <div class="notification is-success">
            <p><?= $message ?></p>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="notification is-danger">
            <p><?= $error ?></p>
        </div>
    <?php endif; ?>

    <!-- enctype="multipart/form-data" はファイルアップロードに必須 -->
    <form action="stock-register.php" method="post" enctype="multipart/form-data">
        
        <!-- 商品名 -->
        <div class="field">
            <label class="label">商品名</label>
            <div class="control">
                <input class="input" type="text" name="product_name" value="<?= htmlspecialchars($product_name ?? '') ?>" required>
            </div>
        </div>
        
        <!-- 価格 -->
        <div class="field">
            <label class="label">価格（円）</label>
            <div class="control">
                <input class="input" type="number" name="price" value="<?= htmlspecialchars($price ?? 0) ?>" required min="1">
            </div>
        </div>
        
        <!-- 在庫数 -->
        <div class="field">
            <label class="label">在庫数（個）</label>
            <div class="control">
                <input class="input" type="number" name="quantity" value="<?= htmlspecialchars($quantity ?? 0) ?>" required min="0">
            </div>
        </div>
        
        <!-- 商品画像 -->
        <div class="field">
            <label class="label">商品画像 (JPEG/PNG)</label>
            <div class="control">
                <div class="file has-name is-fullwidth">
                    <label class="file-label">
                        <input class="file-input" type="file" name="product_picture" accept="image/jpeg, image/png">
                        <span class="file-cta">
                            <span class="file-icon">
                                <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">
                                ファイルを選択
                            </span>
                        </span>
                        <!-- Bulmaのファイル名表示はJavaScriptが必要ですが、ここでは簡略化 -->
                        <span class="file-name">
                            <!-- 選択したファイル名が表示される想定 -->
                        </span>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- 生産者画像ファイル名 -->
        <div class="field">
            <label class="label">生産者画像ファイル名 (手動入力)</label>
            <div class="control">
                <input class="input" type="text" name="producer_picture" value="<?= htmlspecialchars($producer_pic ?? '') ?>">
                <p class="help">img/ の後のファイル名を拡張子付きで入力してください。</p>
            </div>
        </div>

        <!-- 商品説明 -->
        <div class="field">
            <label class="label">商品説明</label>
            <div class="control">
                <textarea class="textarea" name="product_explanation" rows="5" placeholder="商品の詳細を入力してください"><?= htmlspecialchars($explanation ?? '') ?></textarea>
            </div>
        </div>
        
        <!-- 送信ボタン -->
        <div class="field is-grouped is-justify-content-flex-end">
            <div class="control">
                <button type="submit" class="button is-primary is-medium">商品を登録</button>
            </div>
        </div>
    </form>
</div>

<?php require 'footer.php'; ?>