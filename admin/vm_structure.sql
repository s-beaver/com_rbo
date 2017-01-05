-- Добавили:
-- Катагория_1
-- Товар_1 (артикул_1)
-- Цена 156 руб.

CREATE TABLE j3_virtuemart_category_categories (
  id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  category_parent_id int(1) UNSIGNED NOT NULL DEFAULT 0,
  category_child_id int(1) UNSIGNED NOT NULL DEFAULT 0,
  ordering int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  INDEX category_child_id (category_child_id),
  UNIQUE INDEX category_parent_id (category_parent_id, category_child_id),
  INDEX ordering (ordering)
);

CREATE TABLE j3_virtuemart_categories (
  virtuemart_category_id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  virtuemart_vendor_id int(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Belongs to vendor',
  category_template char(128) DEFAULT NULL,
  category_layout char(64) DEFAULT NULL,
  category_product_layout char(64) DEFAULT NULL,
  products_per_row tinyint(1) DEFAULT NULL,
  limit_list_step char(32) DEFAULT NULL,
  limit_list_initial smallint(1) UNSIGNED DEFAULT NULL,
  hits int(1) UNSIGNED NOT NULL DEFAULT 0,
  metarobot char(40) NOT NULL DEFAULT '',
  metaauthor char(64) NOT NULL DEFAULT '',
  ordering int(1) NOT NULL DEFAULT 0,
  shared tinyint(1) NOT NULL DEFAULT 0,
  published tinyint(1) NOT NULL DEFAULT 1,
  created_on datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  created_by int(1) NOT NULL DEFAULT 0,
  modified_on datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified_by int(1) NOT NULL DEFAULT 0,
  locked_on datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  locked_by int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (virtuemart_category_id)
);

CREATE TABLE j3_virtuemart_categories_ru_ru (
  virtuemart_category_id int(1) UNSIGNED NOT NULL,
  category_name varchar(180) NOT NULL DEFAULT '',
  category_description varchar(19000) NOT NULL DEFAULT '',
  metadesc varchar(400) NOT NULL DEFAULT '',
  metakey varchar(400) NOT NULL DEFAULT '',
  customtitle varchar(255) NOT NULL DEFAULT '',
  slug varchar(192) NOT NULL DEFAULT '',
  PRIMARY KEY (virtuemart_category_id),
  UNIQUE INDEX slug (slug)
);

CREATE TABLE j3_virtuemart_product_categories (
  id INT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  virtuemart_product_id INT(1) UNSIGNED NOT NULL DEFAULT 0,
  virtuemart_category_id INT(1) UNSIGNED NOT NULL DEFAULT 0,
  ordering INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  INDEX ordering (ordering),
  UNIQUE INDEX virtuemart_product_id (virtuemart_product_id, virtuemart_category_id)
);
INSERT INTO j3_virtuemart_product_categories VALUES
(1, 1, 1, 0);

CREATE TABLE j3_virtuemart_product_prices (
  virtuemart_product_price_id INT(1) UNSIGNED NOT NULL AUTO_INCREMENT,/*1*/
  virtuemart_product_id INT(1) UNSIGNED NOT NULL DEFAULT 0,           /*1*/
  virtuemart_shoppergroup_id INT(1) UNSIGNED NOT NULL DEFAULT 0,      /*0*/
  product_price DECIMAL(15, 6) DEFAULT NULL,                          /*156.000*/
  override TINYINT(1) DEFAULT NULL,                                   /*0*/
  product_override_price DECIMAL(15, 5) DEFAULT NULL,                 /*0.00000*/
  product_tax_id INT(1) DEFAULT NULL,                                 /*0*/
  product_discount_id INT(1) DEFAULT NULL,                            /*0*/
  product_currency SMALLINT(1) DEFAULT NULL,                          /*131*/
  product_price_publish_up DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',       /*'0000-00-00 00:00:00'*/
  product_price_publish_down DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',     /*'0000-00-00 00:00:00'*/
  price_quantity_start INT(1) UNSIGNED NOT NULL DEFAULT 0,                        /*0*/
  price_quantity_end INT(1) UNSIGNED NOT NULL DEFAULT 0,                          /*0*/
  created_on DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',                     /*'2016-04-11 13:39:09'*/
  created_by INT(1) NOT NULL DEFAULT 0,                                           /*979*/
  modified_on DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',                    /*'2016-04-11 13:39:09'*/
  modified_by INT(1) NOT NULL DEFAULT 0,                                          /*979*/
  locked_on DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',                      /*'0000-00-00 00:00:00'*/
  locked_by INT(1) NOT NULL DEFAULT 0                                             /*0*/
);
INSERT INTO j3_virtuemart_product_prices VALUES
(1, 1, 0, 156.000000, 0, 0.00000, 0, 0, 131, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '2016-04-11 13:39:09', 979, '2016-04-11 13:39:09', 979, '0000-00-00 00:00:00', 0);

CREATE TABLE j3_virtuemart_products (
  virtuemart_product_id INT(1) UNSIGNED NOT NULL AUTO_INCREMENT,             /*1*/
  virtuemart_vendor_id INT(1) UNSIGNED NOT NULL DEFAULT 1,                   /*1*/
  product_parent_id INT(1) UNSIGNED NOT NULL DEFAULT 0,                      /*0*/
  product_sku VARCHAR(255) DEFAULT NULL,                                     /*'артикул_1'*/
  product_gtin VARCHAR(64) DEFAULT NULL,                                     /*''*/
  product_mpn VARCHAR(64) DEFAULT NULL,                                      /*''*/
  product_weight DECIMAL(10, 4) DEFAULT NULL,                                /*NULL*/
  product_weight_uom VARCHAR(7) DEFAULT NULL,                                /*'KG'*/
  product_length DECIMAL(10, 4) DEFAULT NULL,                                /*NULL*/
  product_width DECIMAL(10, 4) DEFAULT NULL,                                 /*NULL*/
  product_height DECIMAL(10, 4) DEFAULT NULL,                                /*NULL*/
  product_lwh_uom VARCHAR(7) DEFAULT NULL,                                   /*'M'*/
  product_url VARCHAR(255) DEFAULT NULL,                                     /*''*/
  product_in_stock INT(1) NOT NULL DEFAULT 0,                                /*0*/
  product_ordered INT(1) NOT NULL DEFAULT 0,                                 /*0*/
  low_stock_notification INT(1) UNSIGNED NOT NULL DEFAULT 0,                 /*0*/
  product_available_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',    /*'2016-04-11 00:00:00'*/
  product_availability VARCHAR(32) DEFAULT NULL,                             /*''*/
  product_special TINYINT(1) DEFAULT NULL,                                   /*0*/
  product_sales INT(1) UNSIGNED NOT NULL DEFAULT 0,                          /*0*/
  product_unit VARCHAR(8) DEFAULT NULL,                                      /*'KG'*/
  product_packaging DECIMAL(8, 4) UNSIGNED DEFAULT NULL,                     /*NULL*/
  product_params text NOT NULL,                                              /*'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|'*/
  hits INT(1) UNSIGNED DEFAULT NULL,                                         /*NULL*/
  intnotes text DEFAULT NULL,                                                /*''*/
  metarobot VARCHAR(400) DEFAULT NULL,                                       /*''*/
  metaauthor VARCHAR(400) DEFAULT NULL,                                      /*''*/
  layout VARCHAR(16) DEFAULT NULL,                                           /*'0'*/
  published TINYINT(1) DEFAULT NULL,                                         /*1*/
  pordering INT(1) UNSIGNED NOT NULL DEFAULT 0,                              /*0*/
  created_on DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',                /*'2016-04-11 13:39:09'*/
  created_by INT(1) NOT NULL DEFAULT 0,                                      /*979*/
  modified_on DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',               /*'2016-04-11 13:39:09'*/
  modified_by INT(1) NOT NULL DEFAULT 0,                                     /*979*/
  locked_on DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',                 /*'0000-00-00 00:00:00'*/
  locked_by INT(1) NOT NULL DEFAULT 0                                        /*0*/
);
INSERT INTO j3_virtuemart_products VALUES
(1, 1, 0, 'артикул_1', '', '', NULL, 'KG', NULL, NULL, NULL, 'M', '', 0, 0, 0, '2016-04-11 00:00:00', '', 0, 0, 'KG', NULL, 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|', NULL, '', '', '', '0', 1, 0, '2016-04-11 13:39:09', 979, '2016-04-11 13:39:09', 979, '0000-00-00 00:00:00', 0);

CREATE TABLE j3_virtuemart_products_ru_ru (
  virtuemart_product_id INT(1) UNSIGNED NOT NULL,      /*1*/
  product_s_desc VARCHAR(2000) NOT NULL DEFAULT '',    /*''*/
  product_desc VARCHAR(18400) NOT NULL DEFAULT '',     /*''*/
  product_name VARCHAR(180) NOT NULL DEFAULT '',       /*'Товар_1'*/
  metadesc VARCHAR(400) NOT NULL DEFAULT '',           /*''*/
  metakey VARCHAR(400) NOT NULL DEFAULT '',            /*''*/
  customtitle VARCHAR(255) NOT NULL DEFAULT '',        /*''*/
  slug VARCHAR(192) NOT NULL DEFAULT ''                /*'товар_1'*/
);
INSERT INTO j3_virtuemart_products_ru_ru VALUES
(1, '', '', 'Товар_1', '', '', '', 'товар_1');
