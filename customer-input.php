<?php session_start(); ?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<?php
$name = $address = $login_name = $password = $mail = $telephone_number '';
if (isset($_SESSION['customer'])) {
    $user_id = $_SESSION['customer']['user_id'];
    $mail = $_SESSION['customer']['mail'];
    $name = $_SESSION['customer']['name'];
    $address = $_SESSION['customer']['address'];
    $login_name = $_SESSION['customer']['login_name'];
    $password = $_SESSION['customer']['password'];

}
echo '<form action="customer-output.php" method="post">';
echo '<table>';
echo '<tr><td>お名前</td><td>';
echo '<input type="text" name="name" value="',$name,'">';
echo '</td></tr>';
echo '<tr><td>ご住所</td><td>';
echo '<input type="text" name="address" value="',$address,'">';
echo '</td></tr>';
echo '<tr><td>ログイン名</td><td>';
echo '<input type="text" name="login" value="',$login_name,'">';
echo '</td></tr>';
echo '<tr><td>パスワード</td><td>';
echo '<input type="text" name="password" value="',$password,'">';
echo '</td></tr>';
echo '<tr><td>メールアドレス</td><td>';
echo '<input type="text" name="mail" value="',$mail,'">';
echo '</td></tr>';
echo '<tr><td>電話番号</td><td>';
echo '<input type="text" name="telephone_number" value="',$telephone_number,'">';
echo '</table>';
echo '<input type="submit" value="確定">';
echo '</form>';
?>
<?php require 'footer.php'; ?>