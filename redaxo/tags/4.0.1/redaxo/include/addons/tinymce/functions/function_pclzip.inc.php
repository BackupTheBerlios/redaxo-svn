<?php

/**
 * TinyMCE Addon
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 *
 * @package redaxo4
 * @version $Id: function_pclzip.inc.php,v 1.8 2007/10/17 08:30:12 kills Exp $
 */

include_once $REX['INCLUDE_PATH'] . '/addons/tinymce/classes/class.pclzip.inc.php';

function rex_a52_extract_archive($file, $msg = '', $path=null )
{
  global $REX;
  if(!$path) $path = '../files/'. $REX['TEMP_PREFIX'];

	$archive = new PclZip($file);
	if ($archive->extract(PCLZIP_OPT_PATH, $path) == 0)
	{
	  die("Error : " . $archive->errorInfo(true));
	}

	if (($list = $archive->listContent()) == 0)
	{
	  die("Error : " . $archive->errorInfo(true));
	}
}

?>