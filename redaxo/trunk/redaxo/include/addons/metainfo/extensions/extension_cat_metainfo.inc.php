<?php

/**
 * MetaForm Addon
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 * @package redaxo4
 * @version $Id: extension_cat_metainfo.inc.php,v 1.3 2008/03/11 16:03:32 kills Exp $
 */

rex_register_extension('CAT_FORM_ADD', 'rex_a62_metainfo_form');
rex_register_extension('CAT_FORM_EDIT', 'rex_a62_metainfo_form');

rex_register_extension('CAT_ADDED', 'rex_a62_metainfo_form');
rex_register_extension('CAT_UPDATED', 'rex_a62_metainfo_form');

rex_register_extension('CAT_FORM_BUTTONS', 'rex_a62_metainfo_button');

function rex_a62_metainfo_button($params)
{
	global $REX, $I18N_META_INFOS;

	$fields = new rex_sql();
  $fields->setQuery('SELECT * FROM '. $REX['TABLE_PREFIX'] .'62_params p,'. $REX['TABLE_PREFIX'] .'62_type t WHERE `p`.`type` = `t`.`id` AND `p`.`name` LIKE "cat_%" LIMIT 1');

  if ($fields->getRows()==1)
  {
  	$return = '<p class="rex-button-add"><script type="text/javascript"><!--

  function rex_metainfo_toggle()
  {
  	var trs = getElementsByClass("rex-metainfo-cat");
  	for(i=0;i<trs.length;i++)
    {
  		show = toggleElement(trs[i]);
  	}
    if (show == "") changeImage("rex-metainfo-icon","media/file_del.gif")
    else changeImage("rex-meta-icon","media/file_add.gif");
  }

  //--></script><a class="rex-button-add" href="javascript:rex_metainfo_toggle();"><img src="media/file_add.gif" id="rex-metainfo-icon" alt="'. $I18N_META_INFOS->msg('edit_metadata') .'" title="'. $I18N_META_INFOS->msg('edit_metadata') .'" /></a></p>';

	   return $params['subject'] . $return;
  }

  return $params['subject'];
}

/**
 * Callback, dass ein Formular item formatiert
 */
function rex_a62_metainfo_form_item($field, $tag, $tag_attr, $id, $label, $labelIt)
{
  global $REX_USER;

  $add_td = '';
  if ($REX_USER->hasPerm('advancedMode[]'))
    $add_td = '<td>&nbsp;</td>';
  
  $element = $field;
  if ($labelIt)
  {
    $element = '
  	   <'.$tag.$tag_attr.'>
  	     <label for="'. $id .'">'. $label .'</label>
  	     '.$field.'
  	   </'.$tag.'>';
  }
  
  $s = '
  <tr class="rex-table-row-activ rex-metainfo-cat" style="display:none;">
  	<td>&nbsp;</td>
  	'.$add_td.'
  	<td colspan="5">
  	 <div class="rex-form-row">
  	   '.$element.'
  	 </div>
    </td>
	</tr>';

  return $s;
}

/**
 * Erweitert das Meta-Formular um die neuen Meta-Felder
 */
function rex_a62_metainfo_form($params)
{
  if(isset($params['category']))
  {
    $params['activeItem'] = $params['category'];

    // Hier die category_id setzen, damit beim klick auf den REX_LINK_BUTTON der Medienpool in der aktuellen Kategorie startet
    $params['activeItem']->setValue('category_id', $params['id']);
  }

  $result = _rex_a62_metainfo_form('cat_', $params, '_rex_a62_metainfo_cat_handleSave');

  // Bei CAT_ADDED und CAT_UPDATED nur speichern und kein Formular zur�ckgeben
  if($params['extension_point'] == 'CAT_UPDATED' || $params['extension_point'] == 'CAT_ADDED')
    return $params['subject'];
  else
    return $params['subject'] . $result;
}

?>