<?php

/**
 * Addonlist
 * @package redaxo4
 * @version $Id: addons.inc.php,v 1.1 2008/03/26 13:34:12 kills Exp $
 */

// ----------------- addons
if (isset($REX['ADDON']['status'])) {
  unset($REX['ADDON']['status']);
}

// ----------------- DONT EDIT BELOW THIS
// --- DYN

// --- /DYN
// ----------------- /DONT EDIT BELOW THIS

foreach(OOAddon::getAvailableAddons() as $addonName)
{
  // Warnungen unterdr�cken ist schneller als ein file_exists
  @include $REX['INCLUDE_PATH'].'/addons/'.$addonName.'/config.inc.php';
}

// ----- all addons configs included
rex_register_extension_point( 'ADDONS_INCLUDED');

?>