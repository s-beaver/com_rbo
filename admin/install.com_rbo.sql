CREATE TABLE #__rbo_firms (
  cashID int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) DEFAULT NULL,
  balance int(11) DEFAULT NULL COMMENT 'Остаток по кассе',
  disabled int(11) NOT NULL DEFAULT 0 COMMENT 'По умолчанию - enabled. Если > 0 то disabled',
  balanceStart int(11) DEFAULT NULL,
  dateStart datetime DEFAULT NULL,
  PRIMARY KEY (cashID)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 694
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE #__rbo_categories (
  categoryId int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) DEFAULT NULL,
  parent int(11) DEFAULT NULL,
  products_count int(11) DEFAULT NULL,
  description text DEFAULT NULL,
  picture varchar(30) DEFAULT NULL,
  products_count_admin int(11) DEFAULT NULL,
  PRIMARY KEY (categoryId)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 108
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE #__rbo_cust (
  custId int(11) NOT NULL AUTO_INCREMENT,
  cust_name varchar(50) DEFAULT NULL,
  cust_fullname varchar(255) DEFAULT NULL,
  cust_email varchar(30) DEFAULT NULL,
  cust_data text DEFAULT NULL,
  cust_phone varchar(30) DEFAULT NULL,
  cust_rem text DEFAULT NULL,
  created_by varchar(30) NOT NULL,
  created_on datetime NOT NULL,
  modified_by varchar(30) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (custId)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 702
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE #__rbo_docs (
  docId int(11) NOT NULL AUTO_INCREMENT,
  doc_num int(11) DEFAULT NULL,
  doc_date date DEFAULT NULL,
  doc_type varchar(5) DEFAULT NULL COMMENT 'счет/накл/акт',
  doc_status varchar(15) DEFAULT NULL,
  doc_base int(11) DEFAULT NULL,
  custId int(11) DEFAULT NULL,
  doc_sum float DEFAULT 0,
  doc_manager varchar(30) NOT NULL,
  doc_firm varchar(10) NOT NULL DEFAULT 'ИП',
  doc_rem text DEFAULT NULL,
  created_by varchar(30) NOT NULL,
  created_on datetime NOT NULL,
  modified_by varchar(30) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (docId)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 169
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE #__rbo_opers (
  operId int(11) NOT NULL AUTO_INCREMENT,
  oper_type varchar(20) DEFAULT NULL,
  oper_date date DEFAULT NULL,
  custId int(11) DEFAULT NULL,
  productId int(11) DEFAULT NULL,
  product_code varchar(25) DEFAULT NULL,
  product_name varchar(255) DEFAULT NULL,
  product_price float DEFAULT NULL,
  product_cnt int(11) DEFAULT NULL,
  oper_sum float DEFAULT 0,
  oper_manager varchar(30) DEFAULT NULL,
  oper_firm varchar(10) DEFAULT '',
  oper_rem text DEFAULT NULL,
  oper_TZ varchar(5) DEFAULT NULL,
  created_by varchar(30) DEFAULT NULL,
  created_on datetime DEFAULT NULL,
  modified_by varchar(30) DEFAULT NULL,
  modified_on datetime DEFAULT NULL,
  PRIMARY KEY (operId),
  INDEX IDX_rbo_opers_custId (custId),
  INDEX IDX_rbo_opers_oper_date (oper_date),
  INDEX IDX_rbo_opers_oper_firm (oper_firm),
  INDEX IDX_rbo_opers_oper_manager (oper_manager)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 146
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE #__rbo_operstype (
  operRName varchar(255) NOT NULL,
  operEName varchar(255) DEFAULT NULL,
  signCash int(11) NOT NULL,
  fldCashSum varchar(50) DEFAULT NULL,
  signMove int(11) DEFAULT NULL,
  PRIMARY KEY (operRName),
  UNIQUE INDEX UK_SS_operstype_operRName (operRName)
)
ENGINE = INNODB
AVG_ROW_LENGTH = 41
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE #__rbo_products (
  productId int(11) NOT NULL AUTO_INCREMENT,
  product_code varchar(50) DEFAULT NULL,
  categoryId int(11) DEFAULT NULL,
  product_name varchar(255) DEFAULT NULL,
  product_price float DEFAULT NULL,
  product_in_stock int(11) DEFAULT NULL,
  product_price1 float DEFAULT NULL,
  product_type int(1) NOT NULL DEFAULT 1 COMMENT 'Если TRUE, то это товар, иначе услуга',
  price_name varchar(30) DEFAULT NULL,
  PRIMARY KEY (productId)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 687
CHARACTER SET utf8
COLLATE utf8_general_ci;

INSERT INTO #__rbo_operstype VALUES
  ('закуп', 'buy', -1, 'sPaySum', 1),
  ('продажа', 'sell', 1, 'sPaySum', -1),
  ('списание', 'remove', 0, 'sPaySum', -1),
  ('затраты-бухгал', NULL, -1, 'sPaySum', 0),
  ('затраты-налоги', NULL, -1, 'sPaySum', 0),
  ('затраты-прочие', NULL, -1, 'sPaySum', -1),
  ('затраты-зарплата', NULL, -1, 'sPaySum', 0),
  ('ддс', NULL, -1, 'sPaySum', 0),
  ('затраты-банков', NULL, -1, NULL, -1),
  ('затраты-произв', NULL, -1, NULL, -1),
  ('затраты-аренда', NULL, -1, NULL, -1),
  ('затраты-коммун', NULL, -1, NULL, -1),
  ('затраты-связь', NULL, -1, NULL, -1);