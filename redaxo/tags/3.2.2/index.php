<?php

/** 
 * 
 * @package redaxo3 
 * @version $Id: index.php,v 1.30 2006/03/22 15:30:41 kristinus Exp $ 
 */ 

include "./redaxo/include/functions/function_rex_mquotes.inc.php";

// ----- ob caching start f�r output filter
ob_start();

// --------------------------- ini settings

// Setzten des arg_separators, falls Sessions verwendet werden,
// um XHTML valide Links zu produzieren
@ini_set( 'arg_separator.input', '&amp;');
@ini_set( 'arg_separator.output', '&amp;');

// --------------------------- globals

unset($REX);

// Flag ob Inhalte mit Redaxo aufgerufen oder
// von der Webseite aus
// Kann wichtig f�r die Darstellung sein
// Sollte immer false bleiben

$REX['REDAXO'] = false;


// Wenn $REX[GG] = true; dann wird der
// Content aus den redaxo/include/generated/
// genommen

$REX['GG'] = true;


// setzte pfad und includiere klassen und funktionen

$REX['HTDOCS_PATH'] = "./";
include "./redaxo/include/master.inc.php";


// Starte einen neuen Artikel und setzte die aktuelle
// artikel id. wenn nicht vorhanden, nimm einen
// speziellen artikel. z.b. fehler seite oder home seite

if (!isset($article_id) or $article_id == '') $article_id = $REX['START_ARTICLE_ID'];

$REX_ARTICLE = new article;
$REX_ARTICLE->setCLang($clang);
if ($REX_ARTICLE->setArticleId($article_id))
{
  echo $REX_ARTICLE->getArticleTemplate();
}elseif($REX_ARTICLE->setArticleId($REX['NOTFOUND_ARTICLE_ID']))
{
  echo $REX_ARTICLE->getArticleTemplate();
}else
{
  echo 'Kein Startartikel selektiert / No starting Article selected. Please click here to enter <a href="redaxo/index.php">redaxo</a>';
  $REX['STATS'] = 0;
}

// ----- caching end f�r output filter
$CONTENT = ob_get_contents();
ob_end_clean();

// ----- EXTENSION POINT
$CONTENT = rex_register_extension_point( 'OUTPUT_FILTER', $CONTENT);

// ----- EXTENSION POINT - keine Manipulation der Ausgaben ab hier (read only)
rex_register_extension_point( 'OUTPUT_FILTER_CACHE', $CONTENT, '', true);

// ----- inhalt endgueltig ausgeben
echo $CONTENT;

?>