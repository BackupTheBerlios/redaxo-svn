<?php

/**
 * Addonlist
 * @package redaxo4
 * @version svn:$Id$
 */

// ----------------- addons
unset($REX['ADDON']);

// ----------------- DONT EDIT BELOW THIS
// --- DYN

// --- /DYN
// ----------------- /DONT EDIT BELOW THIS

require $REX['INCLUDE_PATH']. '/plugins.inc.php';

foreach(OOAddon::getAvailableAddons() as $addonName)
{
  $addonConfig = rex_addons_folder($addonName). 'config.inc.php';
  if(file_exists($addonConfig))
  {
    require $addonConfig;
  }
  
  foreach(OOPlugin::getAvailablePlugins($addonName) as $pluginName)
  {
    $pluginConfig = rex_plugins_folder($addonName, $pluginName). 'config.inc.php';
    if(file_exists($pluginConfig))
    {
      rex_pluginManager::addon2plugin($addonName, $pluginName, $pluginConfig);
    }
  }
}

// ----- all addons configs included
rex_register_extension_point('ADDONS_INCLUDED');