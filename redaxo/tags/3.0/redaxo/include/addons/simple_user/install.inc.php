<?php

// CREATE/UPDATE DATABASE
$sql = new sql;
// $sql->debugsql=1;


// ----- usertabelle
$sql->setQuery("
CREATE TABLE `rex_2_user` (
`id` INT NOT NULL AUTO_INCREMENT ,
`user_login` VARCHAR( 255 ) NOT NULL ,
`user_password` VARCHAR( 255 ) NOT NULL ,
`user_name` VARCHAR( 255 ) NOT NULL ,
`user_firstname` VARCHAR( 255 ) NOT NULL ,
`user_gender` VARCHAR( 1 ) NOT NULL ,
`user_birthdate` VARCHAR( 255 ) NOT NULL ,
`user_eyecolor` VARCHAR( 255 ) NOT NULL ,
`user_haircolor` VARCHAR( 255 ) NOT NULL ,
`user_street` VARCHAR( 255 ) NOT NULL ,
`user_plz` VARCHAR( 255 ) NOT NULL ,
`user_town` VARCHAR( 255 ) NOT NULL ,
`user_phone` VARCHAR( 255 ) NOT NULL ,
`user_mobile` VARCHAR( 255 ) NOT NULL ,
`user_email` VARCHAR( 255 ) NOT NULL ,
`user_icq` VARCHAR( 255 ) NOT NULL ,
`user_aim` VARCHAR( 255 ) NOT NULL ,
`user_msn` VARCHAR( 255 ) NOT NULL ,
`user_skype` VARCHAR( 255 ) NOT NULL ,
`user_private_data_public` INT NOT NULL ,
`company_name` VARCHAR( 255 ) NOT NULL ,
`company_department` VARCHAR( 255 ) NOT NULL ,
`company_operating_field` VARCHAR( 255 ) NOT NULL ,
`company_street` VARCHAR( 255 ) NOT NULL ,
`company_plz` VARCHAR( 255 ) NOT NULL ,
`company_town` VARCHAR( 255 ) NOT NULL ,
`company_phone` VARCHAR( 255 ) NOT NULL ,
`company_mobile` VARCHAR( 255 ) NOT NULL ,
`company_email` VARCHAR( 255 ) NOT NULL ,
`company_data_public` INT NOT NULL ,
`personally_positive_characteristics` TEXT NOT NULL ,
`personally_negaitve_characteristics` TEXT NOT NULL ,
`personally_hobby` TEXT NOT NULL ,
`personally_favorite_place` TEXT NOT NULL ,
`personally_slogan` TEXT NOT NULL ,
`personally_data_public` INT NOT NULL ,
`info_newsletter` INT NOT NULL ,
`info_mail` INT NOT NULL ,
`user_status` INT NOT NULL ,
`user_typ` INT NOT NULL ,
`user_file1` VARCHAR( 255 ) NOT NULL ,
`user_file2` VARCHAR( 255 ) NOT NULL ,
`login_activation` INT NOT NULL ,
`activation_key` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
);");

// ----- gruppentabelle
$sql->setQuery("
CREATE TABLE `rex_2_group` (
`id` INT NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 255 ) NOT NULL ,
`extras` TEXT NOT NULL ,
PRIMARY KEY ( `id` )
);");

// ----- gruppen-user relation
$sql->setQuery("
CREATE TABLE `rex_2_u_g` (
`id` INT NOT NULL AUTO_INCREMENT ,
`user_id` INT NOT NULL ,
`group_id` INT NOT NULL ,
PRIMARY KEY ( `id` )
);");


// CREATE/UPDATE MODULES


// CREATE/UPDATE PAGES


// CREATE/UPDATE FILES


// REGENERATE SITE


$REX[ADDON][install]["simple_user"] = 1;

// ERRMSG IN CASE: $REX[ADDON][installmsg]["import_export"] = "Leider konnte nichts installiert werden da.";


?>