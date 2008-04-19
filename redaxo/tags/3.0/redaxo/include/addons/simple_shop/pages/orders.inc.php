<?php

$mypage = "simple_shop";

$stats[0] = "<span style=\"color:red\">".$I18N_SIMPLE_SHOP->msg("status_0")."</span>";
$stats[1] = "<span style=\"color:orange\">".$I18N_SIMPLE_SHOP->msg("status_1")."</span>";
$stats[2] = "<span style=\"color:green\">".$I18N_SIMPLE_SHOP->msg("status_2")."</span>";


$sql= new sql;

if($function == "delete"){
	$sql->setTable("rex_4_order");
	$sql->where("id=".$aid."");
	$sql->delete();
	$sql->setTable("rex_4_order_product");
	$sql->where("order_id=".$aid."");
	$sql->delete();

	$function = "";
}

if($function == "edit_article" && $send == 1){

		$sql->setTable("rex_4_order");
		$sql->setValue("status", $article[status]);
		$sql->where("id=".$aid."");
		$sql->update();
		$function = "";
}

if($function == "edit_article"){

	$sql->setQuery("SELECT *,DATE_FORMAT(rex_4_order.date, '%d.%m.%Y %H:%i') as datum
	FROM rex_4_order LEFT JOIN rex_4_order_product ON rex_4_order.id = rex_4_order_product.order_id WHERE rex_4_order.id='".$aid."'");

   	$mailtext 	= nl2br($sql->getValue("mailtext"));
	$status     = $sql->getValue("status");
	
    echo "<a href=\"index.php?page=simple_shop&subpage=".$subpage."\" target=\"_self\">&#171; ".$I18N_SIMPLE_SHOP->msg("back_overview")."</a><br /><br />
			<form action='index.php' method=post>
			<input type=hidden name='page' value='simple_shop' />
			<input type=hidden name='subpage' value='$subpage' />
			<input type=hidden name='clang' value='$clang' />
			<input type=hidden name='aid' value='".$aid."' />
			<input type=hidden name='function' value='edit_article' />
			<input type=hidden name='send' value='1' />
			<table width=770 border=0 cellpadding=5 cellspacing=1 />
			<tr>
				<th width=30>&nbsp;</th>
				<th colspan=2>".$I18N_SIMPLE_SHOP->msg("header_order")."</th>
			</tr>
			<tr>
				<td class=grey width=30>&nbsp;</td>
				<td class=grey width=170>".$I18N_SIMPLE_SHOP->msg("date")."</td>
				<td class=grey width=550>".$sql->getValue("datum")."</td>
			</tr>
			<tr>
				<td class=grey width=30>&nbsp;</td>
				<td class=grey width=170>".$I18N_SIMPLE_SHOP->msg("customer")."</td>
				<td class=grey width=550>".$sql->getValue("name")."</td>
			</tr>
			<tr>
				<td class=grey width=30>&nbsp;</td>
				<td class=grey width=170 valign=top>".$I18N_SIMPLE_SHOP->msg("products")."</td>
				<td class=grey width=550>";
			
	for($i=0; $i<$sql->rows;$i++){

       	echo  $sql->getValue("amount")." x ".$sql->getValue("product_name").", ".number_format($sql->getValue("price"), 2, ".", "" )."€ <br />";
		$sql->next();
		
 	}
			
			
	echo "		</td>
			</tr>
			<tr>
				<td class=grey width=30>&nbsp;</td>
				<td class=grey width=170 valign=top>".$I18N->msg("email")."</td>
				<td class=grey width=550>".$mailtext."</td>
			</tr>
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N->msg("status")."</td>
				<td class=grey><select name='article[status]' size=1 style='widht:100%;'>";

				foreach($stats as $k=>$v){
					if($k == $status){$selected = "selected";}else{$selected = "";}
					echo "<option value='".$k."' ".$selected." >".$v."</option>";
 				}
				
	echo "		</select></td>
			</tr>
			
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>&nbsp;</td>
				<td class=grey>
				<table border=0 cellspacing=0 cellpadding=0>
				<tr><td><input type=submit name=submit value='".$I18N_SIMPLE_SHOP->msg("save")."' /></td></form><form action='index.php' method=post>
				<input type=hidden name='aid' value='".$aid."'>
				<input type=hidden name='subpage' value='".$subpage."'>
				<input type=hidden name='page' value='simple_shop'>
				<input type=hidden name='function' value='delete'><td>
				
				<input type=submit name=submit value='".$I18N->msg("delete")."' onclick=\"return confirm('".$I18N->msg("delete")." ?');\"  />
				</form></td></tr></table>
				
				</td>
			</tr>
			</table>";

}

if($function == ""){

$sql->setQuery("SELECT *, DATE_FORMAT(rex_4_order.date, '%d.%m.%Y %H:%i') as datum FROM rex_4_order ORDER BY date desc");

echo "<table class=rex border=0 cellpadding=5 cellspacing=1 width=770><tr><th class=icon></th><th class=dgrey><strong>".$I18N_SIMPLE_SHOP->msg("orders")."</strong></th></tr></table>";

for($i=0; $i<$sql->rows; $i++){
	echo "<table class=rex border=0 cellpadding=5 cellspacing=1 width=770>
	<tr>
	<td class=grey width=30 align=center><a href='index.php?page=".$mypage."&function=edit_article&aid=".$sql->getValue("id")."&clang=$clang&articlesearch=".$articlesearch."' target='_self'><img src=\"pics/document.gif\" border=\"0\" height=\"16\" width=\"16\"></A></td>
	<td class=grey>".$sql->getValue("datum")." | ".$sql->getValue("name")."</td>

	<td class=grey width=153>".$stats[$sql->getValue("status")]."</td>
	<td class=grey width=250 ><a href='index.php?page=".$mypage."&subpage=".$subpage."&function=edit_article&aid=".$sql->getValue("id")."&clang=$clang&articlesearch=".$articlesearch."' target='_self'>".$I18N_SIMPLE_SHOP->msg("header_order_edit")."</td>";
	echo "</tr></table>";
	$sql->next();
}
}

?>
