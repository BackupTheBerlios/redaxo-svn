<?php

/** 
 * Regelt die Rechte an den einzelnen Kategorien und gibt den Pfad aus
 * Kategorien = Startartikel und Bez�ge  
 * @package redaxo3 
 * @version $Id: function_rex_category.inc.php,v 1.14 2006/03/09 11:31:52 kristinus Exp $ 
 */
  
$KATebene = 0; // aktuelle Ebene: default
$KATPATH = "|"; // Standard f�r path Eintragungen in DB
if (!isset($KATout)) $KATout = ''; // Variable definiert und vorbelegt wenn nicht existent

$KATPERM = false;
if ($REX_USER->isValueOf("rights","csw[0]") || $REX_USER->isValueOf("rights","admin[]")) $KATPERM = true;

$KAT = new sql;
$KAT->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id=$category_id and startpage=1 and clang=$clang");

if ($KAT->getRows()!=1)
{
	// kategorie existiert nicht
	
}else
{
	// kategorie existiert
	
	$KPATH = explode("|",$KAT->getValue("path"));
		
	$KATebene = count($KPATH)-1;
	for ($ii=1;$ii<$KATebene;$ii++)
	{
		
		$SKAT = new sql;
		$SKAT->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id=".$KPATH[$ii]." and startpage=1 and clang=$clang");

		if ($SKAT->getRows()==1)
		{

			if ($KATPERM || $REX_USER->isValueOf("rights","csw[".$SKAT->getValue("id")."]"))
			{

				$KATout .= " : <a href=index.php?page=structure&category_id=".$SKAT->getValue("id")."&clang=$clang>".$SKAT->getValue("catname")."</a>";
				$KATPATH .= $KPATH[$ii]."|";
				$KATPERM = true;

			}else if ($KATPERM || $REX_USER->isValueOf("rights","csr[".$SKAT->getValue("id")."]"))
			{

				$KATout .= " : <a href=index.php?page=structure&category_id=".$SKAT->getValue("id")."&clang=$clang>".$SKAT->getValue("catname")."</a>";
				$KATPATH .= $KPATH[$ii]."|";

			}

		}

	}
	
	if ($KATPERM || $REX_USER->isValueOf("rights","csr[$category_id]") || $REX_USER->isValueOf("rights","csw[$category_id]"))
	{

		$KATout .= " : <a href=index.php?page=structure&category_id=$category_id&clang=$clang>".$KAT->getValue("catname")."</a>";
		$KATPATH .= "$category_id|";
		if ($REX_USER->isValueOf("rights","csw[$category_id]")) $KATPERM = true;

	}else
	{
		$category_id = 0;	
		$article_id = 0;
	}

}

$KATout = "&nbsp;&nbsp;&nbsp;".$I18N->msg("path")." : <a href=index.php?page=structure&category_id=0&clang=$clang>Homepage</a>".$KATout;

?>
