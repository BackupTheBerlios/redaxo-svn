<?php

$mypage = "simple_shop"; // only for this file

include_once $REX[INCLUDE_PATH]."/addons/$mypage/classes/class.shop_article.inc.php";
include_once $REX[INCLUDE_PATH]."/addons/$mypage/classes/class.shop_category.inc.php";
include_once $REX[INCLUDE_PATH]."/addons/$mypage/classes/class.shop_order.inc.php";

if (!$REX[GG])
{
	// only backend

	$I18N_SIMPLE_SHOP = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/addons/$mypage/lang/"); 	// CREATE LANG OBJ FOR THIS ADDON
	$REX[ADDON][rxid][$mypage] = "4"; // unique redaxo addon id
	// $REX[ADDON][nsid][$mypage] = "REX002,REX003";	// necessary rxid; - not yet included
	$REX[ADDON][page][$mypage] = "$mypage";			// pagename/foldername
	$REX[ADDON][name][$mypage] = $I18N_SIMPLE_SHOP->msg("simple_shop");
	$REX[ADDON][perm][$mypage] = "simple_shop[]"; 		// permission
	$REX[PERM][] = "simple_shop[]";

}

// backend and frontend

$REX[ADDON][tbl][art][$mypage] = "rex_4_article"; // article tabelle
$REX[ADDON][tbl][ord][$mypage] = "rex_4_order";
$REX[ADDON][tbl][ord_product][$mypage] = "rex_4_order_product";


// Defaultwerte:
if($page=="simple_shop" && $function == "edit_article" && $send!=1 && $aid<1){

	$article['deliverprice']	= 13;
	$article['tax']				= 16;

}


?>
