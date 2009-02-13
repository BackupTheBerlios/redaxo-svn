<?php

/**
 * Layout Kopf des Backends
 * @package redaxo4
 * @version $Id: top.php,v 1.7 2008/04/02 19:58:00 kills Exp $
 */

if (!isset ($page_name))
  $page_name = '';

$page_title = $REX['SERVERNAME'];

if ($page_name != '')
  $page_title .= ' - ' . $page_name;

$body_id = str_replace('_', '-', $page);
$bodyAttr = 'id="rex-page-'. $body_id .'"';

if ($REX["PAGE_NO_NAVI"]) $bodyAttr .= ' onunload="closeAll();"';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $I18N->msg('htmllang'); ?>" lang="<?php echo $I18N->msg('htmllang'); ?>">
<head>
  <title><?php echo htmlspecialchars($page_title) ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $I18N->msg('htmlcharset'); ?>" />
  <meta http-equiv="Content-Language" content="<?php echo $I18N->msg('htmllang'); ?>" />
  <meta http-equiv="Cache-Control" content="no-cache" />
  <meta http-equiv="Pragma" content="no-cache" />
  <link rel="stylesheet" type="text/css" href="media/css_import.css" media="screen, projection, print" />
  <!-- jQuery immer nach den Stylesheets! -->
  <script src="media/jquery.min.js" type="text/javascript"></script>
  <script src="media/standard.js" type="text/javascript"></script>
  <script type="text/javascript">
  <!--
  var redaxo = true;

  // jQuery is now removed from the $ namespace
  // to use the $ shorthand, use (function($){ ... })(jQuery);
  // and for the onload handler: jQuery(function($){ ... });
  jQuery.noConflict();
  //-->
  </script>
<?php
  // ----- EXTENSION POINT
  echo rex_register_extension_point('PAGE_HEADER', '');
?>
</head>
<body <?php echo $bodyAttr; ?>>
<div id="rex-website">
<div id="rex-header">

  <p class="rex-header-top"><a href="../index.php" onclick="window.open(this.href);"><?php echo htmlspecialchars($REX['SERVERNAME']); ?></a></p>

  <div id="rex-navi-login"><?php
  
if (isset ($LOGIN) && $LOGIN && !$REX["PAGE_NO_NAVI"])
{
  $accesskey = 1;
  $user_name = $REX_USER->getValue('name') != '' ? $REX_USER->getValue('name') : $REX_USER->getValue('login');
  echo '<p class="rex-logout">' . $I18N->msg('name') . ' : <strong><a href="index.php?page=profile">' . htmlspecialchars($user_name) . '</a></strong> [<a href="index.php?rex_logout=1"'. rex_accesskey($I18N->msg('logout'), $REX['ACKEY']['LOGOUT']) .'>' . $I18N->msg('logout') . '</a>]</p>' . "\n";
}else if(!$REX["PAGE_NO_NAVI"])
{
  echo '<p class="rex-logout">' . $I18N->msg('logged_out') . '</p>';
}else
{
  echo '<p class="rex-logout">&nbsp;</p>';
}
  
?></div>

  <div id="rex-navi-main">
<?php

if (isset ($LOGIN) && $LOGIN && !$REX["PAGE_NO_NAVI"])
{
  
  $navi_system = array();
  $navi_addons = array();
  foreach($REX_USER->pages as $pageKey => $pageArr)
  {
    $pageKey = strtolower($pageKey);
  	if(!in_array($pageKey, array("credits","profile","content","linkmap")))
  	{
  	  $item = array();
  	  
      $item['id'] = 'rex-navi-page-'.$pageKey;
      $item['class'] = '';
	  	if($pageKey == $REX["PAGE"]) 
        $item['class'] = 'rex-active';

			if($pageArr[1] != 1)
			{
				// ***** Basis
				if($pageKey == "mediapool")
				{
          $item['href'] = '#';
          $item['onclick'] = 'openMediaPool();';
				}
				else
				{ 
          $item['href'] = 'index.php?page='.$pageKey;
				}
				
        $item['extra'] = rex_accesskey($pageArr[0], $accesskey++);
        $item['tabindex'] = rex_tabindex(false);
  	  	$navi_system[$pageArr[0]] = $item;
			}
			else
			{
				// ***** AddOn
	  		if(isset ($REX['ADDON']['link'][$pageKey]) && $REX['ADDON']['link'][$pageKey] != "") 
          $item['href'] = $REX['ADDON']['link'][$pageKey];
				else 
          $item['href'] = 'index.php?page='.$pageKey;
          
	      if(isset ($REX['ACKEY']['ADDON'][$pageKey]))
          $item['extra'] = rex_accesskey($name, $REX['ACKEY']['ADDON'][$pageKey]);
	      else 
          $item['extra'] = rex_accesskey($pageArr[0], $accesskey++);
	      
        $item['tabindex'] = rex_tabindex(false);
  	  	$navi_addons[$pageArr[0]] = $item;
			}
  	}  	
  }
  
  
  foreach(array('system' => $navi_system, 'addon' => $navi_addons) as $topic => $naviList)
  {
    $headline = $topic == 'system' ? $I18N->msg('navigation_basis') : $I18N->msg('navigation_addons');
    echo '<h1>'. $headline .'</h1>';
    echo '<ul id="rex-navi-'. $topic .'">';
    
    $first = true;
    foreach($naviList as $pageTitle => $item)
    {
      if($first)
        $item['class'] .= ' rex-navi-first';
        
      $class = $item['class'] != '' ? ' class="'. $item['class'] .'"' : '';
      unset($item['class']);
      $extra = $item['extra'];
      unset($item['extra']);
      $id = $item['id'];
      unset($item['id']);
      
      $tags = '';
      foreach($item as $tag => $value)
        $tags .= ' '. $tag .'="'. $value .'"';
      
      echo '<li'. $class .' id="'. $id .'"><a'. $tags . $extra .'>'. $pageTitle .'</a></li>';
      $first = false;
    }
    echo '</ul>' . "\n";
  }

}

?>
  </div>

</div>

<div id="rex-wrapper">