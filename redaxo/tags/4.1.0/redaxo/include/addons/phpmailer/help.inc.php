<?php

/**
 * PHPMailer Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 *
 * @package redaxo4
 * @version $Id: help.inc.php,v 1.3 2008/03/11 16:03:48 kills Exp $
 */

?>
<p>
PHPMailer Addon

<br /><br />

<?php
  $file = dirname( __FILE__) .'/_changelog.txt';
  if(is_readable($file))
    echo str_replace( '+', '&nbsp;&nbsp;+', nl2br(file_get_contents($file)));
?>
</p>