<?php

/** 
 * Funktionen zum handeln von magic_quotes=off  
 * @package redaxo3 
 * @version $Id: function_rex_mquotes.inc.php,v 1.5 2005/11/13 15:05:14 kills Exp $ 
 */
  
function addSlashesOnArray(&$theArray)
{
	if (is_array($theArray))
	{
		reset($theArray);
		while(list($Akey,$AVal)=each($theArray))
		{
			if (is_array($AVal))
			{
				addSlashesOnArray($theArray[$Akey]);
			}else
			{
				$theArray[$Akey] = addslashes($AVal);
			}
		}
		reset($theArray);
	}
}

if (is_array($_GET))
{
    addSlashesOnArray($_GET);
    
	while(list($Akey,$AVal)=each($_GET))
	{
		$$Akey = $AVal;
	}
}

if (is_array($_POST))
{
    addSlashesOnArray($_POST);
    
	while(list($Akey,$AVal)=each($_POST))
	{
		$$Akey = $AVal;
	}
}

?>