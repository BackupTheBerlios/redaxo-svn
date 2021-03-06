<?php
/**
 * Getter Funktionen zum Handling von Superglobalen Variablen 
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

/**
 * Gibt die Superglobale variable $varname des Array $_GET zur�ck und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zur�ckgegeben
 */
function rex_get($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_GET, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_POST zur�ck und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zur�ckgegeben
 */
function rex_post($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_POST, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_REQUEST zur�ck und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zur�ckgegeben
 */
function rex_request($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_REQUEST, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_SERVER zur�ck und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zur�ckgegeben
 */
function rex_server($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_SERVER, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_SESSION zur�ck und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zur�ckgegeben
 */
function rex_session($varname, $vartype = '', $default = '')
{
  global $REX;

  if(isset($_SESSION[$varname][$REX['INSTNAME']]))
  {
    return _rex_cast_var($_SESSION[$varname][$REX['INSTNAME']], $vartype, $default, 'found');
  }
  
  if($default === '')
  {
    return _rex_cast_var($default, $vartype, $default, 'default');
  }
  return $default;
}

/**
 * Setzt den Wert einer Session Variable.
 * 
 * Variablen werden Instanzabh�ngig gespeichert.
 */
function rex_set_session($varname, $value)
{
  global $REX;

  $_SESSION[$varname][$REX['INSTNAME']] = $value;
}

/**
 * L�scht den Wert einer Session Variable.
 * 
 * Variablen werden Instanzabh�ngig gel�scht.
 */
function rex_unset_session($varname)
{
  global $REX;

  unset($_SESSION[$varname][$REX['INSTNAME']]);
}

/**
 * Gibt die Superglobale variable $varname des Array $_COOKIE zur�ck und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zur�ckgegeben
 */
function rex_cookie($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_COOKIE, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_FILES zur�ck und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zur�ckgegeben
 */
function rex_files($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_FILES, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_ENV zur�ck und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zur�ckgegeben
 */
function rex_env($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_ENV, $varname, $vartype, $default);
}

/**
 * Durchsucht das Array $haystack nach dem Schl�ssel $needle.
 *  
 * Falls ein Wert gefunden wurde wird dieser nach 
 * $vartype gecastet und anschlie�end zur�ckgegeben.
 * 
 * Falls die Suche erfolglos endet, wird $default zur�ckgegeben
 * 
 * @access private
 */
function _rex_array_key_cast($haystack, $needle, $vartype, $default = '')
{
  if(!is_array($haystack))
  {
    trigger_error('Array expected for $haystack in _rex_array_key_cast()!', E_USER_ERROR);
    exit();
  }
  
  if(!is_scalar($needle))
  {
    trigger_error('Scalar expected for $needle in _rex_array_key_cast()!', E_USER_ERROR);
    exit();
  }
  
  if(array_key_exists($needle, $haystack))
  {
    return _rex_cast_var($haystack[$needle], $vartype, $default, 'found');
  }

  if($default === '')
  {
    return _rex_cast_var($default, $vartype, $default, 'default');
  }
  return $default;
}

/**
 * Castet die Variable $var zum Typ $vartype
 * 
 * M�gliche PHP-Typen sind:
 *  - bool (auch boolean)
 *  - int (auch integer)
 *  - double
 *  - string
 *  - float
 *  - real
 *  - object
 *  - array
 *  - '' (nicht casten)
 *  
 * M�gliche REDAXO-Typen sind:
 *  - rex-article-id
 *  - rex-category-id
 *  - rex-clang-id
 *  - rex-template-id
 *  - rex-ctype-id
 *  - rex-slice-id
 *  - rex-module-id
 *  - rex-action-id
 *  - rex-media-id
 *  - rex-mediacategory-id
 *  - rex-user-id
 * 
 * @access private
 */
function _rex_cast_var($var, $vartype, $default, $mode)
{
  global $REX;
  
  if(!is_string($vartype))
  {
    trigger_error('String expected for $vartype in _rex_cast_var()!', E_USER_ERROR);
    exit(); 
  }
  
  switch($vartype)
  {
    // ---------------- REDAXO types
    case 'rex-article-id':
      $var = (int) $var;
      if($mode == 'found')
      {
        if(!OOArticle::isValid(OOArticle::getArticleById($var)))
          $var = (int) $default; 
      }
      break;
    case 'rex-category-id':
      $var = (int) $var;
      if($mode == 'found')
      {
        if(!OOCategory::isValid(OOCategory::getCategoryById($var)))
          $var = (int) $default;
      } 
      break;
    case 'rex-clang-id':
      $var = (int) $var;
      if($mode == 'found')
      {
        if(empty($REX['CLANG'][$var]))
          $var = (int) $default;
      }
      break;
    case 'rex-template-id':
    case 'rex-ctype-id':
    case 'rex-slice-id':
    case 'rex-module-id':
    case 'rex-action-id':
    case 'rex-media-id':
    case 'rex-mediacategory-id':
    case 'rex-user-id':
      // erstmal keine weitere validierung
      $var = (int) $var;
      break;
      
    // ---------------- PHP types
    case 'bool'   :
    case 'boolean':
      $var = (boolean) $var;
      break; 
    case 'int'    : 
    case 'integer':
      $var = (int)     $var;
      break; 
    case 'double' :
      $var = (double)  $var;
      break; 
    case 'float'  :
    case 'real'   :
      $var = (float)   $var;
      break; 
    case 'string' :
      $var = (string)  $var;
      break; 
    case 'object' :
      $var = (object)  $var;
      break; 
    case 'array'  :
      if(empty($var))
        $var = array();
      else 
        $var = (array) $var;
      break;

    // kein Cast, nichts tun
    case ''       : break;
    
    // Evtl Typo im vartype, deshalb hier fehlermeldung!
    default: trigger_error('Unexpected vartype "'. $vartype .'" in _rex_cast_var()!', E_USER_ERROR); exit(); 
  }
  
  return $var;
}