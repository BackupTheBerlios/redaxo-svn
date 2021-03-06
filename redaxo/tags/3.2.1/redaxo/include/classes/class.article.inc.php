<?php
/**
 * Artikel Objekt. Zust�ndig f�r die Ausgabe eines Artikel mit/ohne Template
 * @package redaxo3
 * @version $Id: class.article.inc.php,v 1.55 2006/04/07 16:12:31 kristinus Exp $
 */

class article
{

  var $slice_id;
  var $article_id;
  var $mode;
  var $article_content;
  var $function;
  var $eval;
  var $category_id;
  var $CONT;
  var $template_id;
  var $ViewSliceId;
  var $contents;
  var $setanker;
  var $save;
  var $ctype;
  var $ctype_var;
  var $clang;
  var $getSlice;

  // ----- Konstruktor
  function article( $article_id = null)
  {
    $this->article_id = 0;
    $this->template_id = 0;
    $this->clang = 0;
    $this->ctype = -1; // zeigt alles an
    $this->ctype_var = "rex_ctype"; // var fuer die ctype unterscheidung in den generated dateien
    $this->slice_id = 0;
    $this->mode = "view";
    $this->article_content = "";
    $this->eval = FALSE;
    $this->setanker = true;


    // AUSNAHME: modul ausw�hlen problem
    // action=index.php#1212 problem
    if (strpos($_SERVER["HTTP_USER_AGENT"],"Mac") and strpos($_SERVER["HTTP_USER_AGENT"],"MSIE") ) $this->setanker = FALSE;

    if ( $article_id !== null) {
      $this->setArticleId( $article_id);
    }
  }

  // ----- Slice Id setzen f�r Editiermodus
  function setSliceId($value)
  {
    $this->slice_id = $value;
  }

  // ----- CType setzen
  function setCType($value)
  {
    $this->ctype = $value;
  }

  function setCLang($value)
  {
    global $REX;
    if ($REX['CLANG'][$value] == "") $value = 0;
    $this->clang = $value;
  }

  function setArticleId($article_id)
  {
    global $REX;

    $article_id = $article_id + 0;
    $this->article_id = $article_id+0;

    if (!$REX['GG'])
    {

      // ---------- select article
      $this->ARTICLE = new sql;
      // $this->ARTICLE->debugsql = 1;
      $this->ARTICLE->setQuery("select * from ".$REX['TABLE_PREFIX']."article where ".$REX['TABLE_PREFIX']."article.id='$article_id' and clang='".$this->clang."'");

      if ($this->ARTICLE->getRows() == 1)
      {
        $this->template_id = $this->ARTICLE->getValue($REX['TABLE_PREFIX']."article.template_id");
        $this->category_id = $this->getValue("category_id");
        return TRUE;
      }else
      {
        $this->article_id = 0;
        $this->template_id = 0;
        $this->category_id = 0;
        return FALSE;
      }
    }else
    {
      if (@include $REX['INCLUDE_PATH']."/generated/articles/".$article_id.".".$this->clang.".article")
      {
        $this->category_id = $REX['ART'][$article_id]['re_id'][$this->clang];
        $this->template_id = $REX['ART'][$article_id]['template_id'][$this->clang];
        return TRUE;
      }else
      {
        return FALSE;
      }
    }
  }

  function setTemplateId($template_id)
  {
    $this->template_id = $template_id;
  }

  function getTemplateId()
  {
    return $this->template_id;
  }

  function setMode($mode)
  {
    $this->mode = $mode;
  }

  function setFunction($function)
  {
    $this->function = $function;
  }

  function setEval($value)
  {
    if ($value) $this->eval = TRUE;
    else $this->eval = FALSE;
  }

  function getValue($value)
  {
    global $REX;

    if ($value == "category_id")
    {
      if ($this->getValue("startpage")!=1) $value = "re_id";
      else if($REX['GG']) $value = "article_id";
      else $value = "id";
    }

    if ($REX['GG']) return $REX['ART'][$this->article_id][$value][$this->clang];
    else return $this->ARTICLE->getValue($value);
  }

  function getArticle($curctype = -1)
  {
    global $module_id,$FORM,$REX_USER,$REX,$REX_SESSION,$REX_ACTION,$I18N;

  // ctype var festlegung komischer umweg
  $a = $this->ctype_var;
  $$a = $curctype;
  $sliceLimit = '';
  if ($this->getSlice){
    //$REX['GG'] = 0;
    $sliceLimit = " and ".$REX['TABLE_PREFIX']."article_slice.id = '" . $this->getSlice . "' ";
  }
    // ----- start: article caching
    ob_start();

    if ($REX['GG'] && !$this->getSlice)
    {
      if ($this->article_id != 0)
      {
        $this->contents = "";
        $filename = $REX['INCLUDE_PATH']."/generated/articles/".$this->article_id.".".$this->clang.".content";
        if ($fd = @fopen ($filename, "r"))
        {
          $this->contents = fread ($fd, filesize ($filename));
          fclose ($fd);
          eval($this->contents);
        }
      }
    }else
    {
      if ($this->article_id != 0)
      {
        // ---------- alle teile/slices eines artikels auswaehlen
        $sql = "select ".$REX['TABLE_PREFIX']."modultyp.id, ".$REX['TABLE_PREFIX']."modultyp.name, ".$REX['TABLE_PREFIX']."modultyp.ausgabe, ".$REX['TABLE_PREFIX']."modultyp.eingabe, ".$REX['TABLE_PREFIX']."modultyp.php_enable, ".$REX['TABLE_PREFIX']."modultyp.html_enable, ".$REX['TABLE_PREFIX']."article_slice.*, ".$REX['TABLE_PREFIX']."article.re_id
          from
            ".$REX['TABLE_PREFIX']."article_slice
          left join ".$REX['TABLE_PREFIX']."modultyp on ".$REX['TABLE_PREFIX']."article_slice.modultyp_id=".$REX['TABLE_PREFIX']."modultyp.id
          left join ".$REX['TABLE_PREFIX']."article on ".$REX['TABLE_PREFIX']."article_slice.article_id=".$REX['TABLE_PREFIX']."article.id
          where
            ".$REX['TABLE_PREFIX']."article_slice.article_id='".$this->article_id."' and
            ".$REX['TABLE_PREFIX']."article_slice.clang='".$this->clang."' and
            ".$REX['TABLE_PREFIX']."article.clang='".$this->clang."'";
        $sql .= $sliceLimit;
        $sql .= "order by
            ".$REX['TABLE_PREFIX']."article_slice.re_article_slice_id";
    
    //print $sql;

        $this->CONT = new sql;
        $this->CONT->setQuery($sql);
        
        // ---------- SLICE IDS/MODUL SETZEN - speichern der daten
        for ($i=0;$i<$this->CONT->getRows();$i++)
        {
          $RE_CONTS[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.id");
          $RE_CONTS_CTYPE[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.ctype");
          $RE_MODUL_OUT[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue($REX['TABLE_PREFIX']."modultyp.ausgabe");
          $RE_MODUL_IN[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue($REX['TABLE_PREFIX']."modultyp.eingabe");
          $RE_MODUL_ID[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue($REX['TABLE_PREFIX']."modultyp.id");
          $RE_MODUL_NAME[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue($REX['TABLE_PREFIX']."modultyp.name");
          $RE_C[$this->CONT->getValue("re_article_slice_id")] = $i;
          $this->CONT->nextValue();
        }

        // ---------- moduleselect: nur module nehmen auf die der user rechte hat
        if($this->mode=="edit")
        {
          $MODULE = new sql;
          $MODULE->setQuery("select * from ".$REX['TABLE_PREFIX']."modultyp order by name");

          $MODULESELECT = new select;
          $MODULESELECT->set_name("module_id");
          $MODULESELECT->set_size(1);
          $MODULESELECT->set_style("width:100%;");
          $MODULESELECT->set_selectextra("onchange='this.form.submit();'");
          $MODULESELECT->add_option("----------------------------  ".$I18N->msg("add_block"),'');

          for ($i=0;$i<$MODULE->getRows();$i++)
          {
            if ($REX_USER->isValueOf("rights","module[".$MODULE->getValue("id")."]") || $REX_USER->isValueOf("rights","admin[]")) $MODULESELECT->add_option($MODULE->getValue("name"),$MODULE->getValue("id"));
            $MODULE->next();
          }
        }




        // ---------- SLICE IDS SORTIEREN UND AUSGEBEN
        $I_ID = 0;
        $PRE_ID = 0;
        $LCTSL_ID = 0;
        $this->article_content = "";
        $this->CONT->resetCounter();
        $tbl_head = "<table width=100% cellspacing=0 cellpadding=5 border=0><tr><td class=lblue>";
        $tbl_bott = "</td></tr></table>";


        for ($i=0;$i<$this->CONT->getRows();$i++)
        {

      // ----- ctype unterscheidung
      if ($i==0 && $this->mode != "edit") $this->article_content = "<?php if (\$".$this->ctype_var." == '".$RE_CONTS_CTYPE[$I_ID]."' || (\$".$this->ctype_var." == '-1')) { ?>";

          // ------------- EINZELNER SLICE - AUSGABE
          $this->CONT->counter = $RE_C[$I_ID];
          $slice_content = "";
          $SLICE_SHOW = TRUE;

          if($this->mode=="edit")
          {

            $this->ViewSliceId = $RE_CONTS[$I_ID];

            $amodule = "
            <table cellspacing=0 cellpadding=5 border=0 width=100%>
            <form action=index.php";
            if ($this->setanker) $amodule .= "#addslice";
            $amodule.= " method=get>
            <input type=hidden name=article_id value=$this->article_id>
            <input type=hidden name=page value=content>
            <input type=hidden name=mode value=$this->mode>
            <input type=hidden name=slice_id value=$I_ID>
            <input type=hidden name=function value=add>
            <input type=hidden name=clang value=".$this->clang.">
            <input type=hidden name=ctype value=".$this->ctype.">
            <tr>
            <td class=dblue>".$MODULESELECT->out()."</td>
            </tr></form></table>";



            // ----- add select box einbauen
            if($this->function=="add" && $this->slice_id == $I_ID)
            {
              $slice_content = $this->addSlice($I_ID,$module_id);
            }else
            {
              $slice_content .= $amodule;
            }


            // ----- edit / delete

            if($REX_USER->isValueOf("rights","module[".$RE_MODUL_ID[$I_ID]."]") || $REX_USER->isValueOf("rights","admin[]"))
            {

              // hat rechte zum edit und delete

              $mne  = "
                <a name=slice$RE_CONTS[$I_ID]></a>
                <table width=100% cellspacing=0 cellpadding=5 border=0>
                <tr>
                <td class=blue width=380><b>$RE_MODUL_NAME[$I_ID]</b></td>
                <td class=llblue align=center><a href=index.php?page=content&article_id=$this->article_id&mode=edit&slice_id=$RE_CONTS[$I_ID]&function=edit&clang=".$this->clang."&ctype=".$this->ctype."#slice$RE_CONTS[$I_ID] class=green12b>".$I18N->msg('edit')."</a></td>
                <td class=llblue align=center><a href=index.php?page=content&article_id=$this->article_id&mode=edit&slice_id=$RE_CONTS[$I_ID]&function=delete&clang=".$this->clang."&ctype=".$this->ctype."&save=1#slice$RE_CONTS[$I_ID] class=red12b onclick='return confirm(\"".$I18N->msg('delete')." ?\")'>".$I18N->msg('delete')."</a></td>";
              if ($REX_USER->isValueOf("rights","moveSlice[]"))
              {
                $mne  .= "<td class=llblue><a href=index.php?page=content&article_id=$this->article_id&mode=edit&slice_id=$RE_CONTS[$I_ID]&function=moveup&clang=".$this->clang."&ctype=".$this->ctype."&upd=".time()."#slice$RE_CONTS[$I_ID] class=green12b><img src=pics/file_up.gif width=16 height=16 border=0 hspace=5></a><a href=index.php?page=content&article_id=$this->article_id&mode=edit&slice_id=$RE_CONTS[$I_ID]&function=movedown&clang=".$this->clang."&ctype=".$this->ctype."&upd=".time()."#slice$RE_CONTS[$I_ID] class=green12b><img src=pics/file_down.gif width=16 height=16 border=0></a></td>";
              }
              $mne .= "</tr></table>";

              $slice_content .= $mne.$tbl_head;
              if($this->function=="edit" && $this->slice_id == $RE_CONTS[$I_ID])
              {
                $slice_content .= $this->editSlice($RE_CONTS[$I_ID],$RE_MODUL_IN[$I_ID],$RE_CONTS_CTYPE[$I_ID]);
              }else
              {
                $slice_content .= $RE_MODUL_OUT[$I_ID];
              }
              $slice_content .= $tbl_bott;
              $slice_content = $this->sliceIn($slice_content);

            }else
            {

              // ----- hat keine rechte an diesem modul

              $mne = "
                <table width=100% cellspacing=0 cellpadding=5 border=0>
                <tr>
                <td class=blue><b>$RE_MODUL_NAME[$I_ID]</b> | <b>".$I18N->msg('no_editing_rights')."</b></td>
                </tr>
                </table>";
              $slice_content .= $mne.$tbl_head.$RE_MODUL_OUT[$I_ID].$tbl_bott;
              $slice_content = $this->sliceIn($slice_content);
            }

          }else
          {

            // ----- wenn mode nicht edit
            if($this->getSlice){
                while(list($k, $v) = each($RE_CONTS))
                  $I_ID = $k;
            }
            
            $slice_content .= $RE_MODUL_OUT[$I_ID];
            $slice_content = $this->sliceIn($slice_content);
          }
          // --------------- ENDE EINZELNER SLICE

          // ---------- slice in ausgabe speichern wenn ctype richtig
            if ($this->ctype == -1 or $this->ctype == $RE_CONTS_CTYPE[$I_ID])
            {
              $this->article_content .= $slice_content;
              
              // last content type slice id
              $LCTSL_ID = $RE_CONTS[$I_ID];
            }

          // ----- zwischenstand: ctype .. wenn ctype neu dann if
          if ($this->mode != "edit" && isset($RE_CONTS_CTYPE[$RE_CONTS[$I_ID]]) && $RE_CONTS_CTYPE[$I_ID] != $RE_CONTS_CTYPE[$RE_CONTS[$I_ID]] && $RE_CONTS_CTYPE[$RE_CONTS[$I_ID]] != "")
          {
            $this->article_content .= "<?php } if(\$".$this->ctype_var." == '".$RE_CONTS_CTYPE[$RE_CONTS[$I_ID]]."' || \$".$this->ctype_var." == '-1'){ ?>";
          }



          // zum nachsten slice
          $I_ID = $RE_CONTS[$I_ID];
          $PRE_ID = $I_ID;

        }

        // ----- end: ctype unterscheidung
        if ($this->mode != "edit" && $i>0) $this->article_content .= "<?php } ?>";

    // ----- add module im edit mode
        if ($this->mode == "edit")
        {
          if($this->function=="add" && $this->slice_id == $LCTSL_ID)
          {
            $slice_content = $this->addSlice($LCTSL_ID,$module_id);
          }else
          {
          	$amodule = "
          <table cellspacing=0 cellpadding=5 border=0 width=100%>
          <form action=index.php";
          if ($this->setanker) $amodule .= "#addslice";
          $amodule.= " method=get>
          <input type=hidden name=article_id value=$this->article_id>
          <input type=hidden name=page value=content>
          <input type=hidden name=mode value=$this->mode>
          <input type=hidden name=slice_id value=$LCTSL_ID>
          <input type=hidden name=function value=add>
          <input type=hidden name=clang value=".$this->clang.">
          <input type=hidden name=ctype value=".$this->ctype.">
          <tr>
          <td class=dblue>".$MODULESELECT->out()."</td>
          </tr></form></table>";
            $slice_content = $amodule;
          }
          $this->article_content .= $slice_content;
        }




        // -------------------------- schreibe content
        if (isset($REX['RC']) and $REX['RC']) echo $this->article_content;
        else eval("?>".$this->article_content);

      }else
      {
        echo $I18N->msg('no_article_available');
      }
    }

    // ----- end: article caching
    $CONTENT = ob_get_contents();
    ob_end_clean();

    return $CONTENT;

  }

  function getArticleTemplate()
  {
    global $FORM,$REX;

    // ----- start: template caching
    ob_start();

    if ($this->getTemplateId() == 0 and $this->article_id != 0)
    {
      echo $this->getArticle();
    }elseif ($this->getTemplateId() != 0 and $this->article_id != 0)
    {
      $template_name = $REX['INCLUDE_PATH']."/generated/templates/".$this->getTemplateId().".template";
      if ($fd = fopen ($template_name, "r"))
      {
        $template_content = fread ($fd, filesize ($template_name));
        fclose ($fd);
      }else
      {
        $template_content = $this->getTemplateId()." not found";
      }

      $template_content = $this->replaceCommonVars( $template_content);
      $template_content = $this->replaceLinks($template_content);

      eval("?>".$template_content);

    }else
    {
      echo "no template";
    }

    // ----- end: template caching
    $CONTENT = ob_get_contents();
    ob_end_clean();

    return $CONTENT;

  }

  // ----- ADD Slice
  // altem inhalt loeschen - sliceClear

  function addSlice($I_ID,$module_id)
  {
    global $REX,$REX_ACTION,$FORM,$I18N;
    $MOD = new sql;
    $MOD->setQuery("select * from ".$REX['TABLE_PREFIX']."modultyp where id=$module_id");
    if ($MOD->getRows() != 1)
    {
      $slice_content = "<table width=100% cellspacing=0 cellpadding=5 border=0><tr><td class=dblue>".$I18N->msg('module_doesnt_exist')."</td></tr></table>";
    }else
    {
      $slice_content = "<a name=addslice></a><table width=100% cellspacing=0 cellpadding=5 border=0>
      <tr><td class=dblue><b>".$I18N->msg('add_block')."</b></td></tr>
      <tr><td class=blue>Modul: <b>".$MOD->getValue("name")."</b></td></tr>
      <tr>
      <td class=lblue>
      <form ENCTYPE=multipart/form-data action=index.php#slice$I_ID method=post name=REX_FORM>
      <input type=hidden name=article_id value=$this->article_id>
      <input type=hidden name=page value=content>
      <input type=hidden name=mode value=$this->mode>
      <input type=hidden name=slice_id value=$I_ID>
      <input type=hidden name=function value=add>
      <input type=hidden name=module_id value=$module_id>
      <input type=hidden name=save value=1>
      <input type=hidden name=clang value=".$this->clang.">
      <input type=hidden name=ctype value=".$this->ctype.">
      ".$MOD->getValue("eingabe")."
      <br><input type=submit value='".$I18N->msg('add_block')."'></form>";
      $slice_content = $this->sliceClear($slice_content);
      $slice_content .= "</td></tr></table>";
    }
    return $slice_content;
  }


  function editSlice($RE_CONTS, $RE_MODUL_IN, $RE_CTYPE)
  {
    global $REX, $REX_ACTION, $FORM, $I18N;
    $slice_content = '<a name="editslice"></a>
      <form enctype="multipart/form-data" action="index.php#slice'.$RE_CONTS.'" method="post" name="REX_FORM">
      <input type="hidden" name="article_id" value="'.$this->article_id.'">
      <input type="hidden" name="page" value="content">
      <input type="hidden" name="mode" value="'.$this->mode.'">
      <input type="hidden" name="slice_id" value="'.$RE_CONTS.'">
      <input type="hidden" name="ctype" value="'.$RE_CTYPE.'">
      <input type="hidden" name="function" value="edit">
      <input type="hidden" name="save" value="1">
      <input type="hidden" name="update" value="0">
      <input type="hidden" name="clang" value="'.$this->clang.'">
      '.$RE_MODUL_IN.'
      <br /><br /><input type="submit" value="'.$I18N->msg('save_block').'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="'.$I18N->msg('update_block').'" onClick="REX_FORM.update.value=1"></form>';

    // werte das erst mal aufgerufen / noch nicht gespeichert / gepspeichert und neu
    if (!isset($REX_ACTION['SAVE'])) $slice_content = $this->sliceIn($slice_content);
    if (!$REX_ACTION['SAVE']) $slice_content = $this->sliceClear($slice_content);
    else $slice_content = $this->sliceIn($slice_content);

    return $slice_content;
  }

  // ----- allgemeines suchen und ersetzen
  function sliceIn($slice_content)
  {
  	global $REX;
    for ($i=1;$i<11;$i++)
    {
      // ----------------------------- LIST BUTTONS

      // ------------- REX_FILELIST_BUTTON

      $medialistvalue = $this->stripPHP($this->convertString($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.filelist$i")));

      $media = "<table class=rexbutton><tr>";
      $media .= "<td valign=top><select name=REX_MEDIALIST_SELECT_$i id=REX_MEDIALIST_SELECT_$i size=8 class=inpgrey100>";
      $medialistarray = explode(",",$medialistvalue);
      if (is_array($medialistarray))
      {
        for($j=0;$j<count($medialistarray);$j++)
        {
          if (current($medialistarray)!="") $media .= "<option value='".current($medialistarray)."'>".current($medialistarray)."</option>\n";
          next($medialistarray);
        }
      }
      $media .= "</select></td>";
      $media .= "<td class=inpicon>".
      "<a href=javascript:moveREXMedialist($i,'top');><img src=pics/file_top.gif width=16 height=16 vspace=2 title='^^' border=0></a>".
      "<br><a href=javascript:moveREXMedialist($i,'up');><img src=pics/file_up.gif width=16 height=16 vspace=2 title='^' border=0></a>".
      "<br><a href=javascript:moveREXMedialist($i,'down');><img src=pics/file_down.gif width=16 height=16 vspace=2 title='v' border=0></a>".
      "<br><a href=javascript:moveREXMedialist($i,'bottom');><img src=pics/file_bottom.gif width=16 height=16 vspace=2 title='vv' border=0></a></td>";
      $media .= "<td class=inpicon>".
      "<a href=javascript:openREXMedialist($i);><img src=pics/file_add.gif width=16 height=16 vspace=2 title='+' border=0></a>".
      "<br><a href=javascript:deleteREXMedialist($i);><img src=pics/file_del.gif width=16 height=16 vspace=2 title='-' border=0></a></td>";
      $media .= "</tr>";
      $media .= "<input type=hidden name=REX_MEDIALIST_$i value='REX_MEDIALIST[$i]' id=REX_MEDIALIST_$i >";
      $media .= "</table><br><br>";
      $slice_content = str_replace("REX_MEDIALIST_BUTTON[$i]",$media,$slice_content);
      $slice_content = str_replace("REX_MEDIALIST[$i]",$medialistvalue,$slice_content);

      // ------------- REX_LINKLIST_BUTTON
      $media = "<input type=text size=30 name=REX_LINKLIST_$i value='REX_LINKLIST[$i]' class=inpgrey id=REX_LINKLIST_$i reado2nly=read2only>";
      $media = $this->stripPHP($media);
      $slice_content = str_replace("REX_LINKLIST_BUTTON[$i]",$media,$slice_content);
      $slice_content = str_replace("REX_LINKLIST[$i]",$this->convertString($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.linklist$i")),$slice_content);

      // ------------- REX_MEDIA
      $media = "<table class=rexbutton><input type=hidden name=REX_MEDIA_DELETE_$i value=0 id=REX_MEDIA_DELETE_$i><tr>";
      $media.= "<td><input type=text size=30 name=REX_MEDIA_$i value='REX_FILE[$i]' class=inpgrey100 id=REX_MEDIA_$i readonly=readonly></td>";
      $media.= "<td class=inpicon><a href=javascript:openREXMedia($i,".$this->clang.");><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>";
      $media.= "<td class=inpicon><a href=javascript:deleteREXMedia($i,".$this->clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
      $media.= "<td class=inpicon><a href=javascript:addREXMedia($i,".$this->clang.")><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td>";
      $media.= "</tr></table>";
      $media = $this->stripPHP($media);

      $slice_content = str_replace("REX_MEDIA_BUTTON[$i]",$media,$slice_content);
      $slice_content = str_replace("REX_FILE[$i]",$this->convertString($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.file$i")),$slice_content);

      // ------------- REX_LINK_BUTTON
      if($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.link$i"))
      {
        $db = new sql;
        $sql = "SELECT name FROM ".$REX['TABLE_PREFIX']."article WHERE id=".$this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.link$i")." and clang=".$this->clang;
        $res = $db->get_array($sql);
        $link_name = $res[0]['name'];
      }else
      {
        $link_name = "";
      }
      $media = "<table class=rexbutton><input type=hidden name=REX_LINK_DELETE_$i value=0 id=REX_LINK_DELETE_$i><input type=hidden name='LINK[$i]' value='REX_LINK[$i]' id=LINK[$i]><tr>";
      $media.= "<td><input type=text size=30 name='LINK_NAME[$i]' value='$link_name' class=inpgrey100 id=LINK_NAME[$i] readonly=readonly></td>";
      $media.= "<td class=inpicon><a href=javascript:openLinkMap($i,".$this->clang.");><img src=pics/file_open.gif width=16 height=16 title='Linkmap' border=0></a></td>";
      $media.= "<td class=inpicon><a href=javascript:deleteREXLink($i,".$this->clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
      $media.= "</tr></table>";
      $media = $this->stripPHP($media);
      $slice_content = str_replace("REX_LINK_BUTTON[$i]",$media,$slice_content);
      $slice_content = str_replace("REX_LINK[$i]",$this->generateLink($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.link$i")),$slice_content);
      $slice_content = str_replace("REX_LINK_ID[$i]",$this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.link$i"),$slice_content);

      // -- show:htmlentities -- edit:nl2br/htmlentities
      $slice_content = str_replace("REX_VALUE[$i]",$this->convertString($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.value$i")),$slice_content);

      // -- show:stripphp -- edit:stripphp
      $slice_content = str_replace("REX_HTML_VALUE[$i]",$this->stripPHP($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.value$i")),$slice_content);

      // -- show:stripphp -- edit:stripphp --
      $slice_content = str_replace("REX_HTML_BR_VALUE[$i]",nl2br($this->stripPHP($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.value$i"))),$slice_content);

      // -- show:- -- edit:-
      $slice_content = str_replace("REX_PHP_VALUE[$i]",$this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.value$i"),$slice_content);

      if ($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.value$i")!="") $slice_content = str_replace("REX_IS_VALUE[$i]","1",$slice_content);

    }

    $slice_content = str_replace("REX_PHP",$this->convertString2($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.php")),$slice_content);
    $slice_content = str_replace("REX_HTML",$this->convertString2($this->stripPHP($this->CONT->getValue($REX['TABLE_PREFIX']."article_slice.html"))),$slice_content);

//    $slice_content = str_replace("REX_ARTICLE_ID",$this->article_id,$slice_content);
//    $slice_content = str_replace("REX_CUR_CLANG",$this->clang,$slice_content);
//    $slice_content = str_replace("REX_CATEGORY_ID",$this->category_id,$slice_content);
    $slice_content = $this->replaceCommonVars( $slice_content);

    // function in function_rex_modrewrite.inc.php
    if ($this->mode != "edit") $slice_content = $this->replaceLinks($slice_content);

    return $slice_content;

  }


  // ----- Slice loeschen damit Werte in den n�chsten Slice nicht �bernommen werden
  function sliceClear($slice_content)
  {

    global $REX, $REX_ACTION;

    for ($i=1;$i<11;$i++)
    {
      // ----------------------------- LIST BUTTONS
      // REX_FILELIST_BUTTON
      $media = "<table class=rexbutton><tr>";
      $media .= "<td valign=top><select name=REX_MEDIALIST_SELECT_$i id=REX_MEDIALIST_SELECT_$i size=8 class=inpgrey100>";
      $medialistarray = explode(",",$REX_ACTION['MEDIALIST'][$i]);
      if (is_array($medialistarray))
      {
        for($j=0;$j<count($medialistarray);$j++)
        {
          if (current($medialistarray)!="") $media .= "<option value='".current($medialistarray)."'>".current($medialistarray)."</option>\n";
          next($medialistarray);
        }
      }
      $media .= "</select></td>";
      $media .= "<td class=inpicon>".
      "<a href=javascript:moveREXMedialist($i,'top');><img src=pics/file_top.gif width=16 height=16 vspace=2 title='^^' border=0></a>".
      "<br><a href=javascript:moveREXMedialist($i,'up');><img src=pics/file_up.gif width=16 height=16 vspace=2 title='^' border=0></a>".
      "<br><a href=javascript:moveREXMedialist($i,'down');><img src=pics/file_down.gif width=16 height=16 vspace=2 title='v' border=0></a>".
      "<br><a href=javascript:moveREXMedialist($i,'bottom');><img src=pics/file_bottom.gif width=16 height=16 vspace=2 title='vv' border=0></a></td>";
      $media .= "<td class=inpicon>".
      "<a href=javascript:openREXMedialist($i);><img src=pics/file_add.gif width=16 height=16 vspace=2 title='+' border=0></a>".
      "<br><a href=javascript:deleteREXMedialist($i);><img src=pics/file_del.gif width=16 height=16 vspace=2 title='-' border=0></a></td>";
      $media .= "</tr>";
      $media .= "<input type=hidden name=REX_MEDIALIST_$i value='REX_MEDIALIST[$i]' id=REX_MEDIALIST_$i >";
      $media .= "</table><br><br>";
      $slice_content = str_replace("REX_MEDIALIST_BUTTON[$i]",$media,$slice_content);
      $slice_content = str_replace("REX_MEDIALIST[$i]",$REX_ACTION['MEDIALIST'][$i],$slice_content);

      // REX_LINKLIST_BUTTON
      $media = "<input type=text size=30 name=REX_LINKLIST_$i value='REX_LINKLIST[$i]' class=inpgrey id=REX_LINKLIST_$i read2only=readonly>";
      $media = $this->stripPHP($media);
      $slice_content = str_replace("REX_LINKLIST_BUTTON[$i]",$media,$slice_content);
      $slice_content = str_replace("REX_LINKLIST[$i]","",$slice_content);

      // ----------------------------- REX_MEDIA_BUTTON
      $media = "<table class=rexbutton><input type=hidden name=REX_MEDIA_DELETE_$i value=0 id=REX_MEDIA_DELETE_$i><tr>";
      $media.= "<td><input type=text size=30 name=REX_MEDIA_$i value='REX_FILE[$i]' class=inpgrey100 id=REX_MEDIA_$i readonly=readonly></td>";
      $media.= "<td class=inpicon><a href=javascript:openREXMedia($i,".$this->clang.");><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>";
      $media.= "<td class=inpicon><a href=javascript:deleteREXMedia($i,".$this->clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
      $media.= "<td class=inpicon><a href=javascript:addREXMedia($i,".$this->clang.")><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td>";
      $media.= "</tr></table>";
      $media = $this->stripPHP($media);
      $slice_content = str_replace("REX_MEDIA_BUTTON[$i]",$media,$slice_content);
      $slice_content = str_replace("REX_FILE[$i]",$REX_ACTION['FILE'][$i],$slice_content);

      // ----------------------------- REX_LINK_BUTTON
      $link_name = "";

      if ($REX_ACTION['LINK'][$i]>0)
      {
        $db = new sql;
        $sql = "SELECT name FROM ".$REX['TABLE_PREFIX']."article WHERE id=".$REX_ACTION[LINK][$i]." and clang=".$this->clang;
        $res = $db->get_array($sql);
        $link_name = $res[0]['name'];
      }

      $media = "<table class=rexbutton><input type=hidden name=REX_LINK_DELETE_$i value=0 id=REX_LINK_DELETE_$i><input type=hidden name='LINK[$i]' value='REX_LINK[$i]' id=LINK[$i]><tr>";
      $media.= "<td><input type=text size=30 name='LINK_NAME[$i]' value='$link_name' class=inpgrey100 id=LINK_NAME[$i] readonly=readonly></td>";
      $media.= "<td class=inpicon><a href=javascript:openLinkMap($i,".$this->clang.");><img src=pics/file_open.gif width=16 height=16 title='Linkmap' border=0></a></td>";
      $media.= "<td class=inpicon><a href=javascript:deleteREXLink($i,".$this->clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
      $media.= "</tr></table>";
      $media = $this->stripPHP($media);
      $slice_content = str_replace("REX_LINK_BUTTON[$i]",$media,$slice_content);
      $slice_content = str_replace("REX_LINK[$i]",$REX_ACTION['LINK'][$i],$slice_content);
      $slice_content = str_replace("REX_LINK_ID[$i]",$REX_ACTION['LINK'][$i],$slice_content);


      // ----------------------------- REX_ OTHER
      $slice_content = str_replace("REX_VALUE[$i]",htmlspecialchars(stripslashes($REX_ACTION['VALUE'][$i])),$slice_content);
      $slice_content = str_replace("REX_HTML_VALUE[$i]","",$slice_content);
      $slice_content = str_replace("REX_PHP_VALUE[$i]","",$slice_content);
      $slice_content = str_replace("REX_IS_VALUE[$i]","",$slice_content);

    }

    $slice_content = str_replace("REX_PHP",htmlspecialchars(stripslashes($REX_ACTION['PHP'])),$slice_content);
    $slice_content = str_replace("REX_HTML",htmlspecialchars(stripslashes($REX_ACTION['HTML'])),$slice_content);

//    $slice_content = str_replace("REX_ARTICLE_ID","",$slice_content);
//    $slice_content = str_replace("REX_CUR_CLANG","",$slice_content);
//    $slice_content = str_replace("REX_CATEGORY_ID","",$slice_content);
    $slice_content = $this->replaceCommonVars( $slice_content);

    return $slice_content;

  }


  // ------------------------------------- CONVERT

  function stripPHP($content)
  {
    $content = str_replace("<?","&lt;?",$content);
    $content = str_replace("?>","?&gt;",$content);

    return $content;
  }

  function convertString2($content)
  {

    if ($this->mode == "edit" && $this->slice_id == $this->ViewSliceId && $this->function=="edit")
    {
      return htmlspecialchars($content);
    }elseif ($this->mode == "edit")
    {
      return nl2br(htmlspecialchars($content));
    }else
    {
      return $content;
    }
  }

  function convertString($content)
  {
    $content = str_replace("$","&#36;",htmlspecialchars($content));
    if ($this->mode == "edit" && $this->slice_id == $this->ViewSliceId && $this->function=="edit")
    {
      return $content;
    }else
    {
      return nl2br($content);
    }
  }

  // ------------------------------------ / CONVERT


  function generateLink($id)
  {
    global $REX;

    if ($this->mode == "edit")
    {
      return $id;
    }else
    {
      if ($REX['GG']) return "aid$id".".php";
      else return rex_getURL($id,$this->clang);
    }
  }

  function replaceLinks($content){

        // -- preg match REX_LINK_INTERN[ARTICLEID] --
        preg_match_all("/REX_LINK_INTERN\[([0-9]*)\]/im",$content,$matches);
        if ( isset ($matches[0][0]) and $matches[0][0] != ''){
            for ($m = 0; $m < count ($matches[0]); $m++){
                $url = rex_getURL($matches[1][$m],$this->clang);
                $content = str_replace($matches[0][$m],$url,$content);
            }
        }

        // -- preg match redaxo://[ARTICLEID] --
        preg_match_all("/redaxo:\/\/([0-9]*)\/?/im",$content,$matches);
        if ( isset ($matches[0][0]) and $matches[0][0] != ''){
            for ($m = 0; $m < count($matches[0]); $m++){
                $url = rex_getURL($matches[1][$m],$this->clang);
                $content = str_replace($matches[0][$m],$url,$content);
            }
        }

        return $content;
  }

  function replaceCommonVars($content) {
    static $search = array(
       'REX_ARTICLE_ID',
       'REX_CATEGORY_ID',
       'REX_CLANG_ID',
    );

    $replace = array(
      $this->article_id,
      $this->category_id,
      $this->clang,
    );

    return str_replace($search, $replace,$content);
  }

}

?>