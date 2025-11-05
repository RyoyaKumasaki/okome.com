<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<?php
if (isset($_SESSION['customer'])) {
    $user_id = $_SESSION['customer']['user_id'];
    $sql = $pdo->prepare('SELECT * FROM customer_user WHERE user_id!=? AND login_name=?');
    $sql->execute([$user_id, $_POST['login_name']]);
} else {
    $sql = $pdo->prepare('SELECT * FROM customer_user WHERE login_name=?');
    $sql->execute([$_POST['login_name']]);
}
if (empty($sql->fetchAll())) {
    if (isset($_SESSION['customer'])) {
        $sql = $pdo->prepare('UPDATE customer_user SET mail=?, password=?, name=?, 
                             address=?, login_name=?, telephone_number=? WHERE user_id=?');
        $sql->execute([
            $_POST['mail'], password_hash($_POST['password'], PASSWORD_DEFAULT),
            $_POST['name'], $_POST['address'], $_POST['login_name'], $_POST['telephone_number'], $user_id]);
        $_SESSION['customer'] = [
            'user_id'=>$user_id, 'mail'=>$_POST['mail'],
            'password'=>$_POST['password'], 'name'=>$_POST['name'], 'address'=>$_POST['address'],
            'login_name'=>$_POST['login_name'], 'telephone_number'=>$_POST['telephone_number']];
            
        echo 'お客様情報を更新しました。';
    } else {
        $sql = $pdo->prepare('INSERT INTO customer_user 
        VALUES(null,mail=:mail,password=:password,name=:name,address=:address,null,
        login_name=:login_name,telephone_number=:telephone_number)');
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql->bindParam(':mail',$_POST['mail']);
        $sql->bindParam(':password',$password_hash);
        $sql->bindParam(':name',$_POST['name']);
        $sql->bindParam(':address',$_POST['address']);
        $sql->bindParam(':login_name',$_POST['login_name']);
        $sql->bindParam(':telephone_number',$_POST['telephone_number']);
        $sql->execute();
        echo 'お客様情報を登録しました。';
    }
} else {
    echo 'ログイン名がすでに使用されていますので、変更してください。';
}
?>
<?php require 'footer.php'; ?>