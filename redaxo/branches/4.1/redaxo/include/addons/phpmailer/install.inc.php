<?php

/**
 * PHPMailer Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 *
 * @package redaxo4
 * @version $Id: install.inc.php,v 1.1 2008/03/26 13:34:13 kills Exp $
 */

$error = '';

$settings_file = $REX['INCLUDE_PATH'] .'/addons/phpmailer/classes/class.rex_mailer.inc.php';

if(($state = rex_is_writable($settings_file)) !== true)
  $error = $state;

if ($error != '')
  $REX['ADDON']['installmsg']['phpmailer'] = $error;
else
  $REX['ADDON']['install']['phpmailer'] = true;

?>