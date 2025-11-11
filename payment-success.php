<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';
?>

<h2>決済完了</h2>
<?php
$choice = $_POST['choice'];

if ($choice == 'kure') {
    echo 'クレジットカードでお支払い';
} elseif ($choice == 'pei') {
    echo 'paypayでお支払い';
} elseif ($choice == 'app') {
    echo 'Apple Payでお支払い';
} else {
    echo 'コンビニでお支払い';
}
?>
<p>ご購入ありがとうございました。<br>
注文が正常に完了しました</p>

<form action="top.php" method="get">
    <input type="submit" value="トップ画面へ戻る">
</form>

<form action="review.php" method="get">
    <input type="submit" value="レビューを投稿する">
</form>
<?php require 'footer.php'; ?>