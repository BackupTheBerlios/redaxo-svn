<?php

/**
 * Backend Search Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id: install.inc.php,v 1.2 2008/02/24 16:17:31 kills Exp $
 */

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['be_search'] = $error;
else
  $REX['ADDON']['install']['be_search'] = true;
?>