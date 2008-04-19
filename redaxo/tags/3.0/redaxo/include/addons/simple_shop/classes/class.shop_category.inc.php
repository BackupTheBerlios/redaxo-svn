<?php
/* @datei class.shop_category.inc.php
 * @date 13.05.2005
 * @firma pergopa
 * @author Fritz Erler
 * @modifier
 * @modified
 * @package project_name
 * @subpackage ...Art der Applikation z.B. Tool-Extern
 * 
 * Kurze Beschreibung:
 */
class shop_category extends OOCategory
{
	
	
	function shop_category(){
	
	}

	
 	
	function getArticleList($clang=0, $id="", $with_offline=true,$order = "id")
	{

		global $REX;
		
		$sql = new sql;
		$sql->debugsql=0;
		
		if($id > 0){
			$add =" where category like '%|".$id."|%' and clang='".$clang."'";
		}elseif($id == "0"){
			$add =" where category like '|' and clang='".$clang."'";
		}else{
			$add =" where clang='".$clang."'";
		}
	
		if(!$with_offline){
			$add .= " AND status>0 ";
		}
	
		$sql->setQuery("SELECT * FROM ".$REX[ADDON][tbl][art]["simple_shop"]." ".$add." ORDER BY $order");
		
		$return = array();
		for($i=0; $i<$sql->rows; $i++){
			$return[] = new shop_article(	
				$sql->getValue("id"), 											
				$sql->getValue("clang"),
				$sql->getValue("name"),
				$sql->getValue("path"),   
				$sql->getValue("category"),
				$sql->getValue("description"),
				$sql->getValue("artnr"),
				$sql->getValue("mwst"),
				$sql->getValue("price"),
				$sql->getValue("old_price"),
				$sql->getValue("deliver_price"),
				$sql->getValue("detaildesc"),
				$sql->getValue("thumbnail"),
				$sql->getValue("picture"),
				$sql->getValue("relation_1"),
				$sql->getValue("relation_2"),
				$sql->getValue("relation_3"),
				$sql->getValue("prio"),
				$sql->getValue("status"),
				$sql->getValue("instock"),
				$sql->getValue("stockinfo"));
			
			$sql->next();
		}
		
		return $return; 	
	}
 	

	
	function searchArticles($search)
	{

		global $REX;
		$strings = explode(" ",$search);
		$counter=0;
	
		foreach($strings as $s){
	
			if($counter != 0) $add.= " AND ";
			$add .= "( name like '%$s%' OR description like '%$s%' OR detaildesc like '%$s%' OR artnr like '%$s%')";
			$counter++;
		}
	
		$sql = new sql;
		$sql->debugsql=0;
		$sql->setQuery("SELECT * FROM ".$REX[ADDON][tbl][art]["simple_shop"]."
			where clang='".$clang."' AND
			status>0 AND ".$add." ORDER BY name");
	
		$return = array();
		for($i=0; $i<$sql->rows; $i++){
		$return[] = new shop_article(
			$sql->getValue("id"),
			$sql->getValue("clang"),
			$sql->getValue("name"),
			$sql->getValue("path"),
			$sql->getValue("category"),
			$sql->getValue("description"),
			$sql->getValue("artnr"),
			$sql->getValue("mwst"),
			$sql->getValue("price"),
			$sql->getValue("old_price"),
			$sql->getValue("deliver_price"),
			$sql->getValue("detaildesc"),
			$sql->getValue("thumbnail"),
			$sql->getValue("picture"),
			$sql->getValue("relation_1"),
			$sql->getValue("relation_2"),
			$sql->getValue("relation_3"),
			$sql->getValue("prio"),
			$sql->getValue("status"),
			$sql->getValue("instock"),
			$sql->getValue("stockinfo"));
			$sql->next();
		}
		
 		return $return;
  	}
}

?>