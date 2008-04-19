<?php

$mypage = "simple_user";

//------------------------------> Gruppe Anlegen|Editieren
if($func == "add" || $func == "edit"){
	
	$mita = new rexform;
	$mita->setWidth(770);
	$mita->setLabelWidth(160);
	$mita->setTablename("rex_2_group");
	if($func == "add"){
		$mita->setFormtype("add");
		$mita->setFormheader("<input type=hidden name=page value=".$mypage."><input type=hidden name=subpage value=".$subpage."><input type=hidden name=func value=".$func." />");
		$mita->setShowFormAlways(false);
	}else{			
		$mita->setFormtype("edit", "id='".$oid."'", "Gruppe wurde nicht gefunden");
		$mita->setFormheader("<input type=hidden name=page value=".$mypage."><input type=hidden name=subpage value=".$subpage."><input type=hidden name=func value=".$func." /><input type=hidden name=oid value=".$oid.">");
		$mita->setShowFormAlways(true);				
	}
	$mita->setValue("subline",$I18N_SIMPLE_USER->msg("uw_hl_group") ,"left",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_name"),"name",1);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_extras"),"extras");
	echo $mita->showForm();
	echo "<br><br><a href=index.php?page=".$mypage."&subpage=".$subpage."><b>&laquo; Zurück zur Übersicht</b></a><br>";
}

//------------------------------> User löschen
if($func == "delete"){
	$query = "delete from rex_2_group where id='".$oid."' ";
	$delsql = new sql;
	$delsql->debugsql=0;
	$delsql->setQuery($query);
	$func = "";
}

//------------------------------> Userliste
if($func == ""){

	$sql = "select * from rex_2_group order by id";

	$mit = new rexlist;
	$mit->setQuery($sql);
	$mit->setGlobalLink("index.php?page=".$mypage."&subpage=".$subpage."&next=");
	$mit->setValue("id","id");
	$mit->setValue("Name","name");
	$mit->setLink("index.php?page=".$mypage."&func=edit&oid=","id");
	$mit->addColumn("löschen","index.php?page=".$mypage."&subpage=".$subpage."&func=delete&oid=","id"," onclick=\"return confirm('".$I18N_SIMPLE_USER->msg("uw_sicherdel")."');\"");
	echo $mit->showall($next);
	echo "<br><br><a href=index.php?page=".$mypage."&subpage=".$subpage."&func=add><b>".$I18N_SIMPLE_USER->msg("uw_creategroup")."</b></a><br>";
}

?>