<?php

/** 
 * Zeit Funktionen  
 * @package redaxo3 
 * @version $Id: function_rex_time.inc.php,v 1.2 2005/11/13 15:06:01 kills Exp $ 
 */ 

function showScripttime()
{
	global $scriptTimeStart;
	$scriptTimeEnd = getCurrentTime();
	$scriptTimeDiv = intval(($scriptTimeEnd - $scriptTimeStart)*1000)/1000;
	return $scriptTimeDiv;
}

function getCurrentTime()
{ 
	$time = explode(" ",microtime()); 
	return ($time[0]+$time[1]);
} 

function startScripttime()
{
	global $scriptTimeStart;
	$scriptTimeStart = getCurrentTime();
}

startScripttime();

?>