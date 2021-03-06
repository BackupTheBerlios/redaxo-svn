<?php

/**
 * Textile Addon
 *  
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo4
 * @version $Id: config.inc.php,v 1.10 2007/10/13 13:52:01 kills Exp $
 */

$mypage = 'textile';

$REX['ADDON']['rxid'][$mypage] = '79';
$REX['ADDON']['page'][$mypage] = $mypage;    
$REX['ADDON']['name'][$mypage] = 'Textile';
$REX['ADDON']['perm'][$mypage] = 'textile[]';
$REX['ADDON']['version'][$mypage] = "1.0";
$REX['ADDON']['author'][$mypage] = "Dean Allen www.textism.com, Markus Staab";
// $REX['ADDON']['supportpage'][$mypage] = "";

$REX['PERM'][] = 'textile[]';
$REX['EXTPERM'][] = 'textile[help]';

$I18N_A79 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/'); 

require_once($REX['INCLUDE_PATH']. '/addons/textile/classes/class.textile.inc.php');
require_once $REX['INCLUDE_PATH']. '/addons/textile/functions/function_textile.inc.php';

if ($REX['REDAXO'])
{
  require_once $REX['INCLUDE_PATH'].'/addons/textile/functions/function_help.inc.php';
}
 
?>