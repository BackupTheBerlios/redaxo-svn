<?php

/**
 * RSS Reader Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$error = '';

if($error == '')
{
  if (version_compare(PHP_VERSION, '4.3.0', '<'))
  {
    $error = 'This plugin requires at least PHP Version 4.3.0';
  }
}

if($error == '')
{
  if (!extension_loaded('xml'))
  {
    $error = 'Missing required PHP-Extension "xml"';
  }
  elseif (!extension_loaded('xmlreader'))
  {
    $error = 'Missing required PHP-Extension "xmlreader"';
  }
}

if($error == '')
{
  $file = dirname(__FILE__). '/cache';

  if(($state = rex_is_writable($file)) !== true)
    $error = $state;
}

if ($error != '')
  $REX['ADDON']['installmsg']['rss_reader'] = $error;
else
  $REX['ADDON']['install']['rss_reader'] = true;