<?php

/**
 * Sprachobjekt zur Internationalisierung (I18N)
 * @package redaxo4
 * @version $Id: class.i18n.inc.php,v 1.4 2008/03/24 14:22:23 kills Exp $
 */

class i18n
{
  var $locales;
  var $searchpath;
  var $locale;
  var $text;
	var $text_loaded;

  /*
   * Constructor
   * the locale must of the common form, eg. de_DE, en_US or just plain en, de.
   * the searchpath is where the language files are located
   */
  function i18n($locale = "de_de", $searchpath)
  {
    $this->searchpath = $searchpath;
    $this->text = array ();
    $this->locale = $locale;
    $this->locales = array ();
    $this->text_loaded = FALSE;
  }

  /*
   * load texts from file.
   * The filename must be of the form:
   *
   * <locale>.lang
   * eg: de_de.lang or en_us.lang or en_gb.lang
   *
   * The file must be in the common property format:
   *
   * key = value
   * # comments must be on one line
   *
   * values may contain placeholders for replacement of variables, e.g.
   * file_not_found = The file {0} could not be found.
   * there can be only 10 placeholders, {0} to {9}.
   */
  function loadTexts()
  {
		$this->text_loaded = TRUE;
    $filename = $this->searchpath . "/" . $this->locale . ".lang";
    if (is_readable($filename))
    {
      $f = fopen($filename, "r");
      while (!feof($f))
      {
        $buffer = fgets($f, 4096);
        if (preg_match("/^(\w*)\s*=\s*(.*)$/", $buffer, $matches))
        {
          $this->addMsg($matches[1], trim($matches[2]));
        }
      }
      fclose($f);
    }
  }

  /*
   * return a message according to a key from the current locale
   * you can give parameters for substitution.
   */
  function msg($key)
  {
  	global $REX;
  	
  	if(isset($REX['LOGIN']) && is_object($REX['LOGIN']) && 
  	   $REX['LOGIN']->getLanguage() != $this->locale)
  	{
  		$this->locale = $REX['LOGIN']->getLanguage();
  		$this->text_loaded = FALSE;
  	}
  	
  	if(!$this->text_loaded)
  	{
  	  $this->loadTexts();
  	}
  	
    if ($this->hasMsg($key))
    {
      $msg = $this->text[$key];
    }
    else
    {
      $msg = "[translate:$key]";
    }

    $patterns = array ();
    $replacements = array ();

    $args = func_get_args();
    for($i = 1; $i < func_num_args(); $i++)
    {
      // zero indexed
      $patterns[] = '/\{'. ($i-1) .'\}/';
      $replacements[] = $args[$i];
    }

    return preg_replace($patterns, $replacements, $msg);
  }

  function addMsg($key, $msg)
  {
    $this->text[$key] = $msg;
  }

  function hasMsg($key)
  {
  	return isset ($this->text[$key]);
  }

  /*
   * find all defined locales in a searchpath
   * the language files must be of the form: <locale>.lang
   * e.g. de_de.lang or en_gb.lang
   */
  function getLocales($searchpath)
  {
    if (empty ($this->locales) && is_readable($searchpath))
    {
      $this->locales = array ();

      $handle = opendir($searchpath);
      while ($file = readdir($handle))
      {
        if ($file != "." && $file != "..")
        {
          if (preg_match("/^(\w+)\.lang$/", $file, $matches))
          {
            $this->locales[] = $matches[1];
          }
        }
      }
      closedir($handle);

    }

    return $this->locales;
  }

}

// Funktion zum Anlegen eines Sprache-Objekts
function rex_create_lang($locale = "de_de", $searchpath = '', $setlocale = TRUE)
{
  global $REX;

  $_searchpath = $searchpath;

  if ($searchpath == '')
  {
    $searchpath = $REX['INCLUDE_PATH'] . "/lang";
  }

  $lang_object = new i18n($locale, $searchpath);

  if ($_searchpath == '')
  {
    $REX['LOCALES'] = $lang_object->getLocales($searchpath);
  }

  $locales = array();
  foreach(explode(',', trim($lang_object->msg('setlocale'))) as $locale)
  {
    $locales[]= $locale .'.'. strtoupper(str_replace('iso-', 'iso', $lang_object->msg('htmlcharset')));
    $locales[]= $locale;
  }
  
  if($setlocale) setlocale(LC_ALL, $locales);

  return $lang_object;
}