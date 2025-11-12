<?php
// ... (データベース接続、セッション開始などの共通処理)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_detail_id'], $_POST['change_amount'])) {
    $cart_detail_id = $_POST['cart_detail_id'];
    $change_amount = $_POST['change_amount'];
    
    // 1. 現在の個数を取得
    $stmt = $pdo->prepare("SELECT amount FROM cart_detail WHERE cart_detail_id = ?");
    $stmt->execute([$cart_detail_id]);
    $current_amount = $stmt->fetchColumn();

    if ($current_amount !== false) {
        $new_amount = $current_amount;
        
        // 2. 新しい個数を計算
        if ($change_amount === '+') {
            $new_amount++;
        } elseif ($change_amount === '-' && $current_amount > 1) {
            $new_amount--;
        }
        
        // 3. データベースを更新
        if ($new_amount !== $current_amount) { // 実際に個数が変わった、または減らすボタンで1より大きい場合
            $update_stmt = $pdo->prepare("UPDATE cart_detail SET amount = ? WHERE cart_detail_id = ?");
            $update_stmt->execute([$new_amount, $cart_detail_id]);
        }
    }
}

// 4. カート一覧ページへリダイレクト
header('Location: cart-show.php'); // ここはあなたのカート一覧のファイル名に合わせてください
exit;