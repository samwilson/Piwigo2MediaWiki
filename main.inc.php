<?php
/*
Plugin Name: Piwigo to MediaWiki
Version: 0.1.0
Description: A Piwigo plugin for exporting photos to MediaWiki wikis.
Plugin URI: auto
Author: Sam Wilson
Author URI: https://samwilson.id.au
*/

// Make sure we're already in Piwigo.
defined('PHPWG_ROOT_PATH') or exit(1);

// Define plugin's paths etc.
define('PIWIGO2MEDIAWIKI_ID', 'piwigo2mediawiki');
define('PIWIGO2MEDIAWIKI_PATH', PHPWG_PLUGINS_PATH.PIWIGO2MEDIAWIKI_ID.'/');
define('PIWIGO2MEDIAWIKI_PAGE', 'plugin-'.PIWIGO2MEDIAWIKI_ID);
define(
  'PIWIGO2MEDIAWIKI_ADMIN',
  get_absolute_root_url().'admin.php?page=plugin-'.PIWIGO2MEDIAWIKI_ID
);
define(
  'PIWIGO2MEDIAWIKI_DIR',
  realpath(PHPWG_PLUGINS_PATH.PIWIGO2MEDIAWIKI_ID).'/'
);

// Complain if our plugin directory is not named correctly.
if (basename(dirname(__FILE__)) != PIWIGO2MEDIAWIKI_ID) {
  add_event_handler('init', function () {
    global $page;
    $page['errors'][] = l10n(
      'Plugin folder name is incorrect, please rename %s to %s',
      basename(dirname(__FILE__)),
      PIWIGO2MEDIAWIKI_ID
    );
  });
  return;
}

//Initialise the plugin.
add_event_handler('init', function(){
  global $conf;
  load_language('plugin.lang', PIWIGO2MEDIAWIKI_PATH);
  if (isset($conf['piwigo2mediawiki'])) {
    $conf['piwigo2mediawiki'] = safe_unserialize($conf['piwigo2mediawiki']);
  }
});

// Add event handlers.
if (defined('IN_ADMIN')) {

  // Add the admin menu item.
  add_event_handler('get_admin_plugin_menu_links', function($menu) {
    $menu[] = array(
      'NAME' => l10n('Piwigo to MediaWiki'),
      'URL' => PIWIGO2MEDIAWIKI_ADMIN,
    );
    return $menu;
  });

  // Add the send-to-MediaWiki global action.
  add_event_handler('loc_end_element_set_global',
    function () {
      global $template, $conf;
      $content = $template->assign( PIWIGO2MEDIAWIKI_DIR.'action.tpl' );
      $template->append('element_set_global_plugins_actions',
        array(
          'ID' => PIWIGO2MEDIAWIKI_ID,
          'NAME' => l10n('Copy to MediaWiki'), 'CONTENT' => $content,
        )
      );
    }
  );

}
