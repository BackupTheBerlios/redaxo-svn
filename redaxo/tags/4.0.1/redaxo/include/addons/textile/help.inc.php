<?php

/**
 * Textile Addon
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo4
 * @version $Id: help.inc.php,v 1.7 2007/10/17 08:58:36 kills Exp $
 */

?>
<p>
Bringt die Möglichkeit in Modulen Textile Markup zu verwenden

<br /><br />

<?php
  $file = dirname( __FILE__) .'/_changelog.txt';
  if(is_readable($file))
    echo str_replace( '+', '&nbsp;&nbsp;+', nl2br(file_get_contents($file)));
?>
</p>