--
-- ������ ������������ Devart dbForge Studio for MySQL, ������ 6.3.358.0
-- �������� �������� ��������: http://www.devart.com/ru/dbforge/mysql/studio
-- ���� �������: 02.08.2015 18:12:17
-- ������ �������: 5.5.44-0+deb7u1
-- ������ �������: 4.1
-- ����������, ��������� ��������� ����� ����� ���� ����� �������� ����� �������
--


SET NAMES 'utf8';

-- USE `new.robik.ru`;


--
-- �������� ������� "rbo_docs_products"
--
ALTER TABLE rbo_docs_products
  ADD INDEX IDX_rbo_docs_products_docId (docId);