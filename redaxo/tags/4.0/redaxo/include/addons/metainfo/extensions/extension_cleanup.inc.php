<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo4
 * @version $Id: extension_cleanup.inc.php,v 1.8 2007/10/13 23:27:01 kills Exp $
 */

rex_register_extension('A1_BEFORE_DB_IMPORT', 'rex_a62_metainfo_cleanup');

/**
 * Alle Metafelder l�schen, nicht das nach einem Import in der Parameter Tabelle
 * noch Datens�tze zu Feldern stehen, welche nicht als Spalten in der
 * rex_article angelegt wurden!
 */
function rex_a62_metainfo_cleanup($params)
{
	global $REX;

	// Cleanup nur durchf�hren, wenn auch die rex_article Tabelle neu angelegt wird
	if(isset($params['force']) && $params['force'] != true &&
     strpos($params['content'], 'CREATE TABLE `'. $REX['TABLE_PREFIX'] .'article`') === false &&
	   strpos($params['content'], 'CREATE TABLE '. $REX['TABLE_PREFIX'] .'article') === false)
  {
    return;
  }

  require_once $REX['INCLUDE_PATH'].'/addons/metainfo/classes/class.rex_tableExpander.inc.php';

  $sql = new rex_sql();
  $sql->setQuery('SELECT name FROM ' . $REX['TABLE_PREFIX'] . '62_params');

  for ($i = 0; $i < $sql->getRows(); $i++)
  {
    if (substr($sql->getValue('name'), 0, 4) == 'med_')
      $tableManager = new rex_a62_tableManager($REX['TABLE_PREFIX'] . 'file');
    else
      $tableManager = new rex_a62_tableManager($REX['TABLE_PREFIX'] . 'article');

    $tableManager->deleteColumn($sql->getValue('name'));

    $sql->next();
  }


  // evtl reste aufr�umen
  $tablePrefixes = array('article' => array('art_', 'cat_'), 'file' => array('med_'));
  foreach($tablePrefixes as $table => $prefixes)
  {
    $table = $REX['TABLE_PREFIX'] .$table;
    $tableManager = new rex_a62_tableManager($table);

    foreach(rex_sql::showColumns($table) as $column)
    {
      $column = $column['name'];
      if(in_array(substr($column, 0, 4), $prefixes))
      {
        $tableManager->deleteColumn($column);
      }
    }
  }

  $sql = new rex_sql();
  $sql->setQuery('DELETE FROM '. $REX['TABLE_PREFIX'] .'62_params');

  rex_generateAll();
}

?>