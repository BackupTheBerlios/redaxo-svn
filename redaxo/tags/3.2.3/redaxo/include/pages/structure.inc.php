<?php

/** 
 *  
 * @package redaxo3 
 * @version $Id: structure.inc.php,v 1.56 2006/03/23 12:32:08 tbaddade Exp $ 
 */

/*
 * 
 * Todos: prio geschichten
 *
 * ### erstelle neue prioliste wenn noetig
 *
 */

$echo = ''; // Variable definiert

// --------------------------------------------- EXISTIERT DIESER ARTIKEL ?
if (isset ($edit_id) and $edit_id != '')
{
  $thisCat = new sql;
  $thisCat->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."article WHERE id = '".$edit_id."' and clang = '".$clang."'");
  if ($thisCat->getRows() != 1)
    unset ($edit_id);
}
else
{
  unset ($edit_id);
}

if (isset ($article_id) and $article_id != '')
{
  $thisArt = new sql;
  $thisArt->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id='".$article_id."' and clang=$clang");
  if ($thisArt->getRows() != 1)
    unset ($article_id);
}
else
{
  unset ($article_id);
}

// --------------------------------------------- KATEGORIE PFAD UND RECHTE WERDEN �BERPR�FT

include $REX['INCLUDE_PATH']."/functions/function_rex_category.inc.php";

// --------------------------------------------- TITLE

rex_title($I18N->msg("title_structure"), "$KATout");

$sprachen_add = "&category_id=$category_id";
include $REX['INCLUDE_PATH']."/functions/function_rex_languages.inc.php";

// --------------------------------------------- KATEGORIE FUNKTIONEN
if (isset ($catedit_function) and $catedit_function != "" && $edit_id != "" && $KATPERM)
{
  // --------------------- KATEGORIE EDIT

  $old_prio = $thisCat->getValue("catprior");
  $new_prio = (int) $Position_Category;
  if ($new_prio == 0)
    $new_prio = 1;
  $re_id = $thisCat->getValue("re_id");

  $EKAT = new sql;
  $EKAT->setTable($REX['TABLE_PREFIX']."article");
  $EKAT->where("id='$edit_id' and startpage=1 and clang=$clang");
  $EKAT->setValue("catname", "$kat_name");
  $EKAT->setValue("catprior", "$new_prio");
  $EKAT->setValue("path", $KATPATH);
  $EKAT->setValue("updatedate", time());
  $EKAT->setValue("updateuser", $REX_USER->getValue("login"));
  $EKAT->update();

  // ----- PRIOR
  rex_newCatPrio($re_id, $clang, $new_prio, $old_prio);

  $message = $I18N->msg("category_status_updated");

  rex_generateArticle($edit_id);

  // ----- EXTENSION POINT
  $message = rex_register_extension_point('CAT_UPDATED', $message, array (
    "id" => $edit_id,
    "re_id" => $re_id,
    "clang" => $clang,
    "name" => $kat_name,
    "prior" => $new_prio,
    "path" => $KATPATH,
    "status" => $thisCat->getValue('status'
  )));

}
elseif (isset ($catdelete_function) and $catdelete_function != "" && $edit_id != "" && $KATPERM && !$REX_USER->isValueOf("rights", "editContentOnly[]"))
{
  // --------------------- KATEGORIE DELETE
  $KAT = new sql;
  $KAT->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id='$edit_id' and clang='$clang' and startpage=1");
  if ($KAT->getRows() == 0)
  {
    $KAT->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id='$edit_id' and clang='$clang' and startpage=0");
    if ($KAT->getRows() == 0)
    {
      $re_id = $thisCat->getValue("re_id");
      $message = rex_deleteArticle($edit_id);

      // ----- PRIOR
      $CL = $REX['CLANG'];
      reset($CL);
      for ($j = 0; $j < count($CL); $j++)
      {
        $mlang = key($CL);
        rex_newCatPrio($re_id, $mlang, 0, 1);
        next($CL);
      }

      // ----- EXTENSION POINT
      $message = rex_register_extension_point('CAT_DELETED', $message, array (
        "id" => $edit_id,
        "re_id" => $re_id
      ));

    }
    else
    {
      $message = $I18N->msg("category_could_not_be_deleted")." ".$I18N->msg("category_still_contains_articles");
      $function = "edit";
    }
  }
  else
  {
    $message = $I18N->msg("category_could_not_be_deleted")." ".$I18N->msg("category_still_contains_subcategories");
    $function = "edit";
  }

}
elseif (isset ($function) and $function == "status" && $edit_id != "" 
       && ($REX_USER->isValueOf("rights", "admin[]") || $KATPERM && $REX_USER->isValueOf("rights", "publishArticle[]")))
{
  // --------------------- KATEGORIE STATUS
  $KAT->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id='$edit_id' and clang=$clang and startpage=1");
  if ($KAT->getRows() == 1)
  {
    if ($KAT->getValue("status") == 1)
      $newstatus = 0;
    else
      $newstatus = 1;

    $EKAT = new sql;
    $EKAT->setTable($REX['TABLE_PREFIX']."article");
    $EKAT->where("id='$edit_id' and clang=$clang and startpage=1");
    $EKAT->setValue("status", "$newstatus");
    $EKAT->setValue("updatedate", time());
    $EKAT->setValue("updateuser", $REX_USER->getValue("login"));
    $EKAT->update();

    $message = $I18N->msg("category_status_updated");
    rex_generateArticle($edit_id);

    // ----- EXTENSION POINT
    $message = rex_register_extension_point('CAT_STATUS', $message, array (
      "id" => $edit_id,
      "clang" => $clang,
      "status" => $newstatus
    ));

  }
  else
  {
    $message = $I18N->msg("no_such_category");
  }

}
elseif (isset ($function) and $function == "add_category" && $KATPERM && !$REX_USER->isValueOf("rights", "editContentOnly[]"))
{
  // --------------------- KATEGORIE ADD
  $message = $I18N->msg("category_added_and_startarticle_created");
  $template_id = 0;
  unset ($TMP);
  if ($category_id != "")
  {
    $sql = new sql;
    // $sql->debugsql = 1;
    $sql->setQuery("select clang,template_id from ".$REX['TABLE_PREFIX']."article where id=$category_id and startpage=1");
    for ($i = 0; $i < $sql->getRows(); $i++, $sql->next())
    {
      $TMP[$sql->getValue("clang")] = $sql->getValue("template_id");
    }
  }

  $Position_New_Category = (int) $Position_New_Category;
  if ($Position_New_Category == 0)
    $Position_New_Category = 1;

  unset ($id);
  reset($REX['CLANG']);
  while (list ($key, $val) = each($REX['CLANG']))
  {

    // ### erstelle neue prioliste wenn noetig  

    $template_id = 0;
    if (isset ($TMP[$key]) && $TMP[$key] != "")
      $template_id = $TMP[$key];

    $AART = new sql;
    // $AART->debugsql = 1;
    $AART->setTable($REX['TABLE_PREFIX']."article");
    if (!isset ($id) or !$id)
      $id = $AART->setNewId("id");
    else
      $AART->setValue("id", $id);
    $AART->setValue("clang", $key);
    $AART->setValue("template_id", $template_id);
    $AART->setValue("name", "$category_name");
    $AART->setValue("catname", "$category_name");
    $AART->setValue("catprior", $Position_New_Category);
    $AART->setValue("re_id", $category_id);
    $AART->setValue("prior", 1);
    $AART->setValue("path", $KATPATH);
    $AART->setValue("startpage", 1);
    $AART->setValue("status", 0);
    $AART->setValue("online_from", time());
    $AART->setValue("online_to", mktime(0, 0, 0, 1, 1, 2010));
    $AART->setValue("createdate", time());
    $AART->setValue("createuser", $REX_USER->getValue("login"));
    $AART->setValue("updatedate", time());
    $AART->setValue("updateuser", $REX_USER->getValue("login"));
    $AART->insert();

    // ----- PRIOR
    rex_newCatPrio($category_id, $key, 0, $Position_New_Category);

    // ----- EXTENSION POINT
    $message = rex_register_extension_point('CAT_ADDED', $message, array (
      "id" => $id,
      "re_id" => $category_id,
      "clang" => $key,
      "name" => $category_name,
      "prior" => $Position_New_Category,
      "path" => $KATPATH,
      "status" => 0
    ));

  }

  rex_generateArticle($id);

}

// --------------------------------------------- ARTIKEL FUNKTIONEN

if (isset ($function) && $function == "status_article" && $article_id != "" 
    && ($REX_USER->isValueOf("rights", "admin[]") || $KATPERM && $REX_USER->isValueOf("rights", "publishArticle[]")))
{
  // --------------------- ARTICLE STATUS
  $GA = new sql;
  $GA->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id='$article_id' and clang=$clang");
  if ($GA->getRows() == 1)
  {
    if ($GA->getValue("status") == 1)
      $newstatus = 0;
    else
      $newstatus = 1;

    $EA = new sql;
    $EA->setTable($REX['TABLE_PREFIX']."article");
    $EA->where("id='$article_id' and clang=$clang");
    $EA->setValue("status", "$newstatus");
    $EA->setValue("updatedate", time());
    $EA->setValue("updateuser", $REX_USER->getValue("login"));
    $EA->update();

    $message = $I18N->msg("article_status_updated");
    rex_generateArticle($article_id);

    // ----- EXTENSION POINT
    $message = rex_register_extension_point('ART_STATUS', $message, array (
      "id" => $article_id,
      "clang" => $clang,
      "status" => $newstatus
    ));

  }
  else
  {
    $message = $I18N->msg("no_such_category");
  }

}
else
  if (isset ($function) and $function == "add_article" && $KATPERM && !$REX_USER->isValueOf("rights", "editContentOnly[]"))
  {
    // --------------------- ARTIKEL ADD
    $Position_New_Article = (int) $Position_New_Article;
    if ($Position_New_Article == 0)
      $Position_New_Article = 1;

    $amessage = $I18N->msg("article_added");

    unset ($id);
    reset($REX['CLANG']);
    while (list ($key, $val) = each($REX['CLANG']))
    {

      // ### erstelle neue prioliste wenn noetig

      $AART = new sql;
      // $AART->debugsql = 1;
      $AART->setTable($REX['TABLE_PREFIX']."article");
      if (!isset ($id) or !$id)
        $id = $AART->setNewId("id");
      else
        $AART->setValue("id", $id);
      $AART->setValue("name", $article_name);
      $AART->setValue("catname", $article_name);
      $AART->setValue("clang", $key);
      $AART->setValue("re_id", $category_id);
      $AART->setValue("prior", $Position_New_Article);
      $AART->setValue("path", $KATPATH);
      $AART->setValue("startpage", 0);
      $AART->setValue("status", 0);
      $AART->setValue("online_from", time());
      $AART->setValue("online_to", mktime(0, 0, 0, 1, 1, 2010));
      $AART->setValue("createdate", time());
      $AART->setValue("createuser", $REX_USER->getValue("login"));
      $AART->setValue("updatedate", time());
      $AART->setValue("updateuser", $REX_USER->getValue("login"));
      $AART->setValue("template_id", $template_id);
      $AART->insert();

      // ----- PRIOR
      rex_newArtPrio($category_id, $key, 0, $Position_New_Article);
    }

    rex_generateArticle($id);

    // ----- EXTENSION POINT
    $message = rex_register_extension_point('ART_ADDED', $message, array (
      "id" => $id,
      "status" => 0,
      "name" => $article_name,
      "re_id" => $category_id,
      "prior" => $Position_New_Article,
      "path" => $KATPATH,
      "template_id" => $template_id
    ));

  }
  else
    if (isset ($function) and $function == "edit_article" && $article_id != "" && $KATPERM)
    {
      // --------------------- ARTIKEL EDIT
      $Position_Article = (int) $Position_Article;
      if ($Position_Article == 0)
        $Position_Article = 1;

      $amessage = $I18N->msg("article_updated");
      $EA = new sql;
      $EA->setTable($REX['TABLE_PREFIX']."article");
      $EA->where("id='$article_id' and clang=$clang");
      $EA->setValue("name", $article_name);
      $EA->setValue("template_id", $template_id);
      // $EA->setValue("path",$KATPATH);
      $EA->setValue("updatedate", time());
      $EA->setValue("updateuser", $REX_USER->getValue("login"));
      $EA->setValue("prior", $Position_Article);
      $EA->update();

      // ----- PRIOR
      rex_newArtPrio($category_id, $clang, $Position_Article, $thisArt->getValue("prior"));
      rex_generateArticle($article_id);

      // ----- EXTENSION POINT
      $message = rex_register_extension_point('ART_UPDATED', $message, array (
        "id" => $article_id,
        "status" => $thisArt->getValue("status"), 
        "name" => $article_name, 
        "clang" => $clang, 
        "re_id" => $category_id, 
        "prior" => $Position_Article, 
        "path" => $KATPATH, 
        "template_id" => $template_id)
        );

    }
elseif (isset ($function) and $function == "delete_article" && $article_id != "" && $KATPERM && !$REX_USER->isValueOf("rights", "editContentOnly[]"))
{
  // --------------------- ARTIKEL DELETE

  $message = rex_deleteArticle($article_id);
  $re_id = $thisArt->getValue("re_id");

  // ----- PRIO
  $CL = $REX['CLANG'];
  reset($CL);
  for ($j = 0; $j < count($CL); $j++)
  {
    $mlang = key($CL);
    rex_newArtPrio($thisArt->getValue("re_id"), $mlang, 0, 1);
    next($CL);
  }

  // ----- EXTENSION POINT
  $message = rex_register_extension_point('ART_DELETED', $message, array (
    "id" => $article_id,
    "re_id" => $re_id
  ));

}

// --------------------------------------------- KATEGORIE LISTE

if ($KATPERM && !$REX_USER->isValueOf("rights", "editContentOnly[]"))
  $addc = '<a href="index.php?page=structure&amp;category_id='.$category_id.'&amp;function=add_cat&amp;clang='.$clang.'"><img src="pics/folder_plus.gif" width="16" height="16" border="0" alt="'.$I18N->msg("add_category").'" title="'.$I18N->msg("add_category").'"></a>';
else
  $addc = "&nbsp;";

echo "<table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>
    <tr>
      <th class=icon>$addc</th>";
if ($REX_USER->isValueOf("rights", "advancedMode[]"))
  echo "<th width=30>ID</th>";
echo "    <th>".$I18N->msg("header_category")."</th>
      <th width=50>".$I18N->msg("header_priority")."</th>
      <th width=300>".$I18N->msg("header_edit_category")."</th>
      <th width=153>".$I18N->msg("header_status")."</th>
    </tr>";

if (isset ($message) and $message != "")
  echo "<tr class=warning><td align=center ><img src=pics/warning.gif width=16 height=16></td><td colspan=5><b>$message</b></td></tr>";
if (isset ($category_id) and $category_id != 0)
  echo "<tr><td>&nbsp;</td><td colspan=5>..</td></tr>";

if (isset ($function) and $function == "add_cat" && $KATPERM && !$REX_USER->isValueOf("rights", "editContentOnly[]"))
{
  // --------------------- KATEGORIE ADD FORM
  $echo .= "
      <tr>
        <form action=index.php name=cat_add_form><input type=hidden name=page value=structure>
        <input type=hidden name=category_id value=$category_id>
        <input type=hidden name=function value='add_category'>
        <input type=hidden name=clang value='$clang'>
        <td class=icon><img src=pics/folder.gif width=16 height=16></td>";
  if ($REX_USER->isValueOf("rights", "advancedMode[]"))
    $echo .= "<td class=dgrey align=center>-</td>";
  $echo .= "
        <td><input type=text size=30 name=category_name></td>
        <td>&nbsp;<input type=text name=Position_New_Category value=\"100\" style='width:30px'></td>
        <td><input type=submit value='".$I18N->msg("add_category")."'></td>
        <td>&nbsp;</td>
        </form>
        <script type='text/javascript'> 
           <!-- 
           document.cat_add_form.category_name.focus(); 
           //--> 
        </script>
      </tr>";
}

// --------------------- KATEGORIE LIST

$KAT = new sql;
$KAT->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id='$category_id' and startpage=1 and clang=$clang order by catprior");
$cat_pos = 0; // Variable definiert und vorbelegt

for ($i = 0; $i < $KAT->getRows(); $i++)
{
  $i_category_id = $KAT->getValue("id");
  if ($KAT->getValue("status") == 0)
  {
    $status_color = "#aa0000";
    $kat_status = $I18N->msg("status_offline");
  }
  else
  {
    $status_color = "#00aa00";
    $kat_status = $I18N->msg("status_online");
  }
  $kat_status = "<font color=$status_color>$kat_status</font>";

  if ($KATPERM)
  {
    if ($REX_USER->isValueOf("rights", "admin[]") || $KATPERM && $REX_USER->isValueOf("rights", "publishCategory[]"))
    {
      $kat_status = "<a href=index.php?page=structure&amp;category_id=$category_id&amp;edit_id=$i_category_id&amp;function=status&amp;clang=$clang><u>$kat_status</u></a>";
    }
    else
    {
      $kat_status = "$kat_status";
    }
      

    $kat_link = "index.php?page=structure&amp;category_id=$i_category_id&amp;clang=$clang";
    $cat_pos++;

    if (isset ($edit_id) and $edit_id == $i_category_id and isset ($function) and $function == "edit")
    {
      // --------------------- KATEGORIE EDIT FORM
      $echo .= "<tr>
              <form action=index.php name=cat_edit_form><input type=hidden name=page value=structure><input type=hidden name=edit_id value=$edit_id><input type=hidden name=category_id value=$category_id><input type=hidden name=cid value=".$KAT->getValue("id")."><input type=hidden name=clang value=$clang>
              <td class=icon><a href=$kat_link><img src=pics/folder.gif width=16 height=16 border=0></a></td>";
      if ($REX_USER->isValueOf("rights", "advancedMode[]"))
        $echo .= "<td class=grey align=center>$i_category_id</td>";
      $echo .= "
              <td><input type=text size=30 name=kat_name value=\"".htmlspecialchars($KAT->getValue("catname"))."\"></td>
              <td><input type=text name=Position_Category value=\"".htmlspecialchars($KAT->getValue("catprior"))."\" style='width:30px'></td>
              <td><input type=submit name=catedit_function value='".$I18N->msg('edit_category')."'>";
      if (!$REX_USER->isValueOf("rights", "editContentOnly[]"))
        $echo .= "<input type=submit name=catdelete_function value='".$I18N->msg('delete_category')."' onclick='return confirm(\"".$I18N->msg('delete')." ?\")'>";
      $echo .= "</td>
              <td>$kat_status</td></form>
              <script type='text/javascript'> 
                 <!-- 
                 document.cat_edit_form.kat_name.focus(); 
                 //--> 
              </script>
              </tr>";
    }
    else
    {
      // --------------------- KATEGORIE WITH WRITE
      $echo .= "<tr>
                <td class=icon><a href=$kat_link><img src=pics/folder.gif border=0 width=16 height=16 align=middle></a></td>";
      if ($REX_USER->isValueOf("rights", "advancedMode[]"))
        $echo .= "<td class=grey align=center>$i_category_id</td>";
      $echo .= "
                <td><a href=$kat_link>".$KAT->getValue("catname")."&nbsp;</a></td>
                <td valign=middle width=20>".htmlspecialchars($KAT->getValue("catprior"))."</td>
                <td><a href=index.php?page=structure&amp;category_id=$category_id&amp;edit_id=$i_category_id&amp;function=edit&amp;clang=$clang>";
      if ($REX_USER->isValueOf("rights", "editContentOnly[]")) $echo .= $I18N->msg('edit_category');
      else $echo .= $I18N->msg("category_edit_delete");
      $echo .= "&nbsp;</a></td>
                <td>$kat_status</td>
                </tr>";
    }

  }
  else
    if ($REX_USER->isValueOf("rights", "csr[$i_category_id]") || $REX_USER->isValueOf("rights", "csw[$i_category_id]"))
    {
      // --------------------- KATEGORIE WITH READ
      $kat_link = "index.php?page=structure&amp;category_id=$i_category_id&amp;clang=$clang";
      $echo .= "<tr>
            <td class=icon><a href=$kat_link><img src=pics/folder.gif border=0 width=16 height=16 align=middle></a></td>";
      if ($REX_USER->isValueOf("rights", "advancedMode[]"))
        $echo .= "<td class=grey align=center>$i_category_id</td>";
      $echo .= "
            <td><a href=$kat_link>".$KAT->getValue("catname")."&nbsp;</a></td>
            <td valign=middle width=20>".htmlspecialchars($KAT->getValue("catprior"))."</td>
            <td>".$I18N->msg("no_permission_to_edit")."</td><td class=grey>$kat_status</td>
            </tr>";
    }

  $KAT->next();
}
echo $echo;
echo "</table>";

// --------------------------------------------- ARTIKEL LISTE

// --------------------- READ TEMPLATES

if ($category_id > -1)
{
  $TEMPLATES = new sql;
  $TEMPLATES->setQuery("select * from ".$REX['TABLE_PREFIX']."template order by name");
  $TMPL_SEL = new select;
  $TMPL_SEL->set_name("template_id");
  $TMPL_SEL->set_size(1);
  $TMPL_SEL->set_style("width:150");
  $TMPL_SEL->add_option($I18N->msg("option_no_template"), "0");

  for ($i = 0; $i < $TEMPLATES->getRows(); $i++)
  {
    if ($TEMPLATES->getValue("active") == 1)
    {
      $TMPL_SEL->add_option($TEMPLATES->getValue("name"), $TEMPLATES->getValue("id"));
    }
    $TEMPLATE_NAME[$TEMPLATES->getValue("id")] = $TEMPLATES->getValue("name");
    $TEMPLATES->nextValue();
  }
  $TEMPLATE_NAME[0] = $I18N->msg("template_default_name");

  // --------------------- ARTIKEL LIST

  echo "  <br><table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>
      <tr>
        <th class=icon>";
  if ($KATPERM && !$REX_USER->isValueOf("rights", "editContentOnly[]"))
    echo "<a href=index.php?page=structure&category_id=$category_id&function=add_art&clang=$clang><img src=pics/document_plus.gif width=16 height=16 border=0 alt=\"".$I18N->msg("article_add")."\" title=\"".$I18N->msg("article_add")."\"></a>";
  else
    echo "&nbsp;";
  echo "</th>";
  if ($REX_USER->isValueOf("rights", "advancedMode[]"))
    echo "<th>ID</th>";
  echo "
        <th>".$I18N->msg("header_article_name")."</th>
        <th width=50>".$I18N->msg("header_priority")."</th>
        <th width=150>".$I18N->msg("header_template")."</th>
        <th width=100>".$I18N->msg("header_date")."</th>
        <th>&nbsp;</th>
        <th colspan=3>".$I18N->msg("header_status")."</th>
      </tr>";

  if (isset ($amessage) and $amessage != "")
  {
    echo '<tr class="warning"><td align="center"><img src="pics/warning.gif" width="16" height="16"></td><td colspan="9"><b>'.$amessage.'</b></td></tr>';
  }

  // --------------------- ARTIKEL ADD FORM

  if (isset ($function) and $function == "add_art" && $KATPERM && !$REX_USER->isValueOf("rights", "editContentOnly[]"))
  {
    if (!isset ($template_id) or $template_id == '')
    {
      if (!isset ($re_id))
        $re_id = '';
      $sql = new sql;
      $sql->setQuery("SELECT template_id FROM ".$REX['TABLE_PREFIX']."article WHERE re_id=$re_id and clang=$clang and startpage=1");
      if ($sql->getRows() == 1)
        $TMPL_SEL->set_selected($sql->getValue("template_id"));
    }
    echo "<tr>
            <form action=index.php method=post name=art_add_form>
            <input type=hidden name=page value=structure>
            <input type=hidden name=category_id value=$category_id>
            <input type=hidden name=clang value=$clang>
            <input type=hidden name=function value='add_article'>
            <td class=icon><img src=pics/document.gif width=16 height=16 border=0></td>";
    if ($REX_USER->isValueOf("rights", "advancedMode[]"))
      echo "<td class=grey align=center>-</td>";
    echo "        
            <td><input type=text name=article_name size=20></td>
            <td>&nbsp;<input type=text name=Position_New_Article value=\"100\" style='width:30px'></td>
            <td>".$TMPL_SEL->out()."</td>
            <td>".strftime($I18N->msg("adateformat"))."&nbsp;</td>
            <td><b>".$I18N->msg("article")."</b></td>
            <td colspan=3><input type=submit value='add_article'></td>
            </form>
            <script type='text/javascript'> 
               <!-- 
               document.art_add_form.article_name.focus(); 
               //--> 
            </script>
            </tr>";
  }

  // --------------------- ARTIKEL LIST

  $sql = new sql;
  $sql->setQuery("SELECT * 
        FROM 
          ".$REX['TABLE_PREFIX']."article 
        WHERE 
          ((re_id='$category_id' and startpage=0) or (id='$category_id' and startpage=1)) 
          and clang=$clang  
        ORDER BY 
          prior, name");

  for ($i = 0; $i < $sql->getRows(); $i++)
  {

    if ($sql->getValue("startpage") == 1)
    {
      $startpage = $I18N->msg("start_article");
      $icon = "liste.gif";
    }
    else
    {
      $startpage = $I18N->msg("article");
      $icon = "document.gif";
    }

    //    $pos++; // wird das noch gebraucht?

    // --------------------- ARTIKEL EDIT FORM

    if (isset ($function) and $function == "edit" && isset ($article_id) && $sql->getValue("id") == $article_id && $KATPERM)
    {

      $TMPL_SEL->set_selected($sql->getValue("template_id"));

      echo "  <tr>
              <form action=index.php method=post name=art_edit_form>
              <input type=hidden name=page value=structure>
              <input type=hidden name=category_id value=$category_id>
              <input type=hidden name=article_id value=".$sql->getValue("id").">
              <input type=hidden name=function value='edit_article'>
              <input type=hidden name=aid value=".$sql->getValue("id").">
              <input type=hidden name=clang value=$clang>
              <td class=icon><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&clang=$clang><img src=pics/$icon width=16 height=16 border=0></a></td>";
      if ($REX_USER->isValueOf("rights", "advancedMode[]"))
        echo "<td class=grey>".$sql->getValue("id")."</td>";
      echo "
              <td><input type=text name=article_name value=\"".htmlspecialchars($sql->getValue("name"))."\" size=20 style='width:100%'></td>
              <td>&nbsp;<input type=text name=Position_Article value=\"".htmlspecialchars($sql->getValue("prior"))."\" style='width:30px'></td>
              <td>".$TMPL_SEL->out()."</td>
              <td>".strftime($I18N->msg("adateformat"), $sql->getValue("createdate"))."&nbsp;</td>
              <td><b>$startpage</b></td>
              <td colspan=3><input type=submit value='".$I18N->msg("edit")."'></td>
              </form>
              <script type='text/javascript'> 
                 <!-- 
                 document.art_edit_form.article_name.focus(); 
                 //--> 
              </script>
              </tr>";

      // --------------------- ARTIKEL PERMISSION TO ENTER

    }
    elseif ($KATPERM)
    {

      // --------------------- ARTIKEL NORMAL VIEW | EDIT AND ENTER

      echo "  <tr>
              <td class=icon><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit&clang=$clang><img src=pics/$icon width=16 height=16 border=0></a></td>";
      if ($REX_USER->isValueOf("rights", "advancedMode[]"))
        echo "<td class=grey align=center>".$sql->getValue("id")."</td>";
      echo "
              <td><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit&clang=$clang>".$sql->getValue("name")."&nbsp;</a></td>
              <td class=grey align=center width=10 valign=middle>".htmlspecialchars($sql->getValue("prior"))."</td>
              <td>".$TEMPLATE_NAME[$sql->getValue("template_id")]."</td>
              <td>".strftime($I18N->msg("adateformat"), $sql->getValue("createdate"))."&nbsp;</td>
              <td><b>$startpage</b></td>
              <td><a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=edit&category_id=$category_id&clang=$clang>".$I18N->msg("change")."</a></td>";

      if ($sql->getValue("startpage") == 1)
      {
        echo "  <td><strike>".$I18N->msg("delete")."</strike></td>
                    <td><strike>online</strike></td>";
      }
      else
      {
        if ($sql->getValue("status") == 0)
        {
          $article_status = "<font color=#aa0000>".$I18N->msg("status_offline")."</font>";
        }
        elseif ($sql->getValue("status") == 1)
        {
          $article_status = "<font color=#00aa00>".$I18N->msg("status_online")."</font>";
        }
        
        if ($REX_USER->isValueOf("rights", "admin[]") || $KATPERM && $REX_USER->isValueOf("rights", "publishArticle[]"))
        {
            $article_status = "<a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=status_article&category_id=$category_id&clang=$clang><u>$article_status</u></a>";
        }
        
        if(!$REX_USER->isValueOf("rights", "editContentOnly[]"))
        {
          $article_delete = "<a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=delete_article&category_id=$category_id&clang=$clang onclick='return confirm(\"".$I18N->msg('delete')." ?\")'>".$I18N->msg("delete")."</a>";
        }else
        {
          $article_delete = '<strike>'.$I18N->msg("delete").'</strike>';
        }
        
        echo "  
        	<td>".$article_delete."</td>
        	<td class=grey>$article_status</td>";
      }
      echo "</tr>";

    }
    else
    {

      // --------------------- ARTIKEL NORMAL VIEW | NO EDIT NO ENTER

      echo "  <tr>
              <td class=icon><img src=pics/$icon width=16 height=16 border=0></td>";
      if ($REX_USER->isValueOf("rights", "advancedMode[]"))
        echo "<td>".$sql->getValue("id")."</td>";
      echo "
              <td>".htmlspecialchars($sql->getValue("name"))."</td>
              <td>".htmlspecialchars($sql->getValue("prior"))."</td>
              <td>".$TEMPLATE_NAME[$sql->getValue("template_id")]."</td>
              <td>".strftime($I18N->msg("adateformat"), $sql->getValue("createdate"))."&nbsp;</td>
              <td ><b>$startpage</b></td>
              <td><strike>".$I18N->msg("change")."</strike></td>
              <td><strike>".$I18N->msg("delete")."</strike></td>
              <td><strike>";
      if ($sql->getValue("status") == 0)
      {
        echo $I18N->msg("status_offline");
      }
      else
      {
        echo $I18N->msg("status_online");
      }
      echo "</strike></td>";
      echo "</tr>";
    }
    $sql->counter++;
  }
}
echo '</table>';
?>