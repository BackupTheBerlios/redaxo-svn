<?php

/**
 * Backend Search Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id: config.inc.php,v 1.13 2008/03/26 21:06:37 kills Exp $
 */

$mypage = 'be_search';

/* Addon Parameter */
$REX['ADDON']['rxid'][$mypage] = '256';
$REX['ADDON']['page'][$mypage] = $mypage;
//$REX['ADDON']['name'][$mypage] = 'Backend Search';
//$REX['ADDON']['perm'][$mypage] = 'be_search[]';
$REX['ADDON']['version'][$mypage] = '1.2';
$REX['ADDON']['author'][$mypage] = 'Markus Staab';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

// Suchmodus
// global => Es werden immer alle Kategorien durchsucht
// local => Es werden immer die aktuelle+Unterkategorien durchsucht
// $REX['ADDON']['searchmode'][$mypage] = 'global';
$REX['ADDON']['searchmode'][$mypage] = 'local';

$REX['EXTPERM'][] = 'be_search[mediapool]';
$REX['EXTPERM'][] = 'be_search[structure]';

if ($REX['REDAXO'])
{
  if(rex_get('css', 'string') == 'addons/'. $mypage)
  {
    $cssfile = $REX['INCLUDE_PATH'] .'/addons/'. $mypage .'/css/be_search.css';
    rex_send_file($cssfile, 'text/css');
    exit();
  }
  if(rex_get('css', 'string') == 'addons/'. $mypage .'/ie7')
  {
    $cssfile = $REX['INCLUDE_PATH'] .'/addons/'. $mypage .'/css/be_search_ie_lte_7.css';
    rex_send_file($cssfile, 'text/css');
    exit();
  }

  $I18N_BE_SEARCH = new i18n($REX['LANG'], $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

  // Include Functions
  require_once $REX['INCLUDE_PATH'].'/addons/be_search/functions/functions.search.inc.php';
  
  rex_register_extension('PAGE_CHECKED', 'rex_a256_extensions_handler');
}
