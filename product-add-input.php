<?php require 'header.php'; ?>
<?php require 'menu.php'; ?>
<?php require_once 'db-connect.php'; ?>
<h1>商品追加</h1>
<form action="product-add-output.php">
<table>
<?php 
    echo '<tr><td>商品名</td><td>';
    echo '<input type="text" name="product_name" value="',$product_name,'">';
    echo '</td></tr>';
    echo '<tr><td>在庫数</td><td>';
    echo '<input type="text" name="quantity" value="',$quantity,'">';
    echo '</td></tr>';
    echo '<tr><td>値段</td><td>';
    echo '<input type="text" name="price" value="',$price,'">';
    echo '</td></tr>';
    echo '<tr><td>説明</td><td>';
    echo '<input type="text" name="description" value="',$description,'">';
    echo '</td></tr>';
    echo '<tr><td>商品画像URL</td><td>';
    echo '<input type="text" name="image_url" value="',$image_url,'">';
    echo '</td></tr>';
    echo '<tr><td>生産者画像URL</td><td>';
    echo '<input type="text" name="producer_image_url" value="',$producer_image_url,'">';
    echo '</td></tr>';
    echo '<input type="submit" value="確定">';
    echo '</form>';
?>
</table>
</form>