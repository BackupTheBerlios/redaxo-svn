<?php

// INSTALL DB

$installsql = new sql;
// $installsql->debugsql = 1;
// $installsql->query("DROP TABLE IF EXISTS `".$REX[ADDON][tbl][art]["simple_shop"]."`");
$installsql->query("CREATE TABLE `rex_4_article` (
  `id` int(11) NOT NULL default '0',
  `clang` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `category` varchar(255) NOT NULL default '0',
  `description` text NOT NULL,
  `artnr` varchar(255) NOT NULL default '',
  `mwst` float NOT NULL default '0',
  `price` float NOT NULL default '0',
  `old_price` float NOT NULL ,
  `deliver_price` float NOT NULL ,
  `detaildesc` text NOT NULL ,
  `thumbnail` varchar(255) NOT NULL default '',
  `picture` varchar(255) NOT NULL default '',
  `relation_1` int(11) NOT NULL default '0',
  `relation_2` int(11) NOT NULL default '0',
  `relation_3` int(11) NOT NULL default '0',
  `prio` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0'
) ");

$installsql->query("CREATE TABLE `rex_4_order` (
`id` INT NOT NULL AUTO_INCREMENT ,
`user_id` INT NOT NULL ,
`overallsum` FLOAT NOT NULL ,
`status` INT NOT NULL ,
`date` DATETIME NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`mailtext` TEXT NOT NULL,
PRIMARY KEY ( `id` )
) ");

$installsql->query("CREATE TABLE `rex_4_order_product` (
`id` INT NOT NULL AUTO_INCREMENT ,
`product_name` VARCHAR( 255 ) NOT NULL ,
`order_id` INT NOT NULL ,
`product_id` INT NOT NULL ,
`amount` INT NOT NULL ,
`price` FLOAT NOT NULL ,
PRIMARY KEY ( `id` )
) ");

$installsql->query("ALTER TABLE `rex_4_article` ADD `instock` TINYINT( 1 ) NOT NULL AFTER `detaildesc` , ADD `stockinfo` VARCHAR( 255 ) NOT NULL AFTER `instock` ");

$REX[ADDON][install]["simple_shop"] = 1;
// ERRMSG IN CASE: $REX[ADDON][installmsg]["simple_shop"] = "Leider konnte nichts installiert werden da.";

?>