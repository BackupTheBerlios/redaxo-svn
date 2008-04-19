<?php

/** 
 * Addonlist
 * @package redaxo3 
 * @version $Id: addons.inc.php,v 1.5 2005/12/27 15:36:11 kristinus Exp $ 
 */ 

// ----------------- addons
if (isset($REX['ADDON']['status'])) {
  unset($REX['ADDON']['status']);
}

// ----------------- DONT EDIT BELOW THIS
// --- DYN

$REX['ADDON']['install']['image_resize'] = 1;
$REX['ADDON']['status']['image_resize'] = 1;

$REX['ADDON']['install']['import_export'] = 1;
$REX['ADDON']['status']['import_export'] = 1;

$REX['ADDON']['install']['stats'] = 0;
$REX['ADDON']['status']['stats'] = 0;

// --- /DYN
// ----------------- /DONT EDIT BELOW THIS


for($i=0;$i<count($REX['ADDON']['status']);$i++)
{
	if (current($REX['ADDON']['status']) == 1) include $REX['INCLUDE_PATH']."/addons/".key($REX['ADDON']['status'])."/config.inc.php";
	next($REX['ADDON']['status']);
}

// ----- all addons configs included
rex_register_extension_point( 'ADDONS_INCLUDED');

?>