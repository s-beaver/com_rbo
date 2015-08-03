--
-- ������ ������������ Devart dbForge Studio for MySQL, ������ 6.3.358.0
-- �������� �������� ��������: http://www.devart.com/ru/dbforge/mysql/studio
-- ���� �������: 25.07.2015 15:11:07
-- ������ �������: 5.5.44-0+deb7u1
-- ������ �������: 4.1
-- ����������, ��������� ��������� ����� ����� ���� ����� �������� ����� �������
--


SET NAMES 'utf8';

-- USE `new.robik.ru`;


--
-- �������� ������� "rbo_products"
--
ALTER TABLE rbo_products
  DROP COLUMN description,
  DROP COLUMN customers_rating,
  DROP COLUMN picture,
  DROP COLUMN thumbnail,
  DROP COLUMN items_sold,
  DROP COLUMN big_picture,
  DROP COLUMN enabled,
  DROP COLUMN brief_description,
  DROP COLUMN customer_votes,
  CHANGE COLUMN productID productId INT(11) NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN product_code product_code VARCHAR(50) DEFAULT NULL AFTER productId,
  CHANGE COLUMN categoryID categoryId INT(11) DEFAULT NULL AFTER product_code,
  CHANGE COLUMN name product_name VARCHAR(255) DEFAULT NULL AFTER categoryId,
  CHANGE COLUMN Price product_price FLOAT DEFAULT NULL AFTER product_name,
  CHANGE COLUMN in_stock product_in_stock INT(11) DEFAULT NULL AFTER product_price,
  CHANGE COLUMN list_price product_price1 FLOAT DEFAULT NULL AFTER customer_votes;