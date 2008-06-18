<?php

/**
 * PHPMailer Addon
 *  
 * @author staab[at]public-4u[dot]de Markus Staab
 * 
 * @package redaxo4
 * @version $Id: config.inc.php,v 1.6 2007/10/13 13:52:01 kills Exp $
 */

$mypage = 'phpmailer';

$REX['ADDON']['rxid'][$mypage] = '93';
$REX['ADDON']['page'][$mypage] = $mypage;    
$REX['ADDON']['name'][$mypage] = 'PHPMailer';
$REX['ADDON']['perm'][$mypage] = 'phpmailer[]';
$REX['ADDON']['version'][$mypage] = "1.0";
$REX['ADDON']['author'][$mypage] = "Brent R. Matzelle, Markus Staab";
// $REX['ADDON']['supportpage'][$mypage] = "";

$REX['PERM'][] = 'phpmailer[]';
$I18N_A93 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/'); 

require_once($REX['INCLUDE_PATH']. '/addons/phpmailer/classes/class.phpmailer.inc.php');
require_once($REX['INCLUDE_PATH']. '/addons/phpmailer/classes/class.rex_mailer.inc.php');

?>