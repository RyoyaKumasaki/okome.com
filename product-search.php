<?php require_once 'db-connect.php'?>
<?php
// nav.phpで設定された変数を参照
global $selected_kg; 

// 1. 検索タームの準備
$search_term = '%' . $_POST['product_name'] . '%';
$capacity_filter = $selected_kg;

// 2. SQLクエリの初期設定とパラメータ
$sql_query = 'SELECT * FROM product WHERE product_name LIKE ? AND status = 1';
$params = [$search_term];

// 3. キロ数フィルターの適用
if (!empty($capacity_filter)) {    
    $kg_search_term = '%' . $capacity_filter . 'kg%';
    
    $sql_query .= ' AND product_name LIKE ?'; 
    $params[] = $kg_search_term;
}

// 4. クエリの実行
$sql = $pdo->prepare($sql_query);
$sql->execute($params);
$results = $sql->fetchAll();
?>