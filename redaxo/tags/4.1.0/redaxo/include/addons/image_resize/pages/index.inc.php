<?php

/**
 * Image-Resize Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]yakmara[dot]de Jan Kristinus
 *
 * @package redaxo4
 * @version $Id: index.inc.php,v 1.6 2008/03/20 18:13:13 kills Exp $
 */

require $REX['INCLUDE_PATH'] . '/layout/top.php';

if (isset ($subpage) and $subpage == 'clear_cache')
{
  $c = rex_thumbnail::deleteCache();
  $msg = 'Cache cleared - ' . $c . ' cachefiles removed';
}

// Build Subnavigation
$subpages = array (
  	array ('','Erkl&auml;rung'),
  	array ('settings','Konfiguration'),
  	array ('clear_cache','Resize Cache l&ouml;schen'),
	);

rex_title('Image Resize', $subpages);

// Include Current Page
switch($subpage)
{
  case 'settings' :
  {
    break;
  }

  default:
  {
  	if (isset ($msg) and $msg != '')
		  echo rex_warning($msg);

	  $subpage = 'overview';
  }
}

require $REX['INCLUDE_PATH'] . '/addons/image_resize/pages/'.$subpage.'.inc.php';

require $REX['INCLUDE_PATH'] . '/layout/bottom.php';

?>