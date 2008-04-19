<?php

$mypage = "simple_user"; 				// only for this file

$I18N_SIMPLE_USER = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/addons/$mypage/lang/"); 	// CREATE LANG OBJ FOR THIS ADDON

$REX[ADDON][rxid][$mypage] = "2";			// unique id /
// $REX[ADDON][nsid][$mypage] = "REX002,REX003";	// necessary rxid; - not yet included
$REX[ADDON][page][$mypage] = "$mypage";			// pagename/foldername
$REX[ADDON][name][$mypage] = $I18N_SIMPLE_USER->msg("simple_user");		// name
$REX[ADDON][perm][$mypage] = "simple_user[]"; 		// permission

$REX[PERM][] = "simple_user[]";

$TABLE['simple_user'] = "rex_2_user";
$REX[ADDON][extras][$REX[ADDON][rxid][$mypage]][TABLE] = $TABLE['simple_user'] ;

// IF NECESSARY INCLUDE FUNC/CLASSES ETC
// INCLUDE IN FRONTEND --- if ($REX[GG]) 
// INCLUDE IN BACKEND --- if (!$REX[GG]) 

?>