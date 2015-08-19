BEGIN

SET @signMoveVar := (SELECT signMove FROM rbo_operstype WHERE operRName = OLD.oper_type);
SET @signCashVar := (SELECT signCash FROM rbo_operstype WHERE operRName = OLD.oper_type);
SET @_balanceCash := ifnull((SELECT balance FROM rbo_cash WHERE name = OLD.oper_firm),"null");
SET @_balanceMove := ifnull((SELECT product_in_stock FROM rbo_products WHERE productId = OLD.productId),"null");

INSERT log SET event_time = now(), event_user = 'trigger', event_type = 'debug_trigger', event_descr = concat(OLD.oper_firm,'=',@_balanceCash,';',OLD.productId,'=',@_balanceMove);

UPDATE rbo_products SET product_in_stock = ifnull(product_in_stock,0) - @signMoveVar * ifnull(OLD.product_cnt, 0) WHERE productId = OLD.productId;
UPDATE rbo_cash     SET balance =          ifnull(balance,0)          - @signCashVar * ifnull(OLD.oper_sum, 0)    WHERE name = OLD.oper_firm;

SET @_balanceCash := ifnull((SELECT balance FROM rbo_cash WHERE name = OLD.oper_firm),"null");
SET @_balanceMove := ifnull((SELECT product_in_stock FROM rbo_products WHERE productId = OLD.productId),"null");

INSERT log SET event_time = now(), event_user = 'trigger', event_type = 'debug_trigger', event_descr = concat('OLD.oper_sum=', OLD.oper_sum * @signCashVar, '; OLD.oper_type=', OLD.oper_type, '; operId=', OLD.operId);
INSERT log SET event_time = now(), event_user = 'trigger', event_type = 'debug_trigger', event_descr = concat(OLD.oper_firm,'=',@_balanceCash,';',OLD.productId,'=',@_balanceMove);


END