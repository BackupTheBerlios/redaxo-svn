<?php

/**
 * MetaForm Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_a62_tableExpander extends rex_form
{
  var $metaPrefix;
  var $tableManager;

  function rex_a62_tableExpander($metaPrefix, $metaTable, $tableName, $fieldset, $whereCondition, $method = 'post', $debug = false)
  {
    $this->metaPrefix = $metaPrefix;
    $this->tableManager = new rex_a62_tableManager($metaTable);

    parent::rex_form($tableName, $fieldset, $whereCondition, $method, $debug);
  }

  function init()
  {
    global $REX, $I18N;

    // ----- EXTENSION POINT
    // IDs aller Feldtypen bei denen das Parameter-Feld eingeblendet werden soll
    $typeFields = rex_register_extension_point( 'A62_TYPE_FIELDS', array(REX_A62_FIELD_SELECT, REX_A62_FIELD_RADIO, REX_A62_FIELD_CHECKBOX));

    $field =& $this->addReadOnlyField('prefix', $this->metaPrefix);
    $field->setLabel($I18N->msg('minfo_field_label_prefix'));

    $field =& $this->addTextField('name');
    $field->setLabel($I18N->msg('minfo_field_label_name'));

    $field =& $this->addSelectField('prior');
    $field->setLabel($I18N->msg('minfo_field_label_prior'));
    $select =& $field->getSelect();
    $select->setSize(1);
    $select->addOption($I18N->msg('minfo_field_first_prior'), 1);
    // Im Edit Mode das Feld selbst nicht als Position einf�gen
    $qry = 'SELECT name,prior FROM '. $this->tableName .' WHERE `name` LIKE "'. $this->metaPrefix .'%"';
    if($this->isEditMode())
    {
      $qry .= ' AND field_id != '. $this->getParam('field_id');
    }
    $qry .=' ORDER BY prior';
    $sql = new rex_sql();
    $sql->setQuery($qry);
    for($i = 0; $i < $sql->getRows(); $i++)
    {
      $select->addOption(
        $I18N->msg('minfo_field_after_prior', $sql->getValue('name')),
        $sql->getValue('prior')+1
      );
      $sql->next();
    }

    $field =& $this->addTextField('title');
    $field->setLabel($I18N->msg('minfo_field_label_title'));
    $field->setNotice($I18N->msg('minfo_field_notice_title'));

    $field =& $this->addSelectField('type');
    $field->setLabel($I18N->msg('minfo_field_label_type'));
    $field->setAttribute('onchange', 'checkConditionalFields(this, new Array('. implode(',', $typeFields) .'));');
    $select =& $field->getSelect();
    $select->setSize(1);

    $qry = 'SELECT label,id FROM '. $REX['TABLE_PREFIX'] .'62_type';
    if($this->metaPrefix == 'med_')
      $qry .= ' WHERE label NOT LIKE "REX_MEDIA%"';
    $select->addSqlOptions($qry);

    $notices = '';
    for($i = 1; $i < REX_A62_FIELD_COUNT; $i++)
    {
      if($I18N->hasMsg('minfo_field_params_notice_'. $i))
      {
        $notices .= '<span class="rex-form-notice" id="a62_field_params_notice_'. $i .'" style="display:none">'. $I18N->msg('minfo_field_params_notice_'. $i) .'</span>'. "\n";
      }
    }
    $notices .= '
    <script type="text/javascript">
      var needle = new getObj("'. $field->getAttribute('id') .'");

      checkConditionalFields(needle.obj, new Array('. implode(',', $typeFields) .'));
    </script>';

    $field =& $this->addTextAreaField('params');
    $field->setLabel($I18N->msg('minfo_field_label_params'));
    $field->setSuffix($notices);

    $field =& $this->addTextAreaField('attributes');
    $field->setLabel($I18N->msg('minfo_field_label_attributes'));
    $notice = '<span class="rex-form-notice" id="a62_field_attributes_notice">'. $I18N->msg('minfo_field_attributes_notice') .'</span>'. "\n";
    $field->setSuffix($notice);

    $field =& $this->addTextField('default');
    $field->setLabel($I18N->msg('minfo_field_label_default'));

//    $field =& $this->addTextAreaField('validate');
//    $field->setLabel($I18N->msg('minfo_field_label_validate'));
  }

  function getFieldsetName()
  {
    global $I18N;
    return $I18N->msg('minfo_field_fieldset');
  }

  function getFieldValue($fieldName)
  {
    $el =& $this->getElement($this->getFieldsetName(), $fieldName);
    return $el->getValue();
  }

  function delete()
  {
  	// Infos zuerst selektieren, da nach parent::delete() nicht mehr in der db
    $sql = rex_sql::getInstance();
    $sql->debugsql =& $this->debug;
    $sql->setTable($this->tableName);
    $sql->setWhere($this->whereCondition);
    $sql->select('name');
    $columnName = $sql->getValue('name');
    
    if(($result = parent::delete()) === true)
    {
      // Prios neu setzen, damit keine l�cken entstehen
      $this->organizePriorities(1,2);
      return $this->tableManager->deleteColumn($columnName);
    }
    
    return $result;
  }

  function preDelete($fieldsetName, $fieldName, $fieldValue, &$deleteSql)
  {
    global $REX;

    if($fieldsetName == $this->getFieldsetName() && $fieldName == 'name')
    {
      // Vorm l�schen, Prefix wieder anf�gen
      return $this->addPrefix($fieldValue);
    }

    return parent::preDelete($fieldsetName, $fieldName, $fieldValue, $deleteSql);
  }

  function preSave($fieldsetName, $fieldName, $fieldValue, &$saveSql)
  {
    global $REX;

    if($fieldsetName == $this->getFieldsetName() && $fieldName == 'name')
    {
      // Den Namen mit Prefix speichern
      return $this->addPrefix($fieldValue);
    }

    return parent::preSave($fieldsetName, $fieldName, $fieldValue, $saveSql);
  }

  function preView($fieldsetName, $fieldName, $fieldValue)
  {
    if($fieldsetName == $this->getFieldsetName() && $fieldName == 'name')
    {
      // Den Namen ohne Prefix anzeigen
      return $this->stripPrefix($fieldValue);
    }
    return parent::preView($fieldsetName, $fieldName, $fieldValue);
  }

  function addPrefix($string)
  {
    $lowerString = strtolower($string);
    if(substr($lowerString, 0, strlen($this->metaPrefix)) !== $this->metaPrefix)
    {
      return $this->metaPrefix . $string;
    }
    return $string;
  }

  function stripPrefix($string)
  {
    $lowerString = strtolower($string);
    if(substr($lowerString, 0, strlen($this->metaPrefix)) === $this->metaPrefix)
    {
      return substr($string, strlen($this->metaPrefix));
    }
    return $string;
  }

  function validate()
  {
    global $I18N;

    $fieldName = $this->getFieldValue('name');
    if($fieldName == '')
      return $I18N->msg('minfo_field_error_name');

    if(preg_match('/[^a-zA-Z0-9\_]/', $fieldName))
      return $I18N->msg('minfo_field_error_chars_name');

    // Pr�fen ob schon eine Spalte mit dem Namen existiert (nur beim add n�tig)
    if(!$this->isEditMode())
    {
      $sql = new rex_sql();
      $sql->setQuery('SELECT * FROM '. $this->tableName .' WHERE name="'. $this->addPrefix($fieldName) .'" LIMIT 1');
      if($sql->getRows() == 1)
      {
        return $I18N->msg('minfo_field_error_unique_name');
      }
    }

    return parent::validate();
  }

  function save()
  {
    $fieldName = $this->getFieldValue('name');

    // Den alten Wert aus der DB holen
    // Dies muss hier geschehen, da in parent::save() die Werte f�r die DB mit den
    // POST werten �berschrieben werden!
    $fieldOldName = '';
    $fieldOldPrior = 9999999999999; // dirty, damit die prio richtig l�uft...
    $fieldOldDefault = '';
    if($this->sql->getRows() == 1)
    {
      $fieldOldName = $this->sql->getValue('name');
      $fieldOldPrior = $this->sql->getValue('prior');
      $fieldOldDefault = $this->sql->getValue('default');
    }

    if(parent::save())
    {
      global $REX, $I18N;

      $this->organizePriorities($this->getFieldValue('prior'), $fieldOldPrior);
      rex_generateAll();

      $fieldName = $this->addPrefix($fieldName);
      $fieldType = $this->getFieldValue('type');
      $fieldDefault = $this->getFieldValue('default');

      $sql = rex_sql::getInstance();
      $result = $sql->getArray('SELECT `dbtype`, `dblength` FROM `'. $REX['TABLE_PREFIX'] .'62_type` WHERE id='. $fieldType);
      $fieldDbType = $result[0]['dbtype'];
      $fieldDbLength = $result[0]['dblength'];

      // TEXT Spalten d�rfen in MySQL keine Defaultwerte haben
      if($fieldDbType == 'text')
        $fieldDefault = null;

      if($this->isEditMode())
      {
        // Spalte in der Tabelle ver�ndern
        $tmRes = $this->tableManager->editColumn($fieldOldName, $fieldName, $fieldDbType, $fieldDbLength, $fieldDefault);
      }
      else
      {
        // Spalte in der Tabelle anlegen
        $tmRes = $this->tableManager->addColumn($fieldName, $fieldDbType, $fieldDbLength, $fieldDefault);
      }

      if($tmRes)
      {
        // DefaultWerte setzen
        if($fieldDefault != $fieldOldDefault)
        {
          $upd = new rex_sql();
          $upd->setTable($this->tableManager->getTableName());
          $upd->setWhere('`'. $fieldName .'`="'. addSlashes($fieldOldDefault) .'"');
          $upd->setValue($fieldName, addSlashes($fieldDefault));
          return $upd->update();
        }
        // Default werte haben schon zuvor gepasst, daher true zur�ckgeben
        return true;
      }
    }

    return false;
  }

  function organizePriorities($newPrio, $oldPrio)
  {
    if($newPrio == $oldPrio)
      return;

    if ($newPrio < $oldPrio)
      $addsql = 'desc';
    else
      $addsql = 'asc';

    rex_organize_priorities(
      $this->tableName,
      'prior',
      'name LIKE "'. $this->metaPrefix .'%"',
      'prior, updatedate '. $addsql, 
      'field_id'
    );
  }
}