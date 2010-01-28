<?php

/**
 * Version editme
 *
 * @author jan.kristinus@redaxo.de Jan Kristinus
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

require $REX['INCLUDE_PATH'].'/layout/top.php';

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');

rex_title($I18N->msg("editme"),$REX['ADDON'][$page]['subpages']);

$tables = rex_em_getTables();

switch($subpage)
{
  case 'field':
  	require $REX['INCLUDE_PATH'] . '/addons/'.$page.'/pages/'.$subpage.'.inc.php';
    break;
	case '':
	  if($REX['USER']->isAdmin())
	  {
	    $subpage = 'tables';
      require $REX['INCLUDE_PATH'] . '/addons/'.$page.'/pages/'.$subpage.'.inc.php';
	    break;
	  }else
	  {
	  	if(count($tables)>0)
		    foreach($tables as $t)
		    {
		  	  $subpage = $t["label"];
		  	  break;
		    }
	  }
  default:
  {
  	$table = "";
    if(count($tables)>0)
	  	foreach($tables as $t)
	  		if($t["label"] == $subpage)
	  		  $table = $subpage;
  	
		if($table == "")
		{
			$subpage = "tables";
			require $REX['INCLUDE_PATH'] . '/addons/'.$page.'/pages/'.$subpage.'.inc.php';
	  }else
	  {
			require $REX['INCLUDE_PATH'] . '/addons/'.$page.'/pages/edit.inc.php'; 			
	  }
  }
}

require $REX['INCLUDE_PATH'].'/layout/bottom.php';