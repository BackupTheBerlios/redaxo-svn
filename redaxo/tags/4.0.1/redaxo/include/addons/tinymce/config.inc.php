<?php

/**
 * TinyMCE Addon
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 *
 * @package redaxo4
 * @version $Id: config.inc.php,v 1.11 2007/11/08 21:00:47 kills Exp $
 */

$mypage = 'tinymce';

$REX['ADDON']['rxid'][$mypage] = '52';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'TinyMCE';
$REX['ADDON']['perm'][$mypage] = 'tiny_mce[]';
$REX['ADDON']['version'][$mypage] = '1.0';
$REX['ADDON']['author'][$mypage] = 'Wolfgang Hutteger, Markus Staab, Dave Holloway';

$I18N_A52 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');

// Include tinylib
if($REX['REDAXO'])
{
  $cssLink = '  <link rel="stylesheet" type="text/css" href="../files/tmp_/tinymce/tinymce.css" />'."\n";
  rex_register_extension('PAGE_HEADER', create_function('$params', 'return $params[\'subject\'].\''. $cssLink .'\';'));

	include_once $REX['INCLUDE_PATH'].'/addons/tinymce/classes/class.tiny.inc.php';
}
?>