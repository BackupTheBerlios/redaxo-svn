<?php

/**
 * XO-Form 
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

// email templates tabelle loeschen

$sql = new rex_sql();
$sql->setQuery("DROP TABLE `rex_xform_email_template`;");

$REX['ADDON']['install']['xform'] = 0;

?>