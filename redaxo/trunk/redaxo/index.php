<?php

/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */

// ----- caching start f�r output filter
ob_start();
ob_implicit_flush(0);

// ----------------- MAGIC QUOTES CHECK
require './include/functions/function_rex_mquotes.inc.php';

// ----- REX UNSET
unset($REX);

// Flag ob Inhalte mit Redaxo aufgerufen oder
// von der Webseite aus
// Kann wichtig f�r die Darstellung sein
// Sollte immer true bleiben

$REX['REDAXO'] = true;

// Wenn $REX[GG] = true; dann wird der
// Content aus den redaxo/include/generated/
// genommen

$REX['GG'] = false;

// setzte pfad und includiere klassen und funktionen
$REX['HTDOCS_PATH'] = '../';
require 'include/master.inc.php';

// ----- addon/normal page path
$REX['PAGEPATH'] = '';

// ----- pages, verfuegbare seiten
// array(name,addon=1,htmlheader=1);
$REX['PAGES'] = array();
$REX['PAGE'] = '';

// ----------------- SETUP
$REX['USER'] = NULL;
$REX['LOGIN'] = NULL;

if ($REX['SETUP'])
{
  // ----------------- SET SETUP LANG
  $REX['LANG'] = '';
  $requestLang = rex_request('lang', 'string');
  $langpath = $REX['INCLUDE_PATH'].'/lang';
  $REX['LANGUAGES'] = array();
  if ($handle = opendir($langpath))
  {
    while (false !== ($file = readdir($handle)))
    {
      if (substr($file,-5) == '.lang')
      {
        $locale = substr($file,0,strlen($file)-strlen(substr($file,-5)));
        $REX['LANGUAGES'][] = $locale;
        if($requestLang == $locale)
          $REX['LANG'] = $locale;
      }
    }
  }
  closedir($handle);
  if($REX['LANG'] == '')
    $REX['LANG'] = 'de_de';

  $I18N = rex_create_lang($REX['LANG']);
  
  $REX['PAGES']['setup'] = rex_be_navigation::getSetupPage();
  $REX['PAGE'] = "setup";

}else
{
  // ----------------- CREATE LANG OBJ
  $I18N = rex_create_lang($REX['LANG']);

  // ---- prepare login
  $REX['LOGIN'] = new rex_backend_login($REX['TABLE_PREFIX'] .'user');
  $rex_user_login = rex_post('rex_user_login', 'string');
  $rex_user_psw = rex_post('rex_user_psw', 'string');

  if ($REX['PSWFUNC'] != '')
    $REX['LOGIN']->setPasswordFunction($REX['PSWFUNC']);

  if (rex_get('rex_logout', 'boolean'))
    $REX['LOGIN']->setLogout(true);

  $REX['LOGIN']->setLogin($rex_user_login, $rex_user_psw);
  $loginCheck = $REX['LOGIN']->checkLogin();

  $rex_user_loginmessage = "";
  if ($loginCheck !== true)
  {
    // login failed
    $rex_user_loginmessage = $REX['LOGIN']->message;

    // Fehlermeldung von der Datenbank
    if(is_string($loginCheck))
      $rex_user_loginmessage = $loginCheck;

    $REX['PAGES']['login'] = rex_be_navigation::getLoginPage();
    $REX['PAGE'] = 'login';
    
    $REX['USER'] = null;
    $REX['LOGIN'] = null;
  }
  else
  {    
    // Userspezifische Sprache einstellen, falls gleicher Zeichensatz
    $lang = $REX['LOGIN']->getLanguage();
    $I18N_T = rex_create_lang($lang,'',FALSE);
    if ($I18N->msg('htmlcharset') == $I18N_T->msg('htmlcharset')) 
      $I18N = rex_create_lang($lang);

    $REX['USER'] = $REX['LOGIN']->USER;
  }
}

// ----- Prepare Core Pages
if($REX['USER'])
{
  $REX['PAGES'] = rex_be_navigation::getLoggedInPages($REX['USER']);
}

// ----- INCLUDE ADDONS
include_once $REX['INCLUDE_PATH'].'/addons.inc.php';

// ----- Prepare AddOn Pages
if($REX['USER'])
{
  if (is_array($REX['ADDON']['status']))
    reset($REX['ADDON']['status']);

  $onlineAddons = array_filter(array_values($REX['ADDON']['status']));
  if(count($onlineAddons) > 0)
  {
    for ($i = 0; $i < count($REX['ADDON']['status']); $i++)
    {
      $apage = key($REX['ADDON']['status']);
      
      $title = '';
      $href = '';
      
      if(isset($REX['ADDON']['name'][$apage]))
        $title = $REX['ADDON']['name'][$apage];
        
      if(isset($REX['ADDON']['link'][$apage]) && $REX['ADDON']['link'][$apage] != '')
        $href = $REX['ADDON']['link'][$apage];
      else
        $href = 'index.php?page='.$apage;
      
      $addonPage = new rex_be_main_page($title, 'addons', array('page' => $apage));
      $addonPage->setHref($href);
      
      $perm = '';
      if(isset ($REX['ADDON']['perm'][$apage]))
        $perm = $REX['ADDON']['perm'][$apage];
        
      if (current($REX['ADDON']['status']) == 1 && $title != '' && ($perm == '' || $REX['USER']->hasPerm($perm) || $REX['USER']->isAdmin()))
      {
        // wegen REX Version <= 4.2 - alter Stil "SUBPAGES"
        if(isset($REX['ADDON'][$apage]['SUBPAGES']))
        {
          $REX['ADDON']['subpages'][$apage] = $REX['ADDON'][$apage]['SUBPAGES'];
          unset($REX['ADDON'][$apage]['SUBPAGES']);
        }
        // *** ENDE wegen <=4.2
        
        // add be_page's as subpages
        if(isset($REX['ADDON']['subpages'][$apage]) &&
           is_array($REX['ADDON']['subpages'][$apage]))
        {
           foreach($REX['ADDON']['subpages'][$apage] as $s)
           {
             if(is_array($s))
             {
               $subPage = new rex_be_page($s[1], array('page' => $apage, 'subpage' => $s[0]));
               $subPage->setHref('index.php?page='.$apage.'&subpage='.$s[0]);
               $addonPage->addSubPage($subPage);
             }
             else if(rex_be_main_page::isValid($s))
             {
               $REX['PAGES'][$apage.'_'.$s->getTitle()] = $s;
             }
             else if(rex_be_page::isValid($s))
             {
               $addonPage->addSubPage($s);
             }
           }
        }
        
        // navigation to add attributes to the addon-root page
        if(isset($REX['ADDON']['navigation'][$apage]) &&
           is_array($REX['ADDON']['navigation'][$apage]))
        {
          foreach($REX['ADDON']['navigation'][$apage] as $key => $value)
          {
            $addonPage->_set($key, $value);
          }
        }
          
        $REX['PAGES'][$apage] = $addonPage;
      }
      next($REX['ADDON']['status']);
    }
  }
}

// Set Startpage
if($REX['USER'])
{
  $REX['USER']->pages = $REX['PAGES'];

  // --- page herausfinden
  $REX['PAGE'] = trim(rex_request('page', 'string'));
    
  // --- invalide page, neue page bestimmen und diese in neuem request dann verarbeiten
  if(!isset($REX['PAGES'][$REX['PAGE']]))
  {
    $REX['PAGE'] = $REX['LOGIN']->getStartpage();
    if(!isset($REX['PAGES'][$REX['PAGE']]))
    {
      $REX['PAGE'] = $REX['START_PAGE'];
      if(!isset($REX['PAGES'][$REX['PAGE']]))
      {
        $REX['PAGE'] = 'profile';
      }
    }
    
    header('Location: index.php?page='. $REX['PAGE']);
    exit();
  }
}

$REX['PAGE_NO_NAVI'] = !$REX['PAGES'][$REX['PAGE']]->hasNavigation();


// ----- EXTENSION POINT
// page variable validated
// TODO Remove, obsolete
rex_register_extension_point( 'PAGE_CHECKED', $REX['PAGE'], array('pages' => $REX['PAGES']));


/*if(isset($REX['PAGES'][$REX['PAGE']]['PATH']) && $REX['PAGES'][$REX['PAGE']]['PATH'] != '')
{
  // If page has a new/overwritten path
  require $REX['PAGES'][$REX['PAGE']]['PATH'];

}else
*/
if($REX['PAGES'][$REX['PAGE']]->isCorePage())
{
  // Core Page
  require $REX['INCLUDE_PATH'].'/layout/top.php';
  require $REX['INCLUDE_PATH'].'/pages/'. $REX['PAGE'] .'.inc.php';
  require $REX['INCLUDE_PATH'].'/layout/bottom.php';
}else
{
  // Addon Page
  require $REX['INCLUDE_PATH'].'/addons/'. $REX['PAGE'] .'/pages/index.inc.php';
}
// ----- caching end f�r output filter
$CONTENT = ob_get_contents();
ob_end_clean();

// ----- inhalt ausgeben
rex_send_article(null, $CONTENT, 'backend', TRUE);