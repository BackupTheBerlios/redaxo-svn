<?

// ------------------------------------------------------------

if (!$REX[GG]) $REX[GG] = false;

// ------------------------------------------------------------ globals

// ----------------- SERVER VARS

$REX[SERVER] = "www.redaxo.de";
$REX[SERVERNAME] = "REX NAME";
$REX[error_emailaddress] = "info@meinserver.de";
$REX[version] = "2.7";
$REX[STARTARTIKEL_ID] = 1;
$REX[STATS] = 1;

// select default language
$REX[LANG] = en_GB;

// activate mod_rewrite support
$REX[MOD_REWRITE] = false;

// Windows:
// Pfade absolut eintragen
// Linux:
// nicht �ndern

$REX[WWW_PATH] = ""; //
$REX[DOC_ROOT] = "";
$REX[INCLUDE_PATH] = $REX[DOC_ROOT].$REX[HTDOCS_PATH]."redaxo/include";
$REX[MEDIAFOLDER] = $REX[HTDOCS_PATH]."files";

//

$REX[BARRIEREFREI] = false;
$REX[COMMUNITY] = false;

// ----------------- DATENBANK

$DB[1][HOST] = "localhost";
$DB[1][LOGIN] = "root";
$DB[1][PSW] = "";
$DB[1][NAME] = "datenbankname";

$DB[2][HOST] = "";
$DB[2][LOGIN] = "";
$DB[2][PSW] = "";
$DB[2][NAME] = "";

// ----------------- IMAGEMAGICKPATHS
$REX[IMAGEMAGICK] = false;
$REX[IMAGEMAGICK_PATH] = "/usr/bin/convert";


// ----------------- COMMUNITY


// ------------------------------------------------------------ wenn magic quotes off

if (!get_magic_quotes_gpc()) include_once $REX[INCLUDE_PATH]."/functions/function_rex_mquotes.inc.php";

// ------------------------------------------------------------ includes

// hier wird die i18n sprachklasse erzeugt
include_once $REX[INCLUDE_PATH]."/classes/class.i18n.inc.php";
$I18N = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/lang/");
$REX[LOCALES] = i18n::getLocales($REX[INCLUDE_PATH]."/lang/");

include $REX[INCLUDE_PATH]."/functions/function_showmicrotime.inc.php";

// klassen
// include $REX[INCLUDE_PATH]."/classes/class.ftp.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.sql.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.select.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.article.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.login.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.form.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.list.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.board.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.mime_mail.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.mail_decode.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.tar.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.stat.inc.php";

// OO Classes
include_once $REX[INCLUDE_PATH]."/classes/class.oocategory.inc.php";

// Textile class
include_once $REX[INCLUDE_PATH]."/classes/class.textile.inc.php";

// functions
include_once $REX[INCLUDE_PATH]."/functions/function_datefrommydate.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_selectdate.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_mail.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_createimage.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_rex_mediapool.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_rex_modrewrite.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.textile.inc.php";

// ------------------------------------------------------------ redaxo includes

include_once $REX[INCLUDE_PATH]."/functions/function_rex_title.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_rex_generate.inc.php";

if (!isset($category_id) or $category_id == "") $category_id = 0;


?>