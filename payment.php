<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';
?>
<h2>お支払方法の選択</h2>
<form action="payment-success.php" method="post">
        <label><input type="radio" name="choice" value="kure" required> クレジットカードでお支払い</label><br>
        <label><input type="radio" name="choice" value="pei"> paypayでお支払い</label><br>
        <label><input type="radio" name="choice" value="app"> Apple Payでお支払い</label><br>
        <label><input type="radio" name="choice" value="gen"> 現金でお支払い</label><br><br>

        <button type="submit">決済を確定する</button>
    </form>

    <form action="cart.php" method="get">
        <button type="submit">キャンセル</button>
        </form>
<?php require 'footer.php'; ?>