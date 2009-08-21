<?php

$mypage = "community"; // only for this file

// ********** Allgemeine AddOn Config
$REX['ADDON']['rxid'][$mypage] = '5';
$REX['ADDON']['page'][$mypage] = "$mypage";     // pagename/foldername
$REX['ADDON']['name'][$mypage] = "Community";   // name
$REX['ADDON']['perm'][$mypage] = "community[]"; // benoetigt mindest permission
$REX['ADDON']['version'][$mypage] = '1.4';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['PERM'][] = "community[]";

if (isset($I18N) && is_object($I18N))
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

// ********** Community User Funktionen
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_user.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_replace.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_paginate.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_formatter.inc.php";

include $REX["INCLUDE_PATH"]."/addons/community/classes/class.rex_com.inc.php";

// ********** Backend, Perms, Subpages etc.
if ($REX["REDAXO"] && $REX['USER'])
{
	$REX['EXTRAPERM'][] = "community[admin]";
	$REX['EXTRAPERM'][] = "community[users]";
	include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.userconfig.inc.php";
	
	// $REX['ADDON'][$mypage]['SUBPAGES'] = array();
	$REX['ADDON'][$mypage]['SUBPAGES'][] = array( '' , '&Uuml;bersicht');
	
	// Feste Subpages
	if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","community[users]")) 
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('user' , 'User Verwaltung');
	if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","community[admin]")) 
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('user_fields' , 'User Felder erweitern');
	
	if($REX["REDAXO"])
	{
		function rex_community_addCSS($params)
		{
		    echo "\n".'<link rel="stylesheet" type="text/css" href="../files/addons/community/community_be.css" media="screen, projection, print" />';
		}
		rex_register_extension('PAGE_HEADER', 'rex_community_addCSS');
	}
}


// allgemeine feldtypen

$REX["ADDON"]["community"]["ut"] = array();
$REX["ADDON"]["community"]["ut"][1] = "INT(11)";
$REX["ADDON"]["community"]["ut"][2] = "VARCHAR(255)";
$REX["ADDON"]["community"]["ut"][3] = "TEXT";
$REX["ADDON"]["community"]["ut"][4] = "PASSWORD";
$REX["ADDON"]["community"]["ut"][5] = "SELECT";
$REX["ADDON"]["community"]["ut"][6] = "BOOL";
$REX["ADDON"]["community"]["ut"][7] = "FLOAT(10,7) f�r Positionen wie lat und lng";
$REX["ADDON"]["community"]["ut"][8] = "SQL SELECT";

// feste felder
$REX["ADDON"]["community"]["ff"] = array();
$REX["ADDON"]["community"]["ff"][] = "id";
$REX["ADDON"]["community"]["ff"][] = "login";
$REX["ADDON"]["community"]["ff"][] = "password";
$REX["ADDON"]["community"]["ff"][] = "email";
$REX["ADDON"]["community"]["ff"][] = "status";
$REX["ADDON"]["community"]["ff"][] = "name";
$REX["ADDON"]["community"]["ff"][] = "firstname";
$REX["ADDON"]["community"]["ff"][] = "activation_key";

/*
$ff[] = "session_id";
$ff[] = "last_xs";
$ff[] = "last_login";
$ff[] = "email_checked";
$ff[] = "activation_key";
$ff[] = "last_newsletterid";

$ff[] = "gender";
$ff[] = "street";
$ff[] = "zip";
$ff[] = "city";
$ff[] = "phone";
$ff[] = "birthday";

*/







// ********** XForm values/action/validations einbinden

$REX['ADDON']['community']['xform_path']['value'] = array($REX['INCLUDE_PATH'].'/addons/community/xform/classes/value/');
$REX['ADDON']['community']['xform_path']['validate'] = array($REX['INCLUDE_PATH'].'/addons/community/xform/classes/validate/');
$REX['ADDON']['community']['xform_path']['action'] = array($REX['INCLUDE_PATH'].'/addons/community/xform/classes/action/');

rex_register_extension('ADDONS_INCLUDED', 'rex_com_xform_add');
function rex_com_xform_add($params){
	global $REX;
	foreach($REX['ADDON']['community']['xform_path']['value'] as $value)
	{
		$REX['ADDON']['xform']['classpaths']['value'][] = $value;
	}
	foreach($REX['ADDON']['community']['xform_path']['validate'] as $validate)
	{
		$REX['ADDON']['xform']['classpaths']['validate'][] = $validate;
	}
	foreach($REX['ADDON']['community']['xform_path']['action'] as $action)
	{
		$REX['ADDON']['xform']['classpaths']['action'][] = $action;
	}

}