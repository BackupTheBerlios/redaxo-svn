<?php

$message_corpus = "<table border=0 cellpadding=5 cellspacing=1 width=770>
<tr class=warning><td class=icon align=center width=30><img src=\"pics/warning.gif\" height=\"16\" width=\"16\"></td><td class=warning>##msg##</td></tr></table><br />";

$delrel = "";

if($function == "edit_article" && ($send!=1 || isset($uebernehmen)) ){$langswitchadd="&aid=".$aid."&function=".$function."";}else{$langswitchadd = "";}
if(isset($articlesearch)){$langswitchadd.="&articlesearch=".$articlesearch;}

// -----------------------> zugriff auf categorien
function add_cat_options( &$select, &$cat, &$cat_ids, $groupName = '') {
    if( empty( $cat)) {
        return;
    }

    $cat_ids[] = $cat->getId();
    $select->add_option($cat->getName(),$cat->getId(), $cat->getId(),$cat->getParentId());
    $childs = $cat->getChildren();

    if ( is_array( $childs)) {
        foreach ( $childs as $child) {
            add_cat_options( $select, $child, $cat_ids, $cat->getName());
        }
    }
}


// -----------------------> Sprachweiche
@reset($REX[CLANG]);
if (count($REX[CLANG])>1)
{
	$add = "<table class=rex border=0 cellpadding=5 cellspacing=1 width=770><tr><td class=icon><img src=pics/leer.gif width=16 height=16></td><td>&nbsp;<b>".$I18N_SIMPLE_SHOP->msg("languages").":</b> | ";
	while( list($key,$val) = each($REX[CLANG]) )
	{
		if ($key==$clang) $add .= "$val | ";
		else $add .= "<a href=index.php?page=simple_shop&clang=$key".$langswitchadd." >$val</a> | ";
	}
	$add .= "</b></td></tr></table>";
	echo $add;
}

// ----------------------->  Suche der Artikel über die Kategorien

	$sel_cat = new select;
	$sel_cat->set_style("width:100%;");
	$sel_cat->set_size(1);
	$sel_cat->set_name("articlesearch");
	$sel_cat->set_id("articlesearch");
	$sel_cat->set_selected($articlesearch);
	$STYLE= "onchange='document.forms[0].submit();'";
	$sel_cat->add_option($I18N_SIMPLE_SHOP->msg("please_choose_a_cat"),"100000000000000000");
	$cat_ids = array();
	if ($rootCats = OOCategory::getRootCategories())
	{
		foreach( $rootCats as $rootCat) {
		    add_cat_options( $sel_cat, $rootCat, $cat_ids);
		}
	}
	echo "
	<table class=rex border=0 cellpadding=5 cellspacing=1 width=770>
	<tr>
		<th class=icon>&nbsp;</th>
		<th colspan=2 align=left>".$I18N_SIMPLE_SHOP->msg("product_overview")."</th>
	</tr>
	<tr>
		<form action='index.php' method='post' name=catsearch>
		<input type='hidden' name='page' value='".$mypage."' />
		<input type='hidden' name='clang' value='".$clang."' />
			
		<td class=grey>&nbsp;</td>
		<td class=grey width=280>
			".$sel_cat->out()."
		</td>
		<td class=grey>
			<table border=0 cellspacing=0 cellpadding=0>
			<td>
			<input type=submit name='cs' value='".$I18N_SIMPLE_SHOP->msg("show")."' />
			</td></form><form action='index.php' method='post' name=catsearch>
				<input type='hidden' name='clang' value='".$clang."' />
				<input type='hidden' name='page' value='".$mypage."' />
				<input type='hidden' name='articlesearch' value='' />
			<td>
				<input type=submit name='cs' value='".$I18N_SIMPLE_SHOP->msg("show_all")."' />
			</td></form><form action='index.php' method='post' name=catsearch>
				<input type='hidden' name='clang' value='".$clang."' />
				<input type='hidden' name='page' value='".$mypage."' />
				<input type='hidden' name='articlesearch' value='0' />
			<td>
				<input type=submit name='cs' value='".$I18N_SIMPLE_SHOP->msg("show_wo_cat")."' />
			</td></form>
			</table>
		</td>
	</tr>
	</table>";


	$STYLE= "";




if($function == "edit_article"){ // ---------------> Artikel wird editiert oder angelegt

	$sel_cat = new select;
	$sel_cat->multiple(1);
	$sel_cat->set_style("width:100%;");
	$sel_cat->set_size(20);
	$sel_cat->set_name("article[cat][]");

	$cat_ids = array();
	if ($rootCats = OOCategory::getRootCategories())
	{
		foreach( $rootCats as $rootCat) {
		    add_cat_options( $sel_cat, $rootCat, $cat_ids);
		}
	}

	if($send == 1){ //------------------> Das Formular wurde abgeschickt

		if ($article[name] == ""){$article[name] = "[leer]";}

		$sql = new sql;
		$sql->debugsql=0;

		// Kategorien werden aufbereitet
		$diecats = "|";
		if(is_array($article[cat])){
			foreach($article[cat] as $ac){

				$diecats .= $ac."|";

			}
		}
        $article[deliverprice] = number_format(ereg_replace(",",".",$article[deliverprice]), 2, ".", "");
        $article[oldprice] = number_format(ereg_replace(",",".",$article[oldprice]), 2, ".", "");
		$article[price] = number_format(ereg_replace(",",".",$article[price]), 2, ".", "");
		$article[tax] = number_format(ereg_replace(",",".",$article[tax]), 2, ".", "");

		$sql->setTable("rex_4_article");
		$sql->setValue("name", $article[name]);
		$sql->setValue("artnr", $article[artnr]);
		$sql->setValue("description", $article[desc]);
		$sql->setValue("detaildesc", $VALUE[1]);
		$sql->setValue("price", $article[price]);
		$sql->setValue("old_price", $article[oldprice]);
		$sql->setValue("deliver_price", $article[deliverprice]);
		$sql->setValue("mwst", $article[tax]);
		$sql->setValue("status", $article[status]);

		$sql->setValue("instock", $article[instock]);
		$sql->setValue("stockinfo", $article[stockinfo]);

		if($REX_MEDIA_1 == "delete file"){
			$thumbnail = "";
		}else{
			$thumbnail = $REX_MEDIA_1;
		}
		$sql->setValue("thumbnail", $thumbnail);
		if($REX_MEDIA_2 == "delete file"){
			$picture = "";
		}else{
			$picture = $REX_MEDIA_2;
		}
		$sql->setValue("picture", $picture);

		$sql->setValue("relation_1", $article[relation_1]);
		$sql->setValue("relation_2", $article[relation_2]);
		$sql->setValue("relation_3", $article[relation_3]);

		if($aid != ""){		 // ----> update eines Artikels
			$sql->where("id='".$aid."' AND clang='$clang'");
			$sql->update();

			if(strlen($sql->error) > 3){ echo preg_replace("!##msg##!",  $I18N_SIMPLE_SHOP->msg("error"), $message_corpus);}
			else{
				echo preg_replace("!##msg##!",  $I18N_SIMPLE_SHOP->msg("product_saved"), $message_corpus);
					if($uebernehmen){
							$send = 0;
							$function="edit_article";
					}else{
						$send = 1;
						$function="";
					}
			}
			$sql->flush();
			$sql->setTable("rex_4_article");
			$sql->setValue("category", $diecats);
			$sql->where("id='".$aid."'");
			$sql->update();


		}else{	//----> Neuer Artikel wird angelegt
			$sql2 = new sql;
			$sql2->setQuery("SELECT MAX(id) as theid from rex_4_article");

			$theid = $sql2->getValue("theid");
			if($theid == "")$theid = 0;
			$theid++;
			$aid = $theid;
			foreach($REX[CLANG] as $key=>$language){
				$sql->flush();
				$sql->setTable("rex_4_article");
				$sql->setValue("name", $article[name]);
				$sql->setValue("artnr", $article[artnr]);
				$sql->setValue("id", $theid);
				$sql->setValue("clang", $key);
				$sql->setValue("category", $diecats);
				$sql->setValue("description", $article[desc]);
				$sql->setValue("detaildesc", $VALUE[1]);
				$sql->setValue("price", $article[price]);
				$sql->setValue("old_price", $article[oldprice]);
				$sql->setValue("deliver_price", $article[deliverprice]);
				$sql->setValue("mwst", $article[tax]);
				$sql->setValue("picture", $picture);

				$sql->setValue("instock", $article[instock]);
				$sql->setValue("stockinfo", $article[stockinfo]);

				$sql->setValue("relation_1", $article[relation_1]);
				$sql->setValue("relation_2", $article[relation_2]);
				$sql->setValue("relation_3", $article[relation_3]);

				$sql->setValue("thumbnail", $thumbnail);
				$sql->setValue("status", $article[status]);
				$sql->insert();

			}
			$aid = $theid;
			if($sql->error != ""){
				echo preg_replace("!##msg##!",  $I18N_SIMPLE_SHOP->msg("error"), $message_corpus);
			}else{
				echo preg_replace("!##msg##!",  $I18N_SIMPLE_SHOP->msg("product_added"), $message_corpus);
				if($uebernehmen){
    				$send = 0;
				}else{
                    $send = 1;
                    $function="";
				}
				
				
			}
		}
	}

	if($send == 0){ // ---> Ausgabe des Formulars

		// --> Kategorien werden aufbereitet.
		$act_art = new shop_article($aid, $clang);
		foreach( $act_art->getCategories() as $c){
			$sel_cat->set_selected($c);
		}
		if($aid == ""){
			$sel_cat->set_selected($articlesearch);
		}

		$beschreibungsfeld = new rex_wysiwyg_editor;
		$beschreibungsfeld->id 			= 1;
  	 	$beschreibungsfeld->content 	= $act_art->getDetail();
    	$beschreibungsfeld->width  		= "";
    	$beschreibungsfeld->height  	= "";
    	$beschreibungsfeld->stylesheet 	= "";
		$beschreibungsfeld->styles  	= "";
		$beschreibungsfeld->lang  		= "";
		$beschreibungsfeld->buttonrow1 	= "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,link,linkHack,unlink,insertEmail,separator,removeformat,pasteRichtext,code"; // ,outdent,indentstyleselect,separator,
		$beschreibungsfeld->buttonrow2 	= " "; // ,separator,image
		$beschreibungsfeld->buttonrow3  = " "; // tablecontrols, separator, visualaid
		$beschreibungsfeld->buttonrow4  = " "; // rowseparator,formatselect,fontselect,fontsizeselect,forecolor,charmap

		echo "
		    <script language=Javascript>
		      <!--//

				function openREXShop(rel){
					// REX_SHOP_1_NAME REX_SHOP_1_ID
					newWindow( 'relations', 'index.php?page=simple_shop&subpage=relations&rel_id='+rel, 660,500,',status=yes,resizable=yes');
		  		}

		   		function deleteREXShop(rel){
		   			// id and name
					document.getElementById('REX_SHOP_'+rel+'_NAME').value = '';
					document.getElementById('REX_SHOP_'+rel+'_ID').value = '';
		  		}
		  		
		  		function setREXShop(rel,id,name)
		  		{
					document.getElementById('REX_SHOP_'+rel+'_NAME').value = name;
					document.getElementById('REX_SHOP_'+rel+'_ID').value = id;
		  		}

		      //-->
			</script>
			<a href=\"index.php?page=simple_shop\" target=\"_self\">&#171; ".$I18N_SIMPLE_SHOP->msg("back_overview")."</a><br /><br />
			<table class=rex width=770 border=0 cellpadding=5 cellspacing=1 />
			<form action='index.php' method=post enctype=multipart/form-data name=REX_FORM>
			<input type=hidden name='page' value='simple_shop' />
			<input type=hidden name='clang' value='$clang' />
			<input type=hidden name='aid' value='".$aid."' />
			<input type=hidden name='articlesearch' value='".$articlesearch."' />
			<input type=hidden name='function' value='edit_article' />
			<input type=hidden name='send' value='1' />
			
			<tr>
				<th class=icon>&nbsp;</th>
				<th width=180>".$I18N_SIMPLE_SHOP->msg("edit_products")."</th>
				<th class=grey>&nbsp;</th>
			</tr>
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("product_id")."</td>
				<td class=grey><input type=text name=article[artnr] value=\"".htmlentities($act_art->getArticleNumber())."\" class=inp100 /></td>
			</tr>
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("product_name")."</td>
				<td class=grey><input type=text name=article[name] value=\"".htmlentities($act_art->getName())."\" class=inp100 /></td>
			</tr>

			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey valign=top>".$I18N_SIMPLE_SHOP->msg("product_cats")."</td>
				<td class=grey>".$sel_cat->out()."<br>".$I18N->msg("ctrl")."</td>
			</tr>

			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey valign=top>".$I18N_SIMPLE_SHOP->msg("product_desc")."</td>
				<td class=grey><textarea name='article[desc]' style='width:100%;height:70px;'>".htmlentities($act_art->getDescription())."</textarea></td>
			</tr>

			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey valign=top>".$I18N_SIMPLE_SHOP->msg("product_ddesc")."</td>
				<td class=grey>";
				echo $beschreibungsfeld->show();
				echo "</td>
			</tr>
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("product_price")."</td>
				<td class=grey><input type=text name=article[price] value=\"".htmlentities($act_art->getPrice())."\" class=inp100 /></td>
			</tr>
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("product_price_old")."</td>
				<td class=grey><input type=text name=article[oldprice] value=\"".htmlentities($act_art->getOldPrice())."\" class=inp100 /></td>
			</tr>
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("product_price_order")."</td>
				<td class=grey><input type=text name=article[deliverprice] value=\"".htmlentities($act_art->getDeliverPrice())."\" class=inp100 /></td>
			</tr>
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("product_tax")."</td>
				<td class=grey><input type=text name=article[tax] value=\"".htmlentities($act_art->getTax())."\" class=inp100 /></td>
			</tr>

			";
		if ($act_art->getInStock()!=0) $checked = " checked";
		else $checked = "";

		echo "
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("instock")."</td>
				<td class=grey><input type=checkbox name=article[instock] value=\"1\" $checked /></td>
			</tr>
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("stockinfo")."</td>
				<td class=grey><input type=text name=article[stockinfo] value=\"".htmlentities($act_art->getStockinfo())."\" class=inp100 /></td>
			</tr>
			<!--
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>Thumbnail</td>
				<td class=grey>
				<table><tr><td><input type=text size=30 name=REX_MEDIA_1 value=\"".$act_art->getThumbnail()."\" class=inpgrey id=REX_MEDIA_1 readonly=readonly></td><td><a href=javascript:openREXMedia(1,0);><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td><td><a href=javascript:deleteREXMedia(1,0);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td><td><a href=javascript:addREXMedia(1,0)><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td></tr></table>
				</td>
			</tr>-->
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("product_image")."</td>
				<td class=grey>
				<table><tr><td><input type=text size=30 name=REX_MEDIA_2 value=\"".$act_art->getImage()."\" class=inpgrey id=REX_MEDIA_2 readonly=readonly></td><td><a href=javascript:openREXMedia(2,0);><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td><td><a href=javascript:deleteREXMedia(2,0);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td><td><a href=javascript:addREXMedia(2,0)><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td></tr></table>
				</td>
			</tr>";

		$rel_id = "";
		$rel_name = "";
		
		if ($rel_1 = $act_art->getRelated(1))
		{
			$rel_id = $rel_1->getID();
			$rel_name = $rel_1->getName();
		}
		
		echo "			
			<tr>
				<input type=hidden name=article[relation_1] id=REX_SHOP_1_ID value=\"$rel_id\">
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("relation")." 1</td>
				<td class=grey>
				<table>
					<tr>
					<td><input type=text size=30 name=REX_SHOP_1_NAME value=\"".$rel_name."\" class=inpgrey id=REX_SHOP_1_NAME readonly=readonly></td>
					<td><a href=javascript:openREXShop(1);><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>
					<td><a href=javascript:deleteREXShop(1);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>
					</tr>
				</table>
				</td>
			</tr>";

		$rel_id = "";
		$rel_name = "";
		
		if ($rel_2 = $act_art->getRelated(2))
		{
			$rel_id = $rel_2->getID();
			$rel_name = $rel_2->getName();
		}
		
		echo "			
			<tr>
				<input type=hidden name=article[relation_2] id=REX_SHOP_2_ID value=\"$rel_id\">
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("relation")." 2</td>
				<td class=grey>
				<table>
					<tr>
					<td><input type=text size=30 name=REX_SHOP_2_NAME value=\"".$rel_name."\" class=inpgrey id=REX_SHOP_2_NAME readonly=readonly></td>
					<td><a href=javascript:openREXShop(2);><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>
					<td><a href=javascript:deleteREXShop(2);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>
					</tr>
				</table>
				</td>
			</tr>";

		$rel_id = "";
		$rel_name = "";
		
		if ($rel_3 = $act_art->getRelated(3))
		{
			$rel_id = $rel_3->getID();
			$rel_name = $rel_3->getName();
		}
		
		echo "			
			<tr>
				<input type=hidden name=article[relation_3] id=REX_SHOP_3_ID value=\"$rel_id\">
				<td class=grey>&nbsp;</td>
				<td class=grey>".$I18N_SIMPLE_SHOP->msg("relation")." 2</td>
				<td class=grey>
				<table>
					<tr>
					<td><input type=text size=30 name=REX_SHOP_3_NAME value=\"".$rel_name."\" class=inpgrey id=REX_SHOP_3_NAME readonly=readonly></td>
					<td><a href=javascript:openREXShop(3);><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>
					<td><a href=javascript:deleteREXShop(3);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>
					</tr>
				</table>
				</td>
			</tr>";

		echo "			
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>Status</td>
				<td class=grey><select name='article[status]' size=1 style='widht:100%;'>
				<option value='0' >offline</option>
				<option value='1' "; if($act_art->getStatus()){ echo " selected ";} echo ">online</option>
				</select></td>
			</tr>
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey>&nbsp;</td>
				<td class=grey>
				<table border=0 cellpadding=0 cellpspacing=4>
				<tr>
				<td valign=top>
				<input type=submit name=submit value='".$I18N_SIMPLE_SHOP->msg("save_back")."' />
				</td>
				<td valign=top>
				<input type=submit name='uebernehmen' value='".$I18N_SIMPLE_SHOP->msg("save")."'  />
				
				</td></form>";
        if($aid){
		  echo "<form action='index.php' method=post>
				<input type=hidden name='aid' value='".$aid."'>
				<input type=hidden name='page' value='simple_shop'>
				<input type=hidden name='function' value='delete_article'><td valign=top>
				
				<input type=submit name=submit value='".$I18N->msg("delete")."' onclick=\"return confirm('".$I18N->msg("delete")." ?');\"  />
				
				</td></form>";
				}
		echo "
				</tr></table>
				</td>
			</tr>
			</table>";

	}

}
if($function != "edit_article"){

	//---------------------------------- Online / Offline switch
	if($function=="online_article"){

		$sql=new sql;
		$sql->setQuery("update rex_4_article set status='1' WHERE id='".$aid."' AND clang='".$clang."'");

	}
	if($function=="offline_article"){

		$sql=new sql;
		$sql->setQuery("update rex_4_article set status='0' WHERE id='".$aid."' AND clang='".$clang."'");

	}
	//----------------------------------- Artikel löschen
	if($function=="delete_article"){

		$sql=new sql;
		$sql->setQuery("delete from rex_4_article WHERE id='".$aid."'");
		if($sql->error == ""){
			echo preg_replace("!##msg##!",  $I18N_SIMPLE_SHOP->msg("product_deleted"), $message_corpus);
		}else{
			echo preg_replace("!##msg##!",  $I18N_SIMPLE_SHOP->msg("error"), $message_corpus);
		}
	}

	echo	"<table class=rex border=0 cellpadding=5 cellspacing=1 width=770>
				<tr>
					<th class=icon width=30><a href='index.php?page=".$mypage."&function=edit_article&clang=$clang&articlesearch=".$articlesearch."' target='_self' ><img src=pics/document_plus.gif width=16 height=16 border=0 title=\"".$I18N->msg("article_add")." alt=\"".$I18N->msg("article_add")."\"></a></th>
					<th align=left>".$I18N_SIMPLE_SHOP->msg("header_article")."</th>

					<th width=250 align=left>".$I18N_SIMPLE_SHOP->msg("header_edit")."</th>
					<th align=left width=153>".$I18N_SIMPLE_SHOP->msg("header_status")."</th>
				</tr>
				";

	if(isset($articlesearch)){//---------------------------------- Liste der Artikel

		$artikle_objekts = shop_category::getArticleList($clang, $articlesearch);

		for($i=0; $i<count($artikle_objekts); $i++){
			echo "
				<tr>
				<td class=grey width=30 align=center><a href='index.php?page=".$mypage."&function=edit_article&aid=".$artikle_objekts[$i]->getId()."&clang=$clang&articlesearch=".$articlesearch."' target='_self'><img src=\"pics/document.gif\" border=\"0\" height=\"16\" width=\"16\"></A></td>
				<td class=grey>".$artikle_objekts[$i]->getName()."</td>
				<td class=grey width=250 ><a href='index.php?page=".$mypage."&function=edit_article&aid=".$artikle_objekts[$i]->getId()."&clang=$clang&articlesearch=".$articlesearch."' target='_self'>".$I18N_SIMPLE_SHOP->msg("header_article_edit")."</td>";

				if ($artikle_objekts[$i]->getStatus() == 0){ $article_status = "<a href='index.php?page=simple_shop&aid=".$artikle_objekts[$i]->getId()."&function=online_article&clang=$clang&articlesearch=".$articlesearch."'><font color=#dd0000>".$I18N->msg("status_offline")."</font></a>"; }elseif( $artikle_objekts[$i]->getStatus() == 1){ $article_status = "<a href=index.php?page=simple_shop&aid=".$artikle_objekts[$i]->getId()."&function=offline_article&clang=$clang&articlesearch=".$articlesearch."><font color=#00dd00>".$I18N->msg("status_online")."</font></a>"; }

				echo "<td class=grey width=153>$article_status</td>";
				echo "</tr>";
		}
	}
	echo "</table>";
}
?>