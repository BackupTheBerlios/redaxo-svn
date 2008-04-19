<?php
/* @datei class.shop_article.inc.php
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

class shop_article{
	
	var $_id;
	var $_name;
	var $_clang;
	var $_path;
	var $_category;
	var $_description;
	var $_artnr;
	var $_mwst;
	var $_price;
	var $_oldprice;
	var $_deliverprice;
	var $_detaildesc;
	var $_thumbnail;
	var $_picture;
	var $_prio;
	var $_status;
	var $_instock;
	var $_stockinfo;

	
	
	function shop_article($ID="", $CLANG="", $NAME="", $PATH="",$CAT="",$DESC="", $ARTNR="", $MWST="", $PRICE="", $OLDPRICE="", $DELIVERPRICE="",$DETAILDESC="", $THUMBNAIL="", $PICTURE="", $RELATION_1="", $RELATION_2="", $RELATION_3="", $PRIO="", $STATUS="",$INSTOCK="", $STOCKINFO=""){
		global $REX, $article;
		if(count(func_get_args())==2)
		{
			
		
			IF($ID!=""){
				$this->_sql = new sql;
				$this->_sql->debugsql=0;
				$this->_sql->setQuery("SELECT * FROM ".$REX[ADDON][tbl][art]["simple_shop"]." WHERE id='".$ID."' and clang='".$CLANG."'");

				if($this->_sql->rows>0){

					$this->_id			= $this->_sql->getValue("id");
					$this->_name		= $this->_sql->getValue("name");
					$this->_clang		= $this->_sql->getValue("clang");
					$this->_path		= $this->_sql->getValue("path");
					$this->_category	= $this->_sql->getValue("category");
					$this->_description	= $this->_sql->getValue("description");
					$this->_artnr		= $this->_sql->getValue("artnr");
					$this->_mwst		= $this->_sql->getValue("mwst");
					$this->_price		= $this->_sql->getValue("price");
					$this->_oldprice	= $this->_sql->getValue("old_price");
					$this->_deliverprice= $this->_sql->getValue("deliver_price");
					$this->_detaildesc	= $this->_sql->getValue("detaildesc");
					$this->_thumbnail	= $this->_sql->getValue("thumbnail");
					$this->_picture		= $this->_sql->getValue("picture");
					
					$this->_relation[0]	= $this->_sql->getValue("relation_1");
					$this->_relation[1]	= $this->_sql->getValue("relation_2");
					$this->_relation[2]	= $this->_sql->getValue("relation_3");
					
					$this->_prio		= $this->_sql->getValue("prio");
					$this->_status		= $this->_sql->getValue("status");
					$this->_instock		= $this->_sql->getValue("instock");
					$this->_stockinfo	= $this->_sql->getValue("stockinfo");

				}else{
					return false;
				}

			}else{
				//--> Defaultwerte:
                $this->_deliverprice = $article['deliverprice'];
                $this->_mwst = $article['tax'];

	  		}
		
		}elseif(count(func_get_args())<2){
		
		
			
		
		}else{
			
			$this->_id			= func_get_arg(0);						
			$this->_clang		= func_get_arg(1);
			$this->_name		= func_get_arg(2);
			$this->_path		= func_get_arg(3);				
			$this->_category	= func_get_arg(4);
			$this->_description	= func_get_arg(5);
			$this->_artnr		= func_get_arg(6);
			$this->_mwst		= func_get_arg(7);
			$this->_price		= func_get_arg(8);
			$this->_oldprice	= func_get_arg(9);
			$this->_deliverprice= func_get_arg(10);
			$this->_detaildesc	= func_get_arg(11);
			$this->_thumbnail	= func_get_arg(12);
			$this->_picture		= func_get_arg(13);
			
			$this->_relation[0]	= func_get_arg(14);
			$this->_relation[1]	= func_get_arg(15);
			$this->_relation[2]	= func_get_arg(16);
			
			$this->_prio		= func_get_arg(17);
			$this->_status		= func_get_arg(18);
			$this->_instock		= func_get_arg(19);
			$this->_stockinfo	= func_get_arg(20);
			
		}
		
	
	}	
	function getName(){
		return $this->_name;
	}
	
	function getCategories(){
		$article = explode("|", $this->_category);
		$return = array();
		foreach($article as $a){
			if($a != "")
				$return[] = $a; 
		}
		return $return;
	}
	
	function getRelatedId($rel_id=1){
		$rel_id--;
		return $this->_relation[$rel_id];
 	}
	
	function getRelated($rel_id=1){
		$rel_id--;
		return new shop_article($this->_relation[$rel_id], $this->_clang);
 	}
	
	
	function getDescription(){
		return $this->_description;
	}
	
	function getArticleNumber(){
		return $this->_artnr;
	}	
	
	function getTax(){
		return $this->_mwst;
	}
	
	function getPrice(){
		return $this->_price;
	}
	
	function getOldPrice(){
		return $this->_oldprice;
	}
	
	function getDeliverPrice(){
		return $this->_deliverprice;
	}
	
	function getDetail(){
		return $this->_detaildesc;
	}
	
	function getThumbnail(){
		return $this->_thumbnail;
	}
	
	function getImage(){
		return $this->_picture;
	}
	
	function getStatus(){
		return $this->_status;
	}
	function getPrio(){
		return $this->_prio;
	}	
	function getId(){
		return $this->_id;
	}	
	function getPath(){
		return $this->_path;
	}
	function getInStock(){
		return $this->_instock;
	}	
	function getStockinfo(){
		return $this->_stockinfo;
	}	
}
 
?>
