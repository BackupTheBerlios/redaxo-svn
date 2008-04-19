<?php

$mypage = "simple_user";

include_once $REX[INCLUDE_PATH]."/addons/$mypage/classes/class.rexform.inc.php";
include_once $REX[INCLUDE_PATH]."/addons/$mypage/classes/class.rexlist.inc.php";
include_once $REX[INCLUDE_PATH]."/addons/$mypage/classes/class.rexselect.inc.php";

include $REX[INCLUDE_PATH]."/layout/top.php";

if ($subpage == "group")
{
	title($I18N_SIMPLE_USER->msg("simple_user"), "&nbsp;&nbsp;&nbsp;<a href='index.php?page=$page&subpage=user' target='_Self'>".$I18N_SIMPLE_USER->msg("simple_user")."</a> | <a href='index.php?page=$page&subpage=group' target='_Self'>".$I18N_SIMPLE_USER->msg("uw_group")."</a>");
}else
{
	$subpage = "user";
	title($I18N_SIMPLE_USER->msg("simple_user"), "&nbsp;&nbsp;&nbsp;<a href='index.php?page=$page&subpage=user' target='_Self'>".$I18N_SIMPLE_USER->msg("simple_user")."</a> | <a href='index.php?page=$page&subpage=group' target='_Self'>".$I18N_SIMPLE_USER->msg("uw_group")."</a>");
}

include $REX[INCLUDE_PATH]."/addons/$mypage/pages/$subpage.inc.php";


include $REX[INCLUDE_PATH]."/layout/bottom.php";
?>

