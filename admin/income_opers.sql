SELECT
  %group_by%
  SUM(oper_sum)
FROM (SELECT
        #__rbo_opers.oper_type,
        CONCAT(#__rbo_opers.product_name, ' ', #__rbo_opers.oper_rem) AS 'product_name',
        #__rbo_categories.categoryId,
        #__rbo_opers.product_price    AS 'sPrice',
        #__rbo_opers.product_cnt,
        #__rbo_opers.oper_sum,
        DATE_FORMAT(oper_date, '%y')  AS 'sYear',
        DATE_FORMAT(oper_date, '%m')  AS 'sMonth',
        DATE_FORMAT(oper_date, '%d')  AS 'sDay',
        #__rbo_opers.oper_date,
        #__rbo_opers.oper_manager,
        #__rbo_categories.name        AS 'Cat',
        #__rbo_cust.cust_is_own_firm  AS 'own'
      FROM #__rbo_opers
        LEFT JOIN #__rbo_products
          ON (#__rbo_opers.productId = #__rbo_products.productId)
        LEFT JOIN #__rbo_categories
          ON (#__rbo_products.categoryId = #__rbo_categories.categoryId)
        LEFT JOIN #__rbo_cust
          ON (#__rbo_opers.custId = #__rbo_cust.custId)
      HAVING %group_by% (own IS NULL OR own <> 1)
) t10
GROUP BY %group_by%