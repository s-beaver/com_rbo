SET NAMES 'utf8';

ALTER TABLE rbo_opers
  ADD COLUMN docId INT(11) DEFAULT NULL AFTER custId;

INSERT INTO rbo_opers (docId, productId, product_code, product_name, product_cnt, product_price)
  SELECT
    docId,
    productId,
    product_code,
    product_name,
    product_cnt,
    product_price
  FROM rbo_docs_products

UPDATE rbo_opers set oper_sum = product_price * product_cnt where (oper_sum is NULL OR oper_sum=0) AND docId>0;