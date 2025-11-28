<?php require_once 'db-connect.php'?>
<?php
$sql = $pdo->prepare('select * from product where product_name like ? && status = 1');
$search_term = '%' . $_POST['product_name'] . '%';
$sql->execute([$search_term]);
$results = $sql->fetchAll();
?>