<?php

// ********************************************* DATA ADD/EDIT/LIST

$func = rex_request("func","string","");
$data_id = rex_request("data_id","int","");
$rex_em_opener_field = rex_request("rex_em_opener_field","int",-1);
$rex_em_opener_fieldname = rex_request("rex_em_opener_fieldname","string","");
$rex_em_opener_info = rex_request("rex_em_opener_info","string","");
$rex_em_filter = rex_request("rex_em_filter","array");
$rex_em_set = rex_request("rex_em_set","array");

// ********************************** DFAULRT - LISTE AUSGEBEN
$show_list = TRUE;

// ********************************** TABELLE HOLEN
foreach($tables as $table)
{
	$name = $table['name'];
	$id = $table['id'];
	$table["tablename"] = rex_em_getTableName($table['name']);
	if($subpage == $table['name'])
	{
		echo '<table cellpadding="5" class="rex-table"><tr><td><b>'.$table["label"].'</b> - '.$table["description"];
		if($rex_em_opener_info != "")
		{
		  echo ' - Opener-Info: '.$rex_em_opener_info;
		}
		echo '</td></tr></table><br />';
		break; // Wenn Tabelle gefunden - abbrechen
	}
}

// ********************************** FELDER HOLEN
$fields = rex_em_getFields($table['name']);
$field_names = array();
foreach($fields as $field){ if($field["type_id"] == "value") { $field_names[] = $field["f1"]; } }

// ********************************** FILTER UND SETS PR�FEN
$em_url_filter = "";
if(count($rex_em_filter)>0) {
	foreach($rex_em_filter as $k => $v) {
		if(in_array($k,$field_names)) { $em_url_filter .= '&amp;rex_em_filter['.$k.']='.urlencode($v); }
		else { unset($rex_em_filter[$k]); }
	}
};
$em_url_set = "";
if(count($rex_em_set)>0) {
	foreach($rex_em_set as $k => $v) {
		if(in_array($k,$field_names)) { $em_url_set .= '&amp;rex_em_set['.$k.']='.urlencode($v); }
		else { unset($rex_em_set[$k]); }
	}
};
$em_url = $em_url_filter.$em_url_set;




// ---------- Opener Field .. dann wird rahmen weggeCSSt..
if($rex_em_opener_field > -1)
{
	echo '<link rel="stylesheet" type="text/css" href="../files/addons/editme/popup.css" media="screen, projection, print" />';
}







// ********************************************* LOESCHEN
if($func == "delete")
{
	$query = 'delete from '.$table["tablename"].' where id='.$data_id;
	$delsql = new rex_sql;
	// $delsql->debugsql=1;
	$delsql->setQuery($query);
	$func = "";
	echo rex_info("Datensatz wurde gel&ouml;scht");
	$func = "";
}





// ********************************************* FORMULAR
if($func == "add" || $func == "edit")
{

	$xform = new rex_xform;
	// $xform->setDebug(TRUE);
	$xform->setHiddenField("page",$page);
	$xform->setHiddenField("subpage",$subpage);
	$xform->setHiddenField("func",$func);
	$xform->setHiddenField("rex_em_opener_field",$rex_em_opener_field);
	$xform->setHiddenField("rex_em_opener_fieldname",$rex_em_opener_fieldname);

	if(count($rex_em_filter)>0) { foreach($rex_em_filter as $k => $v) { $xform->setHiddenField('rex_em_filter['.$k.']',$v); } };
	if(count($rex_em_set)>0) { foreach($rex_em_set as $k => $v) { $xform->setHiddenField('rex_em_set['.$k.']',$v); } };

	foreach($fields as $field)
	{
		$type_name = $field["type_name"];
		$type_id = $field["type_id"];
		$values = array();
		for($i=1;$i<10;$i++){ $values[] = $field["f".$i]; }
		if($type_id == "value")
		{
			$xform->setValueField($field["type_name"],$values);
		}elseif($type_id == "validate")
		{
			$xform->setValidateField($field["type_name"],$values);
		}elseif($type_id == "action")
		{
			$xform->setActionField($field["type_name"],$values);
		}
	}

	// $xform->setActionField("showtext",array("","Vielen Dank f�r die Eintragung"));
	$xform->setObjectparams("main_table",$table["tablename"]); // f�r db speicherungen und unique abfragen

	if($func == "edit")
	{
		$xform->setHiddenField("data_id",$data_id);
		$xform->setActionField("db",array($table["tablename"],"id=$data_id"));
		$xform->setObjectparams("main_id",$data_id);
		$xform->setObjectparams("main_where","id=$data_id");
		$xform->setGetdata(true); // Datein vorher auslesen
	}elseif($func == "add")
	{
		$xform->setActionField("db",array($table["tablename"]));
	}

	$xform->setObjectparams("rex_em_set",$rex_em_set);

	$form = $xform->getForm();

	if($xform->objparams["form_show"])
	{
		if($func == "edit")
		echo '<div class="rex-area"><h3 class="rex-hl2">Daten editieren</h3><div class="rex-area-content">';
		else
		echo '<div class="rex-area"><h3 class="rex-hl2">Datensatz anlegen</h3><div class="rex-area-content">';
		echo $form;
		echo '</div></div>';
		echo '<br />&nbsp;<br /><table cellpadding="5" class="rex-table"><tr><td><a href="index.php?page='.$page.'&amp;subpage='.$subpage.'&rex_em_opener_field='.$rex_em_opener_field.'&rex_em_opener_fieldname='.htmlspecialchars($rex_em_opener_fieldname).$em_url.'"><b>&laquo; '.$I18N->msg('em_back_to_overview').'</b></a></td></tr></table>';
		$show_list = FALSE;
	}else
	{
		if($func == "edit")
		echo rex_info("Vielen Dank f&uuml;r die Aktualisierung.");
		elseif($func == "add")
		echo rex_info("Vielen Dank f&uuml;r den Eintrag.");
	}

}





// ********************************************* LIST
if($show_list)
{
	echo '<table cellpadding="5" class="rex-table"><tr><td><a href="index.php?page='.$page.'&subpage='.$subpage.'&func=add&rex_em_opener_field='.$rex_em_opener_field.'&rex_em_opener_fieldname='.htmlspecialchars($rex_em_opener_fieldname).$em_url.'"><b>+ anlegen</b></a></td></tr></table><br />';

	// ----- SUCHE
	if($table["search"]==1)
	{

		/*
		 $xform = new rex_xform;
		 // $xform->setDebug(TRUE);
		 $xform->setHiddenField("page",$page);
		 $xform->setHiddenField("subpage",$subpage);
		 $xform->setHiddenField("rex_em_opener_field",$rex_em_opener_field);
		 $xform->setHiddenField("rex_em_opener_fieldname",$rex_em_opener_fieldname);
		 if(count($rex_em_filter)>0) { foreach($rex_em_filter as $k => $v) { $xform->setHiddenField('rex_em_filter['.$k.']',$v); } };
		 if(count($rex_em_set)>0) { foreach($rex_em_set as $k => $v) { $xform->setHiddenField('rex_em_set['.$k.']',$v); } };
		 foreach($fields as $field)
		 {
		 $type_name = $field["type_name"];
		 $type_id = $field["type_id"];
		 $values = array();
		 for($i=1;$i<10;$i++){ $values[] = $field["f".$i]; }
		 if($type_id == "value" && $field["search"])
		 {
		 $xform->setValueField($field["type_name"],$values);
		 }
		 }

		 $form = $xform->getForm();

		 $form = "Suchformular noch einbauen";
		 echo '<div class="rex-area"><h3 class="rex-hl2">TODO: Suche</h3><div class="rex-area-content">';
		 echo $form;
		 echo '</div></div>';
		 */

	}

	// ---------- SQL AUFBAUEN
	$sql = "select * from ".$table["tablename"];
	if(count($rex_em_filter)>0)
	{
		$sql .= ' where ';
		$sql_filter = '';
		foreach($rex_em_filter as $k => $v)
		{
			if($sql_filter != '')
			{
				$sql_filter .= ' AND ';
			}
			$sql_filter .= '`'.$k.'`="'.$v.'"';
		}
		$sql .= $sql_filter;
		// echo $sql;
	}

	// ---------- LISTE AUSGEBEN
	$list = rex_list::factory($sql,$table["list_amount"]);
	$list->setColumnFormat('id', 'Id');

	if(count($rex_em_filter)>0) { foreach($rex_em_filter as $k => $v) { $list->addParam('rex_em_filter['.$k.']',$v); } }
	if(count($rex_em_set)>0) { foreach($rex_em_set as $k => $v) { $list->addParam('rex_em_set['.$k.']',$v); } }
  if($rex_em_opener_field >-1) { $list->addParam("rex_em_opener_field",$rex_em_opener_field); };
  if($rex_em_opener_fieldname != "") { $list->addParam("rex_em_opener_fieldname",$rex_em_opener_fieldname); };
  if($rex_em_opener_info != "") { $list->addParam("rex_em_opener_info",$rex_em_opener_info); };
  
	$list->setColumnParams("id", array("data_id"=>"###id###", "func"=>"edit" ));

	$fields = rex_em_getFields($table['name']);
	foreach($fields as $field)
	{
		if($field["type_id"] == "value")
		{
			if($field["list_hidden"] == 1)
			{
				$list->removeColumn($field["f1"]);
			}else
			{
        $list->setColumnSortable($field["f1"]);
			}
		}
	}

	$list->addColumn('editieren','editieren');
	$list->setColumnParams("editieren", array("data_id"=>"###id###","func"=>"edit"));

	$list->addColumn('l&ouml;schen','l&ouml;schen');
	$list->setColumnParams("l&ouml;schen", array("data_id"=>"###id###","func"=>"delete"));

	// if($rex_em_opener_field){ $list->addColumn('&uuml;bernehmen','<a href="javascript:em_setData('.$rex_em_opener_field.',###id###,\'###'.$rex_em_opener_fieldname.'###\')">&uuml;bernehmen</a>',-1,"asdasd"); }

	echo $list->get();

}
