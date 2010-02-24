<?php
$Basedir = dirname(__FILE__);
$id = rex_request('id','int');
$func = rex_request('func','string');

echo '<div class="rex-addon-output-v2">';
if ($func == '')
{	
	$query = 'SELECT * FROM '.$REX['TABLE_PREFIX'].'679_types';
	
	$list = rex_list::factory($query);
	$list->setNoRowsMessage($I18N->msg('imanager_type_no_types'));
  $list->setCaption($I18N->msg('imanager_type_caption'));
  $list->addTableAttribute('summary', $I18N->msg('imanager_type_summary'));
  $list->addTableColumnGroup(array(40, 100, '*', 130, 130));
	
	$list->removeColumn('id');	
	$list->setColumnLabel('name',$I18N->msg('imanager_type_name'));
  $list->setColumnParams('name', array('func' => 'edit', 'id' => '###id###'));
	$list->setColumnLabel('description',$I18N->msg('imanager_type_description'));

	// icon column
  $thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">'. $I18N->msg('imanager_type_create') .'</span></a>';
  $tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
  $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($thIcon, array('func' => 'edit', 'id' => '###id###'));
  
  // functions column spans 2 data-columns
  $funcs = $I18N->msg('imanager_type_effect_functions');
  $list->addColumn($funcs, $I18N->msg('imanager_effects_edit'), -1, array('<th colspan="2">###VALUE###</th>','<td>###VALUE###</td>'));
  $list->setColumnParams($funcs, array('type_id' => '###id###', 'subpage' => 'effects'));
  
  $delete = 'deleteCol';
  $list->addColumn($delete, $I18N->msg('imanager_type_delete'), -1, array('','<td>###VALUE###</td>'));
  $list->setColumnParams($delete, array('type_id' => '###id###', 'func' => 'delete'));
  $list->addLinkAttribute($delete, 'onclick', 'return confirm(\''.$I18N->msg('delete').' ?\')');
  
	$list->show();
	
} 
elseif ($func == 'edit' || $func == 'add')
{
  if($func == 'edit')
  {
    $formLabel = $I18N->msg('imanager_type_edit');
  }
  else if ($func == 'add')
  {
    $formLabel = $I18N->msg('imanager_type_create');
  }
  
	$form = rex_form::factory($REX['TABLE_PREFIX'].'679_types',$formLabel,'id='.$id);

	$field =& $form->addTextField('name');
	$field->setLabel($I18N->msg('imanager_type_name'));

	$field =& $form->addTextareaField('description');
	$field->setLabel($I18N->msg('imanager_type_description'));

	if($func == 'edit')
	{
		$form->addParam('id', $id);
	}
	
	$form->show();
}

echo '</div>';
?>