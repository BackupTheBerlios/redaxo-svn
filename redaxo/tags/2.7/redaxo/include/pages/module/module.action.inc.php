<?
	
$OUT = TRUE;

if ($function == "delete")
{
	$del = new sql;
	$del->setQuery("select * from rex_module_action where action_id='$action_id'");	// module mit dieser aktion vorhanden ?
	
	if ($del->getRows()>0)
	{
		$module = "<font class=black>|</font> ";
		$modulname = htmlentities($del->getValue("rex_module_action.module_id"));
		for ($i=0;$i<$del->getRows();$i++)
		{
		 $module .= "<a href=index.php?page=module&function=edit&modul_id=".$del->getValue("rex_module_action.module_id").">".$del->getValue("rex_module_action.module_id")."</a> <font class=black>|</font> ";
		 $del->next();
		}
		
		$message = "<b>".$I18N->msg("action_cannot_be_deleted",$action_id)."</b><br> $module";
	}else
	{
		$del->query("delete from rex_action where id='$action_id'");
		$message = $I18N->msg("action_deleted");
	}
}

if ($function == "add" or $function == "edit")
{

	if ($save == "ja")
	{
		$faction = new sql;

		if ($function == "add")
		{
			$faction->query("insert into rex_action (name,action,prepost,status) VALUES ('$name','$actioninput','$prepost','$status')");
			$message = "<p class=warning>".$I18N->msg("action_added")."</p>";
		}else{
			$faction->query("update rex_action set name='$name',action='$actioninput',prepost='$prepost',status='$status' where id='$action_id'");
			$message = "<p class=warning>".$I18N->msg("action_updated")."</p>";
		}
		
		if ($goon != "")
		{
			$save = "nein";
		}else
		{
			$function = "";
		}
	}



	if ($save != "ja")
	{
		echo "<a name=edit><table border=0 cellpadding=5 cellspacing=1 width=770>";
	
		if ($function == "edit"){
			echo "	<tr><th colspan=3 align=left>".$I18N->msg("action_edit")."</th></tr>";

			$hole = new sql;
			$hole->setQuery("select * from rex_action where id='$action_id'");
			$name		= $hole->getValue("name");
			$actioninput	= $hole->getValue("action");
			$prepost	= $hole->getValue("prepost");
			$status		= $hole->getValue("status");
						
		}else{
			echo "	<tr><th colspan=3 align=left>".$I18N->msg("action_create")."</th></tr>";
			$prepost	= 0; // 0=pre / 1=post
			$status		= 0; // 0=add / 1=edit / 2=delete
		}

		if ($message != "")
		{
			echo "<tr><td colspan=3 class=warning>$message</td></tr>";
		}

		$sel_prepost = new select();
		$sel_prepost->set_name("prepost");
		$sel_prepost->add_option($PREPOST[0],"0");
		$sel_prepost->add_option($PREPOST[1],"1");
		$sel_prepost->set_size(1);
		$sel_prepost->set_selected($prepost);

		$sel_status = new select();
		$sel_status->set_name("status");
		$sel_status->add_option($ASTATUS[0],"0");
		$sel_status->add_option($ASTATUS[1],"1");
		$sel_status->add_option($ASTATUS[2],"2");
		$sel_status->set_size(1);
		$sel_status->set_selected($status);

		echo "	
			<form action=index.php method=post>
			<input type=hidden name=page value=module>
			<input type=hidden name=subpage value=actions>
			<input type=hidden name=function value=$function>
			<input type=hidden name=save value=ja>
			<input type=hidden name=action_id value=$action_id>
			<tr>
				<td width=100 class=grey>".$I18N->msg("action_name")."</td>
				<td class=grey colspan=2><input type=text size=10 name=name value=\"".htmlentities($name)."\" style='width:100%;'></td>
			</tr>
			<tr>
				<td valign=top class=grey>".$I18N->msg("input")."</td>
				<td class=grey colspan=2><textarea cols=20 rows=70 name=actioninput style='width:100%; height: 150;'>".htmlentities($actioninput)."</textarea></td>
			</tr>";
			
		echo "
			<tr>
				<td align=right valign=middle class=grey>$PREPOST[0]/$PREPOST[1]</td>
				<td valign=middle class=grey colspan=2>".$sel_prepost->out()."</td>
			</tr>
			<tr>
				<td align=right valign=middle class=grey>STATUS</td>
				<td valign=middle class=grey colspan=2>".$sel_status->out()."</td>
			</tr>			
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey width=200><input type=submit value='".$I18N->msg("save_action_and_quit")."'></td>
				<td class=grey>";
		
		if ($function != "add") echo "<input type=submit name=goon value='".$I18N->msg("save_action_and_continue")."'>";
		
		echo "</td>
			</tr>
			</form>
			</table>";

		$OUT = false;

	}
}

if ($OUT)
{
	// ausgabe modulliste !
	echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
		<tr>
			<th width=30><a href=index.php?page=module&subpage=actions&function=add><img src=pics/modul_plus.gif width=16 height=16 border=0></a></th>
			<th align=left width=300>".$I18N->msg("action_name")."</th>
			<th align=left>".$I18N->msg("action_functions")."</th>
		</tr>
		";
	
	if ($message != "")
	{
		echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=4 class=warning>$message</td></tr>";
	}
	
	
	$sql = new sql;
	$sql->setQuery("select * from rex_action order by name");
	
	for($i=0;$i<$sql->getRows();$i++){
	
		echo "	<tr bgcolor=#eeeeee>
				<td class=grey align=center><img src=pics/modul.gif width=16 height=16></td>
				<td class=grey><a href=index.php?page=module&subpage=actions&action_id=".$sql->getValue("id")."&function=edit>".htmlentities($sql->getValue("name"))."&nbsp;</a>";
		if ($REX_USER->isValueOf("rights","expertMode[]")) echo " [".$sql->getValue("id")."]";
		echo " [".$PREPOST[$sql->getValue("prepost")]."|".$ASTATUS[$sql->getValue("status")]."]";
		echo "</td>
				<td class=grey><a href=index.php?page=module&subpage=actions&action_id=".$sql->getValue("id")."&function=delete>".$I18N->msg("action_delete")."</a></td>
			</tr>";
		$sql->counter++;
	}
	
	echo "</table>";
}

?>