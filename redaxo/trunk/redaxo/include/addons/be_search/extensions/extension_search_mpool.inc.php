<?php

/**
 * Backend Search Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id: extension_search_mpool.inc.php,v 1.10 2008/03/26 21:06:37 kills Exp $
 */

function rex_a256_search_mpool($params)
{
  global $I18N_BE_SEARCH, $REX_USER;

  if(!($REX_USER->isAdmin() || $REX_USER->hasPerm('be_search[medienpool]')))
  {
    return $params['subject'];
  }

  if(rex_request('subpage', 'string') != '') return $params['subject'];
  $media_name = rex_request('a256_media_name', 'string');

  $subject = $params['subject'];

  $search_form = '

    <label class="rex-hide" for="a256_media_name">'. $I18N_BE_SEARCH->msg('search_mpool_media') .'</label>
    <input type="text" name="a256_media_name" id="a256_media_name" value="'. $media_name .'" />
  ';

  $subject = str_replace('</select>', '</select>'. $search_form, $subject);

  return $subject;
}

function rex_a256_search_mpool_query($params)
{
  global $REX, $REX_USER;

  if(!($REX_USER->isAdmin() || $REX_USER->hasPerm('be_search[medienpool]')))
  {
    return $params['subject'];
  }

  $media_name = rex_request('a256_media_name', 'string');
  if($media_name == '') return $params['subject'];

  $qry = $params['subject'];
  $category_id = $params['category_id'];

  $where = " f.category_id = c.id AND (f.filename LIKE '%". $media_name ."%' OR f.title LIKE '%". $media_name ."%')";
  switch(OOAddon::getProperty('be_search', 'searchmode', 'local'))
  {
    case 'local':
    {
      // Suche auf aktuellen Kontext eingrenzen
      if($category_id != 0)
        $where .=" AND (c.path LIKE '%|". $params['category_id'] ."|%' OR c.id=". $params['category_id'] .") ";
    }
  }

  $qry = str_replace('FROM ', 'FROM '. $REX['TABLE_PREFIX'] .'file_category c,', $qry);
  $qry = str_replace('WHERE ', 'WHERE '. $where .' AND ', $qry);

  return $qry;
}

?>