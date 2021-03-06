<?php


/**
 * Object Oriented Framework: Bildet ein Medium des Medienpools ab
 * @package redaxo4
 * @version svn:$Id$
 */

class OOMedia
{
  // id
  var $_id = "";
  // parent (FOR FUTURE USE!)
  var $_parent_id = "";
  // categoryid
  var $_cat_id = "";

  // categoryname
  var $_cat_name = "";
  // oomediacategory
  var $_cat = "";

  // filename
  var $_name = "";
  // originalname
  var $_orgname = "";
  // filetype
  var $_type = "";
  // filesize
  var $_size = "";

  // filewidth
  var $_width = "";
  // fileheight
  var $_height = "";

  // filetitle
  var $_title = "";

  // updatedate
  var $_updatedate = "";
  // createdate
  var $_createdate = "";

  // updateuser
  var $_updateuser = "";
  // createuser
  var $_createuser = "";

  /**
   * @access protected
   */
  function OOMedia($id = null)
  {
    $this->getMediaById($id);
  }

  /**
   * @access protected
   */
  function _getTableName()
  {
    global $REX;
    return $REX['TABLE_PREFIX'].'file';
  }

  /**
   * @access protected
   */
  function _getTableJoin()
  {
    $mediatable = OOMedia :: _getTableName();
    $cattable = OOMediaCategory :: _getTableName();
    return $mediatable.' LEFT JOIN '.$cattable.' ON '.$mediatable.'.category_id = '.$cattable.'.id';
  }

  /**
   * @access public
   */
  function getMediaById($id)
  {
    $id = (int) $id;
    if ($id==0)
    {
      return null;
    }

    $query = 'SELECT '.OOMedia :: _getTableName().'.*, '.OOMediaCategory :: _getTableName().'.name catname  FROM '.OOMedia :: _getTableJoin().' WHERE file_id = '.$id;
    $sql = new rex_sql();
//    $sql->debugsql = true;
    $result = $sql->getArray($query);
    if (count($result) == 0)
    {
      return null;
    }

    $result = $result[0];
    $aliasMap = array(
      'file_id' => 'id',
      're_file_id' => 'parent_id',
      'category_id' => 'cat_id',
      'catname' => 'cat_name',
      'filename' => 'name',
      'originalname' => 'orgname',
      'filetype' => 'type',
      'filesize' => 'size'
    );

    $media = new OOMedia();
    foreach($sql->getFieldNames() as $fieldName)
    {
      if(in_array($fieldName, array_keys($aliasMap)))
        $var_name = '_'. $aliasMap[$fieldName];
      else
        $var_name = '_'. $fieldName;

      $media->$var_name = $result[$fieldName];
    }

    return $media;
  }

  /**
   * @access public
   */
  function getMediaByName($filename)
  {
    return OOMedia :: getMediaByFileName($filename);
  }

  /**
   * @access public
   *
   * @example OOMedia::getMediaByExtension('css');
   * @example OOMedia::getMediaByExtension('gif');
   */
  function getMediaByExtension($extension)
  {
    $query = 'SELECT file_id FROM '.OOMedia :: _getTableName().' WHERE SUBSTRING(filename,LOCATE( ".",filename)+1) = "'.$extension.'"';
    $sql = new rex_sql();
    //              $sql->debugsql = true;
    $result = $sql->getArray($query);

    $media = array ();

    if (is_array($result))
    {
      foreach ($result as $row)
      {
        $media[] = & OOMedia :: getMediaById($row['file_id']);
      }
    }

    return $media;
  }

  /**
   * @access public
   */
  function getMediaByFileName($name)
  {
    $query = 'SELECT file_id FROM '.OOMedia :: _getTableName().' WHERE filename = "'.$name.'"';
    $sql = new rex_sql();
    $result = $sql->getArray($query);

    if (is_array($result))
    {
      foreach ($result as $line)
      {
        return OOMedia :: getMediaById($line['file_id']);
      }
    }

    return null;
  }

  /**
   * @access public
   */
  function getId()
  {
    return $this->_id;
  }

  /**
   * @access public
   */
  function getCategory()
  {
    if ($this->_cat === null)
    {
      $this->_cat = & OOMediaCategory :: getCategoryById($this->getCategoryId());
    }
    return $this->_cat;
  }

  /**
   * @access public
   */
  function getCategoryName()
  {
    return $this->_cat_name;
  }

  /**
   * @access public
   */
  function getCategoryId()
  {
    return $this->_cat_id;
  }

  /**
   * @access public
   */
  function getParentId()
  {
    return $this->_parent_id;
  }

  /**
   * @access public
   */
  function hasParent()
  {
    return $this->getParentId() != 0;
  }

  /**
   * @access public
   * @deprecated 12.10.2007
   */
  function getDescription()
  {
    return $this->getValue('med_description');
  }

  /**
   * @access public
   * @deprecated 12.10.2007
   */
  function getCopyright()
  {
    return $this->getValue('med_copyright');
  }

  /**
   * @access public
   */
  function getTitle()
  {
    return $this->_title;
  }

  /**
   * @access public
   */
  function getFileName()
  {
    return $this->_name;
  }

  /**
   * @access public
   */
  function getOrgFileName()
  {
    return $this->_orgname;
  }

  /**
   * @access public
   */
  function getPath()
  {
    global $REX;
    return $REX['HTDOCS_PATH'].'files';
  }

  /**
   * @access public
   */
  function getFullPath()
  {
    return $this->getPath().'/'.$this->getFileName();
  }

  /**
   * @access public
   */
  function getWidth()
  {
    return $this->_width;
  }

  /**
   * @access public
   */
  function getHeight()
  {
    return $this->_height;
  }

  /**
   * @access public
   */
  function getType()
  {
    return $this->_type;
  }

  /**
   * @access public
   */
  function getSize()
  {
    return $this->_size;
  }

  /**
   * @access public
   */
  function getFormattedSize()
  {
    return $this->_getFormattedSize($this->getSize());
  }

  /**
   * @access protected
   */
  function _getFormattedSize($size)
  {

    // Setup some common file size measurements.
    $kb = 1024; // Kilobyte
    $mb = 1024 * $kb; // Megabyte
    $gb = 1024 * $mb; // Gigabyte
    $tb = 1024 * $gb; // Terabyte
    // Get the file size in bytes.

    // If it's less than a kb we just return the size, otherwise we keep going until
    // the size is in the appropriate measurement range.
    if ($size < $kb)
    {
      return $size." Bytes";
    }
    elseif ($size < $mb)
    {
      return round($size / $kb, 2)." KBytes";
    }
    elseif ($size < $gb)
    {
      return round($size / $mb, 2)." MBytes";
    }
    elseif ($size < $tb)
    {
      return round($size / $gb, 2)." GBytes";
    }
    else
    {
      return round($size / $tb, 2)." TBytes";
    }
  }

  /**
   * Formats a datestamp with the given format.
   *
   * If format is <code>null</code> the datestamp is returned.
   *
   * If format is <code>''</code> the datestamp is formated
   * with the default <code>dateformat</code> (lang-files).
   *
   * @access public
   * @static
   */
  function _getDate($date, $format = null)
  {
    if ($format !== null)
    {
      if ($format == '')
      {
        // TODO Im Frontend gibts kein I18N
        // global $I18N;
        //$format = $I18N->msg('dateformat');
        $format = '%a %d. %B %Y';
      }
      return strftime($format, $date);
    }
    return $date;
  }

  /**
   * @access public
   */
  function getUpdateUser()
  {
    return $this->_updateuser;
  }

  /**
   * @access public
    * @see #_getDate
   */
  function getUpdateDate($format = null)
  {
    return $this->_getDate($this->_updatedate, $format);
  }

  /**
   * @access public
   */
  function getCreateUser()
  {
    return $this->_createuser;
  }

  /**
   * @access public
    * @see #_getDate
   */
  function getCreateDate($format = null)
  {
    return $this->_getDate($this->_createdate, $format);
  }

  /**
   * @access public
   */
  function toImage($params = array ())
  {
    global $REX;

    if(!is_array($params))
    {
      $params = array();
    }

    $path = $REX['HTDOCS_PATH'];
    if (isset ($params['path']))
    {
      $path = $params['path'];
      unset ($params['path']);
    }

    // Ist das Media ein Bild?
    if (!$this->isImage())
    {
      $path = 'media/';
      $file = 'file_dummy.gif';

      // Verwenden einer statischen variable, damit getimagesize nur einmal aufgerufen
      // werden muss, da es sehr lange dauert
      static $dummyFileSize;

      if (empty ($dummyFileSize))
      {
        $dummyFileSize = getimagesize($path.$file);
      }
      $params['width'] = $dummyFileSize[0];
      $params['height'] = $dummyFileSize[1];
    }
    else
    {
      $resize = false;

      // ResizeModus festlegen
      if (isset ($params['resize']) && $params['resize'])
      {
        unset ($params['resize']);
        // Resize Addon installiert?
        if (OOAddon::isAvailable('image_resize'))
        {
          $resize = true;
          if (isset ($params['width']))
          {
            $resizeMode = 'w';
            $resizeParam = $params['width'];
            unset ($params['width']);
          }
          elseif (isset ($params['height']))
          {
            $resizeMode = 'h';
            $resizeParam = $params['height'];
            unset ($params['height']);
          }
          elseif (isset ($params['crop']))
          {
            $resizeMode = 'c';
            $resizeParam = $params['crop'];
            unset ($params['crop']);
          }
          else
          {
            $resizeMode = 'a';
            $resizeParam = 100;
          }

          // Evtl. Gr��eneinheiten entfernen
          $resizeParam = str_replace(array (
            'px',
            'pt',
            '%',
            'em'
          ), '', $resizeParam);
        }
      }

      // Bild resizen?
      if ($resize)
      {
        $file = 'index.php?rex_resize='.$resizeParam.$resizeMode.'__'.$this->getFileName();
      }
      else
      {
        // Bild 1:1 anzeigen
        $path .= 'files/';
        $file = $this->getFileName();
      }
    }

    $title = $this->getTitle();

    // Alternativtext hinzuf�gen
    if (!isset($params['alt']))
    {
      if ($title != '')
      {
        $params['alt'] = htmlspecialchars($title);
      }
    }

    // Titel hinzuf�gen
    if (!isset($params['title']))
    {
      if ($title != '')
      {
        $params['title'] = htmlspecialchars($title);
      }
    }

    // Evtl. Zusatzatrribute anf�gen
    $additional = '';
    foreach ($params as $name => $value)
    {
      $additional .= ' '.$name.'="'.$value.'"';
    }

    return sprintf('<img src="%s"%s />', $path.$file, $additional);
  }

  /**
   * @access public
   */
  function toLink($attributes = '')
  {
    return sprintf('<a href="%s" title="%s"%s>%s</a>', $this->getFullPath(), $this->getDescription(), $attributes, $this->getFileName());
  }
  /**
   * @access public
   */
  function toIcon($iconAttributes = array ())
  {
    global $REX;

    $ext = $this->getExtension();
    $icon = $this->getIcon();

    if(!isset($iconAttributes['alt']))
    {
      $iconAttributes['alt'] = '&quot;'. $ext .'&quot;-Symbol';
    }

    if(!isset($iconAttributes['title']))
    {
      $iconAttributes['title'] = $iconAttributes['alt'];
    }

    if(!isset($iconAttributes['style']))
    {
      $iconAttributes['style'] = 'width: 44px; height: 38px';
    }

    $attrs = '';
    foreach ($iconAttributes as $attrName => $attrValue)
    {
      $attrs .= ' '.$attrName.'="'.$attrValue.'"';
    }

    return '<img src="'.$icon.'"'.$attrs.' />';
  }

  /**
   * @access public
   * @static
   */
  function isValid($media)
  {
    return is_object($media) && is_a($media, 'oomedia');
  }

  /**
   * @access public
   */
  function isImage()
  {
    return $this->_isImage($this->getFileName());
  }

  /**
   * @access public
   * @static
   */
  function _isImage($filename)
  {
    static $imageExtensions;

    if (!isset ($imageExtensions))
    {
      $imageExtensions = array (
        'gif',
        'jpeg',
        'jpg',
        'png',
        'bmp'
      );
    }

    return in_array(OOMedia :: _getExtension($filename), $imageExtensions);
  }

  /**
   * @access public
   */
  function isInUse()
  {
    global $REX;

    $sql = new rex_sql();
    $filename = addslashes($this->getFileName());

    $values = array();
    for ($i = 1; $i < 21; $i++)
    {
      $values[] = 'value'.$i.' LIKE "%'.$filename.'%"';
    }

    $files = array();
    $filelists = array();
    for ($i = 1; $i < 11; $i++)
    {
      $files[] = 'file'.$i.'="'.$filename.'"';
      $filelists[] = '(filelist'.$i.' LIKE "'.$filename.',%" OR filelist'.$i.' LIKE "%,'.$filename.',%" OR filelist'.$i.' LIKE "%,'.$filename.'" ) ';
    }

    $where = '';
    $where .= implode(' OR ', $files).' OR ';
    $where .= implode(' OR ', $filelists) .' OR ';
    $where .= implode(' OR ', $values);
    $query = 'SELECT DISTINCT article_id, clang FROM '.$REX['TABLE_PREFIX'].'article_slice WHERE '. $where;

    // ----- EXTENSION POINT
    $query = rex_register_extension_point('OOMEDIA_IS_IN_USE_QUERY', $query,
      array(
        'filename' => $this->getFileName(),
        'media' => $this,
      )
    );

    $res = $sql->getArray($query);
    if($sql->getRows() > 0)
      return $res;

    return FALSE;
  }

  /**
   * @access public
   */
  function toHTML($attributes = '')
  {
    global $REX;

    $file = $this->getFullPath();
    $filetype = $this->getExtension();

    switch ($filetype)
    {
      case 'jpg' :
      case 'jpeg' :
      case 'png' :
      case 'gif' :
      case 'bmp' :
        {
          return $this->toImage($attributes);
        }
      case 'js' :
        {
          return sprintf('<script type="text/javascript" src="%s"%s></script>', $file, $attributes);
        }
      case 'css' :
        {
          return sprintf('<link href="%s" rel="stylesheet" type="text/css"%s>', $file, $attributes);
        }
      default :
        {
          return 'No html-equivalent available for type "'.$filetype.'"';
        }
    }
  }

  /**
   * @access public
   */
  function toString()
  {
    return 'OOMedia, "'.$this->getId().'", "'.$this->getFileName().'"'."<br/>\n";
  }

  // new functions by vscope
  /**
    * @access public
   */
  function getExtension()
  {
    return $this->_getExtension($this->_name);
  }

  /**
   * @access public
   * @static
   */
  function _getExtension($filename)
  {
    return substr(strrchr($filename, "."), 1);
  }

  /**
   * @access public
   */
  function getIcon($useDefaultIcon = true)
  {
    global $REX;

    $ext = $this->getExtension();
    $folder = $REX['HTDOCS_PATH'] .'redaxo/media/';
    $icon = $folder .'mime-'.$ext.'.gif';

    // Dateityp f�r den kein Icon vorhanden ist
    if (!file_exists($icon))
    {
      if($useDefaultIcon)
        $icon = $folder.'mime-default.gif';
      else
        $icon = $folder.'mime-error.gif';
    }
    return $icon;
  }

  /**
   * @access public
   * @return Returns <code>true</code> on success or <code>false</code> on error
   */
  function save()
  {
    $sql = new rex_sql();
    $sql->setTable($this->_getTableName());
    $sql->setValue('re_file_id', $this->getParentId());
    $sql->setValue('category_id', $this->getCategoryId());
    $sql->setValue('filetype', $this->getType());
    $sql->setValue('filename', $this->getFileName());
    $sql->setValue('originalname', $this->getOrgFileName());
    $sql->setValue('filesize', $this->getSize());
    $sql->setValue('width', $this->getWidth());
    $sql->setValue('height', $this->getHeight());
    $sql->setValue('title', $this->getTitle());

    if ($this->getId() !== null)
    {
      $sql->addGlobalUpdateFields();
      $sql->setWhere('file_id='.$this->getId() . ' LIMIT 1');
      return $sql->update();
    }
    else
    {
      $sql->addGlobalCreateFields();
      return $sql->insert();
    }
  }

  /**
   * @access public
   * @return Returns <code>true</code> on success or <code>false</code> on error
   */
  function delete($filename = null)
  {
    global $REX;
    
    if($filename != null)
    {
      $OOMed = OOMedia::getMediaByFileName($filename);
      if($OOMed)
      {
        return $OOMed->delete();
      }
    }else
    {
      $qry = 'DELETE FROM '.$this->_getTableName().' WHERE file_id = '.$this->getId().' LIMIT 1';
      $sql = new rex_sql();
      $sql->setQuery($qry);
  
      if($this->fileExists())
      {
        unlink($REX['MEDIAFOLDER'].DIRECTORY_SEPARATOR.$this->getFileName());
      }
  
      return $sql->getError();
    }
    return false;
  }
  
  function fileExists($filename = null)
  {
    global $REX;
    
    if($filename === null)
    {
      $filename = $this->getFileName();
    }
    
    return file_exists($REX['MEDIAFOLDER'].DIRECTORY_SEPARATOR.$filename);
  }

  // allowed filetypes
  function getDocTypes()
  {
    static $docTypes = array (
      'bmp',
      'css',
      'doc',
      'docx',
      'eps',
      'gif',
      'gz',
      'jpg',
      'mov',
      'mp3',
      'ogg',
      'pdf',
      'png',
      'ppt',
      'pptx',
      'pps',
      'ppsx',
      'rar',
      'rtf',
      'swf',
      'tar',
      'tif',
      'txt',
      'wma',
      'xls',
      'xlsx',
      'zip'
    );
    return $docTypes;
  }

  function isDocType($type)
  {
    return in_array($type, OOMedia :: getDocTypes());
  }

  // allowed image upload types
  function getImageTypes()
  {
    static $imageTypes = array (
      'image/gif',
      'image/jpg',
      'image/jpeg',
      'image/png',
      'image/x-png',
      'image/pjpeg',
      'image/bmp'
    );
    return $imageTypes;
  }

  function isImageType($type)
  {
    return in_array($type, OOMedia :: getImageTypes());
  }

  function compareImageTypes($type1, $type2)
  {
    static $jpg = array (
      'image/jpg',
      'image/jpeg',
      'image/pjpeg'
    );

    return in_array($type1, $jpg) && in_array($type2, $jpg);
  }

  function hasValue($value)
  {
    if (substr($value, 0, 1) != '_')
    {
      $value = "_".$value;
    }
    return isset($this->$value);
  }

  function getValue($value)
  {
    if (substr($value, 0, 1) != '_')
    {
      $value = "_".$value;
    }

    // damit alte rex_article felder wie copyright, description
    // noch funktionieren
    if($this->hasValue($value))
    {
      return $this->$value;
    }
    elseif ($this->hasValue('med'. $value))
    {
      return $this->getValue('med'. $value);
    }
  }
}