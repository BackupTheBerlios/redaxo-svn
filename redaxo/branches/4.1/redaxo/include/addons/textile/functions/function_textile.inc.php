<?php
/**
 * Textile Addon
 *  
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 * @package redaxo4
 * @version $Id: function_textile.inc.php,v 1.1 2008/03/26 13:34:13 kills Exp $
 */
 
function rex_a79_textile($code)
{
  $textile = rex_a79_textile_instance();
  return $textile->TextileThis($code);
}
 
function rex_a79_textile_instance()
{
  static $instance = null;
  
  if($instance === null)
  {
    $instance = new Textile();
  }
  
  return $instance;
} 
?>