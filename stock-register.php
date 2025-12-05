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
// ファイル名として初期化
$product_picture_filename = ''; 
$producer_picture_filename = ''; 
$uploaded_dest_paths = []; // アップロード成功したファイルのパスを一時保存

// ----------------------------------------------------
// 0. ディレクトリの確認と作成 (処理の最初に実行)
// ----------------------------------------------------
if (!is_dir($upload_dir)) {
    // 0777 はディレクトリの権限。レンタルサーバーの推奨値に合わせてください。
    if (!mkdir($upload_dir, 0777, true)) {
        $error = "致命的なエラー：アップロードディレクトリの作成に失敗しました ({$upload_dir})。権限を確認してください。";
    }
}

/**
 * ファイルアップロードと移動処理を実行する関数
 * @param array $file_info $_FILES['name']などの配列
 * @param string $upload_dir アップロード先ディレクトリ
 * @return string|false 成功した場合はファイル名、失敗した場合はfalse
 */
function handle_file_upload(array $file_info, string $upload_dir, &$error_message, &$uploaded_dest_paths): string|false {
    if ($file_info['error'] === UPLOAD_ERR_NO_FILE) {
        return ''; // ファイルが選択されていない場合は空文字列を返す
    }
    
    if ($file_info['error'] !== UPLOAD_ERR_OK) {
        $error_message = "画像のアップロードに失敗しました（エラーコード: " . $file_info['error'] . "）。";
        return false;
    }

    $file_tmp_path = $file_info['tmp_name'];
    $file_name = $file_info['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    
    // ファイル名を一意にする
    $new_filename = time() . '_' . uniqid() . '.' . $file_ext;
    $dest_path = $upload_dir . $new_filename;

    if (!is_writable($upload_dir)) {
        $error_message = "【パーミッションエラー】アップロード先ディレクトリに書き込み権限がありません。";
        return false;
    }
    
    if (!is_uploaded_file($file_tmp_path)) {
        $error_message = "【ファイルエラー】一時ファイルが見つかりません。アップロード設定を確認してください。";
        return false;
    }

    // ファイル移動を実行
    if (!move_uploaded_file($file_tmp_path, $dest_path)) {
        $error_message = "ファイルのアップロード（移動）に失敗しました。パスを確認してください。";
        return false;
    }
    
    // 成功した場合、削除のためのパスを保存
    $uploaded_dest_paths[] = $dest_path; 
    return $new_filename;
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

    // ユーザー入力の基本的な検証
    if (empty($product_name) || $price <= 0 || $quantity < 0) {
        $error = "商品名、価格、在庫数を正しく入力してください。";
    }

    // ----------------------------------------------------
    // 1-1. ファイルアップロード処理の開始
    // ----------------------------------------------------
    if (!$error) {
        // 商品画像 (product_picture) の処理
        $result_product_pic = handle_file_upload($_FILES['product_picture'], $upload_dir, $error, $uploaded_dest_paths);
        if ($result_product_pic === false) {
             // エラーメッセージは関数内で設定済み
             $error = $error; 
        } else {
             $product_picture_filename = $result_product_pic;
        }
    }
    
    // エラーがなければ、生産者画像 (producer_picture) の処理
    if (!$error) {
        // ★修正点: producer_pictureのアップロード処理を追加
        $result_producer_pic = handle_file_upload($_FILES['producer_picture'], $upload_dir, $error, $uploaded_dest_paths);
        if ($result_producer_pic === false) {
             // エラーメッセージは関数内で設定済み
             $error = $error; 
        } else {
             $producer_picture_filename = $result_producer_pic;
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
                $product_picture_filename, // 商品画像ファイル名をDBに保存
                $producer_picture_filename // ★修正点: 生産者画像ファイル名をDBに保存
            ]);

            $pdo->commit();
            $message = "【成功】商品「" . htmlspecialchars($product_name) . "」を登録しました。";
            
            // 成功後、フォームをクリアするために変数をリセット
            $product_name = $explanation = '';
            $quantity = $price = 0;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "データベース登録中にエラーが発生しました。（詳細: " . $e->getMessage() . "）";
            
            // DBエラーの場合、アップロードしたファイルを削除
            foreach ($uploaded_dest_paths as $path) {
                 if (file_exists($path)) {
                    unlink($path);
                 }
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

    <form action="stock-register.php" method="post" enctype="multipart/form-data">
        
        <div class="field">
            <label class="label">商品名</label>
            <div class="control">
                <input class="input" type="text" name="product_name" value="<?= htmlspecialchars($product_name ?? '') ?>" required>
            </div>
        </div>
        
        <div class="field">
            <label class="label">価格（円）</label>
            <div class="control">
                <input class="input" type="number" name="price" value="<?= htmlspecialchars($price ?? 0) ?>" required min="1">
            </div>
        </div>
        
        <div class="field">
            <label class="label">在庫数（個）</label>
            <div class="control">
                <input class="input" type="number" name="quantity" value="<?= htmlspecialchars($quantity ?? 0) ?>" required min="0">
            </div>
        </div>
        
        <div class="field">
            <label class="label">商品画像 (JPEG/PNG)</label>
            <div class="control">
                <!-- name="product_picture" -->
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
                        <span class="file-name"></span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="field">
            <label class="label">生産者画像 (JPEG/PNG)</label>
            <div class="control">
                <div class="file has-name is-fullwidth">
                    <label class="file-label">
                        <input class="file-input" type="file" name="producer_picture" accept="image/jpeg, image/png">
                        <span class="file-cta">
                            <span class="file-icon">
                                <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">
                                ファイルを選択
                            </span>
                        </span>
                        <span class="file-name"></span>
                    </label>
                </div>
                <p class="help">農家の方の顔写真などを登録してください。</p>
            </div>
        </div>
        <!-- ★修正点ここまで -->

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
    
    <div class="mt-5 has-text-right">
        <a href="controlltop.php" class="button is-link is-outlined">
            <span class="icon"><i class="fas fa-home"></i></span>
            <span>トップ画面へ戻る (管理者サイト)</span>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInputs = document.querySelectorAll('.file-input');
        fileInputs.forEach(input => {
            input.addEventListener('change', () => {
                const fileNameSpan = input.closest('.file').querySelector('.file-name');
                if (input.files.length > 0) {
                    fileNameSpan.textContent = input.files[0].name;
                } else {
                    fileNameSpan.textContent = '';
                }
            });
        });
    });
</script>

<?php require 'footer.php'; ?>