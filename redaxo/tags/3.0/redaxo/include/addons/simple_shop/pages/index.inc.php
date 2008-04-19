<?php



$mypage = "simple_shop";

switch($subpage){
	
	case "relations":
		include $REX[INCLUDE_PATH]."/addons/$page/pages/relation.inc.php";
	break;
	
	case "setup":
	    include $REX[INCLUDE_PATH]."/layout/top.php";
		title($I18N_SIMPLE_SHOP->msg("simple_shop"), "&nbsp;&nbsp;&nbsp;<a href='index.php?page=$page' target='_self'>".$I18N_SIMPLE_SHOP->msg("catverwaltung")."</a> | <a href='index.php?page=$page&subpage=orders' target='_self'>".$I18N_SIMPLE_SHOP->msg("bestverwaltung")."</a><!-- | ".$I18N_SIMPLE_SHOP->msg("mainconfig")." -->");
		include $REX[INCLUDE_PATH]."/addons/$page/pages/setup.inc.php";
		include $REX[INCLUDE_PATH]."/layout/bottom.php";
	break;
	
	case "orders":
	    include $REX[INCLUDE_PATH]."/layout/top.php";
		title($I18N_SIMPLE_SHOP->msg("simple_shop"), "&nbsp;&nbsp;&nbsp;<a href='index.php?page=$page' target='_self'>".$I18N_SIMPLE_SHOP->msg("catverwaltung")."</a> | ".$I18N_SIMPLE_SHOP->msg("bestverwaltung")."<!-- | <a href='index.php?page=$page&subpage=setup' target='_self'>".$I18N_SIMPLE_SHOP->msg("mainconfig")."</a>-->");
		include $REX[INCLUDE_PATH]."/addons/$page/pages/orders.inc.php";
		include $REX[INCLUDE_PATH]."/layout/bottom.php";
	break;
	
	default:
	    include $REX[INCLUDE_PATH]."/layout/top.php";
		title($I18N_SIMPLE_SHOP->msg("simple_shop"), "&nbsp;&nbsp;&nbsp;".$I18N_SIMPLE_SHOP->msg("catverwaltung")." | <a href='index.php?page=$page&subpage=orders' target='_self'>".$I18N_SIMPLE_SHOP->msg("bestverwaltung")."</a><!-- | <a href='index.php?page=$page&subpage=setup' target='_self'>".$I18N_SIMPLE_SHOP->msg("mainconfig")."</a>-->");
		include $REX[INCLUDE_PATH]."/addons/$page/pages/articles.inc.php";
		include $REX[INCLUDE_PATH]."/layout/bottom.php";

}


?>

