<html>
   <head>
      <title>Simple Shop - Relationen</title>
      <link rel=stylesheet type=text/css href=css/style.css>
      <script language=Javascript>
      <!--//
		<?php echo $msg; ?>
      //-->
      </script>
   </head>
<body>
<table width="100%" cellpadding="0" cellspacing="0">

    <tr style="height: 30px">
        <td class=grey>&nbsp;&nbsp;<b class="head"><?php echo $I18N_SIMPLE_SHOP->msg("header_relations"); ?></b></td>
        <td rowspan="3" width="153px"><img src=pics/logo.gif width="153px" height="61px"></td>
    </tr>


    <tr style="height: 1px">
        <td></td>
    </tr>

    <tr style="height: 30px">
        <td class="grey" >&nbsp;&nbsp;&nbsp;</td>
    </tr>

</table>
<br />
<?php

$mypage= "simple_shop";

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



// ----------------------->  Suche der Artikel über die Kategorien

$sel_cat = new select;
$sel_cat->set_style("width:100%;");
$sel_cat->set_size(1);
$sel_cat->set_name("articlesearch");
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
<table border=0 cellpadding=5 cellspacing=1 width=100%>
<tr>
	<th class=icon width=30>&nbsp;</th>
	<th colspan=2 align=left>".$I18N_SIMPLE_SHOP->msg("product_overview")."</th>
</tr>
<tr>
	<td class=grey>&nbsp;</td><form action='index.php' method='post' name=catsearch>
		<input type='hidden' name='page' value='".$mypage."' />
		<input type='hidden' name='subpage' value='".$subpage."' />
		<input type='hidden' name='clang' value='".$clang."' />
		<input type='hidden' name='prod_id' value='".$prod_id."' />
 		<input type='hidden' name='rel_id' value='".$rel_id."' />
	<td class=grey width=280>
		
		".$sel_cat->out()."
	</td>
	<td class=grey>
		<table border=0 cellspacing=0 cellpadding=0>
		<tr><td>
		<input type=submit name='cs' value='".$I18N_SIMPLE_SHOP->msg("show")."' />
		</td></form><form action='index.php' method='post' name=catsearch>
			<input type='hidden' name='clang' value='".$clang."' />
			<input type='hidden' name='page' value='".$mypage."' />
			<input type='hidden' name='subpage' value='".$subpage."' />
			<input type='hidden' name='prod_id' value='".$prod_id."' />
 			<input type='hidden' name='rel_id' value='".$rel_id."' />
		<td>
			
			<input type='hidden' name='articlesearch' value='' /><input type=submit name='cs' value='".$I18N_SIMPLE_SHOP->msg("show_all")."' />
		</td></form>
		</tr>
		</table>
	</td>
</tr>
</table><br />";

if(isset($articlesearch)){//---------------------------------- Liste der Artikel

	$artikle_objekts = shop_category::getArticleList($clang, $articlesearch);

	for($i=0; $i<count($artikle_objekts); $i++){
		echo "<table border=0 cellpadding=5 cellspacing=1 width=100%>
			<tr>
			<td class=grey width=30 align=center><img src=\"pics/document.gif\" border=\"0\" height=\"16\" width=\"16\"></td>
			<td class=grey>".$artikle_objekts[$i]->getName()."</td>
			<td class=grey width=120 ><a href=\"javascript:opener.setREXShop($rel_id,'".$artikle_objekts[$i]->getId()."','".$artikle_objekts[$i]->getName()."');self.close();\")>".$I18N_SIMPLE_SHOP->msg("header_relation_add")."</td>
			</tr>";
	}

	echo "</table>";
}

?>
<a name="bottom"></a>
   <br/>
   <table class="rexFooter" style="width: 100%" cellpadding="5" cellspacing="0">

      <tr>

         <th colspan="2">&nbsp;</th>
      </tr>

      <tr>
         <td>
            <a href="http://www.pergopa.de" target="_blank" class="black">pergopa kristinus gbr</a> |
            <a href="http://www.redaxo.de" target="_blank" class="black">redaxo.de</a> |
            <a href="http://forum.redaxo.de">?</a>

         </td>
         <td style="text-align: right;">&nbsp;</td>
      </tr>

      </table>

   </body>
</html>




