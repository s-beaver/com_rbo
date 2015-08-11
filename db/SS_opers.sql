--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 6.3.358.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 25.07.2015 1:21:57
-- Версия сервера: 5.5.44-0+deb7u1
-- Версия клиента: 4.1
-- Пожалуйста, сохраните резервную копию вашей базы перед запуском этого скрипта
--


SET NAMES 'utf8';

-- USE `new.robik.ru`;


--
-- Изменить таблицу "rbo_opers"
--
ALTER TABLE rbo_opers
  DROP COLUMN sSklad1,
  DROP COLUMN sSklad2,
  DROP COLUMN sPayDate,
  DROP COLUMN sCashPlace2,
  DROP COLUMN sPaySum,
  DROP COLUMN sLevel,
  DROP INDEX sLevel,
  DROP INDEX sPayDate,
  DROP INDEX sSklad1,
  DROP INDEX sSklad2,
  CHANGE COLUMN sKey operId INT(11) NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN sOperType oper_type VARCHAR(15) DEFAULT NULL,
  CHANGE COLUMN sDate oper_date DATE DEFAULT NULL,
  CHANGE COLUMN sContragent cust_name VARCHAR(255) DEFAULT NULL,
  CHANGE COLUMN sProductID productId INT(11) DEFAULT NULL AFTER cust_name,
  CHANGE COLUMN sProductCode product_code VARCHAR(25) DEFAULT NULL AFTER productId,
  CHANGE COLUMN sProductName product_name VARCHAR(255) DEFAULT NULL AFTER product_code,
  CHANGE COLUMN sPrice product_price FLOAT DEFAULT NULL AFTER product_name,
  CHANGE COLUMN sCnt product_cnt INT(11) DEFAULT NULL AFTER product_price,
  CHANGE COLUMN sSum oper_sum FLOAT DEFAULT 0 AFTER product_cnt,
  CHANGE COLUMN sOperMan oper_manager VARCHAR(30) DEFAULT NULL,
  CHANGE COLUMN sCashPlace1 oper_firm VARCHAR(10) DEFAULT '' AFTER oper_manager,
  CHANGE COLUMN sRem oper_rem TEXT DEFAULT NULL AFTER oper_firm,
  CHANGE COLUMN sTZ oper_TZ VARCHAR(5) DEFAULT NULL AFTER oper_rem,
  CHANGE COLUMN sAuthor created_by VARCHAR(30) DEFAULT NULL AFTER oper_TZ,
  CHANGE COLUMN sAuthorTime created_on DATETIME DEFAULT NULL AFTER created_by,
  ADD COLUMN modified_by VARCHAR(30) DEFAULT NULL AFTER created_on,
  ADD COLUMN modified_on DATETIME DEFAULT NULL AFTER modified_by;