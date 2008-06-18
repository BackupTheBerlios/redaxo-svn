<?php

/** 
 * Zeit Funktionen  
 * @package redaxo4 
 * @version $Id: function_rex_time.inc.php,v 1.3 2007/10/13 13:52:01 kills Exp $ 
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