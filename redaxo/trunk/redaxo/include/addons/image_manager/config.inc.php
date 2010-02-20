<?php

/**
 * image_manager Addon
 *
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$mypage = 'image_manager';

/* Addon Parameter */
$REX['ADDON']['rxid'][$mypage] = '';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'image_manager';
$REX['ADDON']['perm'][$mypage] = 'image_manager[]';
$REX['ADDON']['version'][$mypage] = '1.0';
$REX['ADDON']['author'][$mypage] = 'Markus Staab, Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['PERM'][] = 'image_manager[]';


// --- DYN
$REX['ADDON']['image_manager']['max_cachefiles'] = 5;
$REX['ADDON']['image_manager']['max_resizekb'] = 1000;
$REX['ADDON']['image_manager']['max_resizepixel'] = 1500;
$REX['ADDON']['image_manager']['jpg_quality'] = 85;
// --- /DYN

require_once (dirname(__FILE__). '/classes/class.rex_image.inc.php');
require_once (dirname(__FILE__). '/classes/class.rex_image_cacher.inc.php');
require_once (dirname(__FILE__). '/classes/class.rex_image_manager.inc.php');
require_once (dirname(__FILE__). '/classes/class.rex_effect_abstract.inc.php');

//--- handle image request
$rex_img_file = rex_get('rex_img_file', 'string');
$rex_img_type = rex_get('rex_img_type', 'string');

if($rex_img_file != '' && $rex_img_type != '')
{
  $imagepath = $REX['HTDOCS_PATH'].'files/'.$rex_img_file;
  $cachepath = $REX['INCLUDE_PATH'].'/generated/files/';
  
  $image         = new rex_image($imagepath);
  $image_cacher  = new rex_image_cacher($cachepath);
	$image_manager = new rex_image_manager($image_cacher);
	
	$image = $image_manager->applyEffects($image, $rex_img_type);
	$image_manager->sendImage($image, $rex_img_type);
	exit();
}


if($REX['REDAXO'])
{
  // delete thumbnails on mediapool changes
  if(!function_exists('rex_image_manager_ep_mediaupdated'))
  {
    rex_register_extension('MEDIA_UPDATED', 'rex_image_manager_ep_mediaupdated');
    function rex_image_manager_ep_mediaupdated($params){
      rex_managed_image::deleteCache($params["filename"]);
    }
  }
  
  // handle backend pages
  $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');
	$REX['ADDON']['navigation'][$mypage]['subpages'] = array (
  	array ('href' => 'index.php?page=image_manager', 'active_when' => array("page"=>"image_manager","subpage"=>""), 'title' => $I18N->msg('iresize_subpage_desc')),
  	array ('href' => 'index.php?page=image_manager&subpage=settings', 'active_when' => array("page"=>"image_manager","subpage"=>"settings"), 'title' => $I18N->msg('iresize_subpage_config')),
		array ('href' => 'index.php?page=image_manager&subpage=types', 'active_when' => array("page"=>"image_manager","subpage"=>"types"), 'title' => $I18N->msg('iresize_subpage_types')),		
  	array ('href' => 'index.php?page=image_manager&subpage=clear_cache', 'active_when' => array("page"=>"image_manager","subpage"=>"clear_cache"), 'title' => $I18N->msg('iresize_subpage_clear_cache')),
	);
}