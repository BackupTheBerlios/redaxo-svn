<?php

/**
 * Userinfo Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

// zuletzt bearbeitete artikel (metainfos, content, status, version-addon)
// zuletzt bearbeitete editMe datensätze
// zuletzt bearbeitete editMe Datenmodelle
// zuletzt gelaufene cronjobs

class rex_stats_component extends rex_dashboard_component
{
  function rex_stats_component()
  {
    global $I18N;
    
    // default cache lifetime in seconds
    $cache_options['lifetime'] = 1800;
    
    parent::rex_dashboard_component(
      $I18N->msg('userinfo_component_stats_title'),
      '',
      $cache_options
    );
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $stats = rex_a659_statistics();
    
    $content = '';
    $content .= '<span>';
    $content .= $stats['total_articles'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_articles');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_slices'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_slices');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_clangs'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_clangs');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_templates'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_templates');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_modules'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_modules');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_actions'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_actions');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_users'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_users');
    $content .= '<br />';
    
    $this->setContent($content);
  }
}

class rex_articles_component extends rex_dashboard_component
{
  function rex_articles_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_articles_title'));
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $articles = rex_a659_latest_articles();
    
    $content = '';
    
    if(count($articles) > 0)
    {
      $content .= '<ul>';
      foreach($articles as $article)
      {
        $updatedate = rex_formatter::format($article['updatedate'], 'strftime', 'datetime');
        
        $content .= '<li>';
        $content .= '<a href="index.php?page=content&article_id='. $article['id'] .'&mode=edit&clang='. $article['clang'] .'">'. $article['name'] .'</a>';
        $content .= ' ['. $I18N->msg('userinfo_component_userinfo', $article['updateuser'], $updatedate). ']';
        $content .= '</li>';
      }
      $content .= '</ul>';
    }
    
    $this->setContent($content);
  }
}

class rex_templates_component extends rex_dashboard_component
{
  function rex_templates_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_templates_title'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }

  /*protected*/ function prepare()
  {
    global $I18N;
    
    $templates = rex_a659_latest_templates();
    
    $content = '';
    if(count($templates) > 0)
    {
      $content .= '<ul>';
      foreach($templates as $template)
      {
        $updatedate = rex_formatter::format($template['updatedate'], 'strftime', 'datetime');
        
        $content .= '<li>';
        $content .= '<a href="index.php?page=template&function=edit&template_id='. $template['id'] .'">'. $template['name'] .'</a>';
        $content .= ' ['. $I18N->msg('userinfo_component_userinfo', $template['updateuser'], $updatedate). ']';
        $content .= '</li>';
      }
      $content .= '</ul>';
    }
          
    $this->setContent($content);
  }
}

class rex_modules_component extends rex_dashboard_component
{
  function rex_modules_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_modules_title'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $modules = rex_a659_latest_modules();
    
    $content = '';
    if(count($modules) > 0)
    {
      $content .= '<ul>';
      foreach($modules as $module)
      {
        $updatedate = rex_formatter::format($module['updatedate'], 'strftime', 'datetime');
        
        $content .= '<li>';
        $content .= '<a href="index.php?page=module&function=edit&modul_id='. $module['id'] .'">'. $module['name'] .'</a>';
        $content .= ' ['. $I18N->msg('userinfo_component_userinfo', $module['updateuser'], $updatedate). ']';
        $content .= '</li>';
      }
      $content .= '</ul>';
    }
          
    $this->setContent($content);
  }
}

class rex_actions_component extends rex_dashboard_component
{
  function rex_actions_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_actions_title'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $actions = rex_a659_latest_actions();
    
    $content = '';
    if(count($actions) > 0)
    {
      $content .= '<ul>';
      foreach($actions as $action)
      {
        $updatedate = rex_formatter::format($action['updatedate'], 'strftime', 'datetime');

        $content .= '<li>';
        $content .= '<a href="index.php?page=module&subpage=actions&function=edit&action_id='. $action['id'] .'">'. $action['name'] .'</a>';
        $content .= ' ['. $I18N->msg('userinfo_component_userinfo', $action['updateuser'], $updatedate). ']';
        $content .= '</li>';
      }
      $content .= '</ul>';
    }
          
    $this->setContent($content);
  }
}

class rex_users_component extends rex_dashboard_component
{
  function rex_users_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_users_title'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }
    
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $users = rex_a659_latest_users();
    
    $content = '';
    if(count($users) > 0)
    {
      $content .= '<ul>';
      foreach($users as $user)
      {
        $updatedate = rex_formatter::format($user['updatedate'], 'strftime', 'datetime');
        
        $content .= '<li>';
        $content .= '<a href="index.php?page=user&user_id='. $user['user_id'] .'">'. $user['name'] .'</a>';
        $content .= ' ['. $I18N->msg('userinfo_component_userinfo', $user['updateuser'], $updatedate). ']';
        $content .= '</li>';
      }
      $content .= '</ul>';
    }
          
    $this->setContent($content);
  }
}

class rex_media_component extends rex_dashboard_component
{
  function rex_media_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_media_title'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->hasMediaPerm();
  }

  /*protected*/ function prepare()
  {
    global $I18N;
    
    $media = rex_a659_latest_media();
    
    $content = '';
    if(count($media) > 0)
    {
      $content .= '<ul>';
      foreach($media as $medium)
      {
        $url = 'index.php?page=mediapool&subpage=detail&file_id='. $medium['file_id'];
        $updatedate = rex_formatter::format($medium['updatedate'], 'strftime', 'datetime');
        
        $content .= '<li>';
        $content .= '<a href="'. $url .'" onclick="newPoolWindow(this.href); return false;">'. $medium['filename'] .'</a>';
        $content .= ' ['. $I18N->msg('userinfo_component_userinfo', $medium['updateuser'], $updatedate). ']';
        $content .= '</li>';
      }
      $content .= '</ul>';
    }
          
    $this->setContent($content);
  }
}
