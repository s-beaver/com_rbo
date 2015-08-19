BEGIN

SET @signMoveVar := (SELECT signMove FROM rbo_operstype WHERE operRName = NEW.oper_type);
SET @signCashVar := (SELECT signCash FROM rbo_operstype WHERE operRName = NEW.oper_type);
SET @_balanceCash := ifnull((SELECT balance FROM rbo_cash WHERE name = NEW.oper_firm),"null");
SET @_balanceMove := ifnull((SELECT product_in_stock FROM rbo_products WHERE productId = NEW.productId),"null");

INSERT log SET event_time = now(), event_user = 'trigger', event_type = 'debug_trigger', event_descr = concat(NEW.oper_firm,'=',@_balanceCash,';',NEW.productId,'=',@_balanceMove);

UPDATE rbo_products SET product_in_stock = ifnull(product_in_stock,0) + @signMoveVar * ifnull(NEW.product_cnt, 0) WHERE productId = NEW.productId;
UPDATE rbo_cash     SET balance =          ifnull(balance,0)          + @signCashVar * ifnull(NEW.oper_sum, 0)    WHERE name = NEW.oper_firm;

SET @_balanceCash := ifnull((SELECT balance FROM rbo_cash WHERE name = NEW.oper_firm),"null");
SET @_balanceMove := ifnull((SELECT product_in_stock FROM rbo_products WHERE productId = NEW.productId),"null");

INSERT log SET event_time = now(), event_user = 'trigger', event_type = 'debug_trigger', event_descr = concat('NEW.oper_sum=', NEW.oper_sum * @signCashVar, '; NEW.oper_type=', NEW.oper_type, '; operId=', NEW.operId);
INSERT log SET event_time = now(), event_user = 'trigger', event_type = 'debug_trigger', event_descr = concat(NEW.oper_firm,'=',@_balanceCash,';',NEW.productId,'=',@_balanceMove);


END