SELECT 
  rbo_opers.oper_type, 
  CONCAT(rbo_opers.product_name,' ',rbo_opers.oper_rem) AS 'product_name',
  rbo_categories.categoryID, 

CASE 
  WHEN rbo_opers.oper_sum>0 THEN rbo_opers.oper_sum
  ELSE rbo_opers.oper_sum
END AS 'sPrice',

	rbo_opers.product_cnt, 
	rbo_opers.oper_sum, 
	DATE_FORMAT(oper_date,'%y') AS 'sYear', 
	DATE_FORMAT(oper_date,'%m') AS 'sMonth', 
	DATE_FORMAT(oper_date,'%d') AS 'sDay', 
	rbo_opers.oper_date, 
	rbo_opers.oper_manager,
        rbo_categories.name AS 'Cat'
FROM rbo_opers 
LEFT JOIN (rbo_products, rbo_categories) ON 
(rbo_opers.productId = rbo_products.productId and rbo_products.categoryId = rbo_categories.categoryId)
HAVING sYear=15 AND CAST(sMonth AS UNSIGNED)>=3