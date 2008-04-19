<?php

$mypage = "simple_user";

//------------------------------> User Anlegen|Editieren
if($func == "add" || $func == "edit"){
	
	$mita = new rexform;
	
	$mita->setWidth(770);
	$mita->setLabelWidth(160);
	$mita->setTablename("rex_2_user");
	
	if($func == "add"){
		$mita->setFormtype("add");
		$mita->setFormheader("<input type=hidden name=page value=".$mypage."><input type=hidden name=func value=".$func." />");
		$mita->setShowFormAlways(false);
	}else{			
		$mita->setFormtype("edit", "id='".$oid."'", "User wurde nicht gefunden");
		$mita->setFormheader("<input type=hidden name=page value=".$mypage."><input type=hidden name=func value=".$func." /><input type=hidden name=oid value=".$oid.">");
		$mita->setShowFormAlways(true);				
	}
	
	$mita->setValue("subline",$I18N_SIMPLE_USER->msg("uw_info_headline") ,"left",0);


	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_login"),"user_login",1);
	$mita->setCols(2);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_passwort"),"user_password",1);
	$mita->setValue("singleselect",$I18N_SIMPLE_USER->msg("uw_user_status"),"user_status",0, "1|".$I18N_SIMPLE_USER->msg("uw_user_status_on")."|0|".$I18N_SIMPLE_USER->msg("uw_user_status_off")."");
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_usre_typ"),"user_typ",0);		
	$mita->setValue("checkbox",$I18N_SIMPLE_USER->msg("uw_info_newsletter"),"info_newsletter",0);
	$mita->setValue("checkbox",$I18N_SIMPLE_USER->msg("uw_info_mail"),"info_mail",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_file1"),"user_file1",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_file2"),"user_file2",0);
	$mita->setValue("singleselect",$I18N_SIMPLE_USER->msg("uw_login_activation"),"login_activation",0, "0|".$I18N_SIMPLE_USER->msg("uw_nein")."|1|".$I18N_SIMPLE_USER->msg("uw_ja")."");
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_activation_key"),"activation_key",0);

	if($func == "edit"){
		$mita->setValue("multipleselectsql","Gruppen","",0,
				"select * from rex_2_group order by name","id","name",
				5,"rex_2_u_g","user_id='$oid'","group_id");
		$mita->setValue("empty","","",0);
	}

	$mita->setValue("subline",$I18N_SIMPLE_USER->msg("uw_persdata_headline"),"left",0);
	$mita->setCols(1);		
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_name"),"user_name",0);
	$mita->setCols(2);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_vorname"),"user_firstname",0);
	
	$mita->setValue("singleselect",$I18N_SIMPLE_USER->msg("uw_geschlecht"),"user_gender",0, "w|".$I18N_SIMPLE_USER->msg("uw_geschlecht_w")."|m|".$I18N_SIMPLE_USER->msg("uw_geschlecht_m")."");
	
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_birthday"),"user_birthdate",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_augen"),"user_eyecolor",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_haare"),"user_haircolor",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_strassse"),"user_street",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_plz"),"user_plz",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_ort"),"user_town",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_telefon"),"user_phone",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_mobil"),"user_mobile",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_email"),"user_email",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_icq"),"user_icq",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_aim"),"user_aim",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_msn"),"user_msn",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_skype"),"user_skype",0);
	$mita->setValue("singleselect",$I18N_SIMPLE_USER->msg("uw_persdata_headline"),"user_private_data_public",0, "1|".$I18N_SIMPLE_USER->msg("uw_data_stat1")."|0|".$I18N_SIMPLE_USER->msg("uw_data_stat2")."");
	$mita->setValue("empty","","",0);
	
	$mita->setValue("subline",$I18N_SIMPLE_USER->msg("uw_firmendaten_headline") ,"left",0);	
	$mita->setCols(1);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_firmenname"),"company_name",0);
	$mita->setCols(2);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_firmenabteilung"),"company_department",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_firmenbetaetigung"),"company_operating_field",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_firmenstrasse"),"company_street",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_firmenplz"),"company_plz",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_firmenort"),"company_town",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_firmentelefon"),"company_phone",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_firmenmobil"),"company_mobile",0);
	$mita->setValue("text",$I18N_SIMPLE_USER->msg("uw_firmenemail"),"company_email",0);
	$mita->setValue("singleselect",$I18N_SIMPLE_USER->msg("uw_firmendaten_headline"),"company_data_public",0, "1|".$I18N_SIMPLE_USER->msg("uw_data_stat1")."|0|".$I18N_SIMPLE_USER->msg("uw_data_stat2")."");	
	
	
	$mita->setValue("subline",$I18N_SIMPLE_USER->msg("uw_pers_eigenschaften_vorlieben") ,"left",0);	
	$mita->setCols(1);
	$mita->setValue("textarea",$I18N_SIMPLE_USER->msg("uw_pers_positive"),"personally_positive_characteristics",0);
	$mita->setCols(2);
	$mita->setValue("textarea",$I18N_SIMPLE_USER->msg("uw_pers_negative"),"personally_negaitve_characteristics",0);
	$mita->setValue("textarea",$I18N_SIMPLE_USER->msg("uw_pers_hobbys"),"personally_hobby",0);
	$mita->setValue("textarea",$I18N_SIMPLE_USER->msg("uw_pers_lieblingsorte"),"personally_favorite_place",0);
	$mita->setValue("textarea",$I18N_SIMPLE_USER->msg("uw_pers_motto"),"personally_slogan",0);
	$mita->setValue("singleselect",$I18N_SIMPLE_USER->msg("uw_pers_eigenschaften_vorlieben"),"personally_data_public",0, "1|".$I18N_SIMPLE_USER->msg("uw_data_stat1")."|0|".$I18N_SIMPLE_USER->msg("uw_data_stat2")."");		

	echo $mita->showForm();

	echo "<br><br><a href=index.php?page=".$mypage."><b>&laquo; Zurück zur Übersicht</b></a><br>";
}

//------------------------------> User löschen
if($func == "delete"){
	$query = "delete from rex_2_user where id='".$oid."' ";
	$delsql = new sql;
	$delsql->debugsql=0;
	$delsql->setQuery($query);
	$func = "";
}



//------------------------------> Userliste
if($func == ""){

	$sql = "select * from rex_2_user order by user_login";

	
	$mit = new rexlist;
	$mit->setQuery($sql);
	$mit->setGlobalLink("index.php?page=".$mypage."&next=");
	//$mit->setValue("id","id");
	$mit->setValue("Login","user_login");
	$mit->setLink("index.php?page=".$mypage."&func=edit&oid=","id");
	$mit->setValue("Name","user_name");
	$mit->setLink("index.php?page=".$mypage."&func=edit&oid=","id");
	$mit->setValue("Vorname","user_firstname");
		
	$mit->addColumn("löschen","index.php?page=".$mypage."&func=delete&oid=","id"," onclick=\"return confirm('".$I18N_SIMPLE_USER->msg("uw_sicherdel")."');\"");
	
	echo $mit->showall($next);

	echo "<br><br><a href=index.php?page=".$mypage."&func=add><b>".$I18N_SIMPLE_USER->msg("uw_useranlegen")."</b></a><br>";


}

?>