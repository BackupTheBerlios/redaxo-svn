<?php

/**
 * TinyMCE Addon
 *  
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 * 
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 * 
 * @package redaxo4
 * @version $Id: uninstall.inc.php,v 1.1 2008/03/26 13:34:13 kills Exp $
 */

rex_deleteDir('../files/tmp_/tinymce', true);

$REX['ADDON']['install']['tinymce'] = 0;
?>