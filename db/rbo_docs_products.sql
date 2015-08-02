--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 6.3.358.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 02.08.2015 18:12:17
-- Версия сервера: 5.5.44-0+deb7u1
-- Версия клиента: 4.1
-- Пожалуйста, сохраните резервную копию вашей базы перед запуском этого скрипта
--


SET NAMES 'utf8';

-- USE `new.robik.ru`;


--
-- Изменить таблицу "rbo_docs_products"
--
ALTER TABLE rbo_docs_products
  ADD INDEX IDX_rbo_docs_products_docId (docId);