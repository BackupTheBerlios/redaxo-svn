<?php

// ----------------- addons
unset($REX['ADDON']['status']);

// ----------------- DONT EDIT BELOW THIS
// --- DYN

$REX['ADDON']['install']['image_resize'] = 1;
$REX['ADDON']['status']['image_resize'] = 1;

$REX['ADDON']['install']['import_export'] = 1;
$REX['ADDON']['status']['import_export'] = 1;

$REX['ADDON']['install']['simple_shop'] = 0;
$REX['ADDON']['status']['simple_shop'] = 0;

$REX['ADDON']['install']['simple_user'] = 0;
$REX['ADDON']['status']['simple_user'] = 0;

$REX['ADDON']['install']['stats'] = 1;
$REX['ADDON']['status']['stats'] = 1;

// --- /DYN
// ----------------- /DONT EDIT BELOW THIS


for($i=0;$i<count($REX['ADDON']['status']);$i++)
{
	if (current($REX['ADDON']['status']) == 1) include $REX['INCLUDE_PATH']."/addons/".key($REX['ADDON']['status'])."/config.inc.php";
	next($REX['ADDON']['status']);
}

?>