CREATE
TRIGGER #__rbo_changeInStockDelete
  AFTER DELETE
ON #__rbo_opers
  FOR EACH ROW
BEGIN
SET @signMoveVar := (SELECT signMove FROM #__rbo_operstype WHERE operRName = OLD.oper_type);
SET @signCashVar := (SELECT signCash FROM #__rbo_operstype WHERE operRName = OLD.oper_type);
SET @_balanceCash := ifnull((SELECT balance FROM #__rbo_firms WHERE name = OLD.oper_firm),"null");
SET @_balanceMove := ifnull((SELECT product_in_stock FROM #__rbo_products WHERE productId = OLD.productId),"null");

UPDATE #__rbo_products SET product_in_stock = ifnull(product_in_stock,0) - @signMoveVar * ifnull(OLD.product_cnt, 0) * ifnull(product_type, 0) WHERE productId = OLD.productId;
UPDATE #__rbo_firms     SET balance =          ifnull(balance,0)          - @signCashVar * ifnull(OLD.oper_sum, 0)    WHERE name = OLD.oper_firm;

SET @_balanceCash := ifnull((SELECT balance FROM #__rbo_firms WHERE name = OLD.oper_firm),"null");
SET @_balanceMove := ifnull((SELECT product_in_stock FROM #__rbo_products WHERE productId = OLD.productId),"null");

END