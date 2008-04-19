<?php

/** 
 * Klasse zum prüfen ob Addons installiert/aktiviert sind
 * @package redaxo3 
 * @version $Id: class.ooaddon.inc.php,v 1.3 2005/12/27 14:38:24 kristinus Exp $ 
 */ 

class OOAddon
{
  function isAvailable($addon)
  {
    return OOAddon::isInstalled($addon) && OOAddon::isActivated($addon);
  }

  function isActivated($addon)
  {
    global $REX;
    return isset( $REX['ADDON']['status'][$addon]) && $REX['ADDON']['status'][$addon] == 1;
  }
  function isInstalled($addon)
  {
    global $REX;
    return isset( $REX['ADDON']['install'][$addon]) && $REX['ADDON']['install'][$addon] == 1;
  }
}
?>