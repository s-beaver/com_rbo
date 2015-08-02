--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 6.3.358.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 02.08.2015 18:08:13
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
  DROP COLUMN cust_name,
  DROP INDEX sCashPlace,
  DROP INDEX sContragent,
  DROP INDEX sDate,
  DROP INDEX sOperMan,
  ADD COLUMN custId INT(11) DEFAULT NULL AFTER oper_date,
  CHANGE COLUMN productId productId INT(11) DEFAULT NULL,
ENGINE = INNODB;

ALTER TABLE rbo_opers
  ADD INDEX IDX_rbo_opers_oper_firm (oper_firm);

ALTER TABLE rbo_opers
  ADD INDEX IDX_rbo_opers_oper_date (oper_date);

ALTER TABLE rbo_opers
  ADD INDEX IDX_rbo_opers_oper_manager (oper_manager);

ALTER TABLE rbo_opers
  ADD INDEX IDX_rbo_opers_custId (custId);