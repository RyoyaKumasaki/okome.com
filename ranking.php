<h2>
    ランキング
</h2>
<?php
require_once 'db-connect.php';
$sql = "
SELECT p.product_id, p.product_name, p.quantity, p.price, p.status, p.product_explanation, p.product_picture, p.producer_picture
FROM `LAA1607615-okome`.`product` p
JOIN (
  SELECT product_id
  FROM `LAA1607615-okome`.`review`
  GROUP BY product_id
  ORDER BY AVG(rating) DESC
  LIMIT 3
) top_products ON p.product_id = top_products.product_id;
";