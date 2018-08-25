<?php

// Make sure we're already in Piwigo.
defined('PHPWG_ROOT_PATH') or exit(1);

require_once __DIR__.'/vendor/autoload.php';

use Mediawiki\Api\ApiUser;
use Mediawiki\Api\FluentRequest;
use Mediawiki\Api\MediawikiApi;
use Mediawiki\Api\UsageException;

// Prepare the template.
$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
$p2mConf = isset($conf[PIWIGO2MEDIAWIKI_ID])
? $conf[PIWIGO2MEDIAWIKI_ID]
: array();
$template->assign(array(
  'admin_url' => PIWIGO2MEDIAWIKI_ADMIN,
  'piwigo2mediawiki_page' => PIWIGO2MEDIAWIKI_PAGE,
  'mediawiki_url' => $url,
  'p2m_conf' => $p2mConf,
));

// Delete if requested.
if (isset($_POST['action']) && $_POST['action']==='delete'
  && isset($_POST['id']) && isset($p2mConf[$_POST['id']])
) {
  unset($p2mConf[$_POST['id']]);
  conf_update_param(PIWIGO2MEDIAWIKI_ID, $p2mConf);
  redirect(PIWIGO2MEDIAWIKI_ADMIN);
}

// Load one wiki's data for editing if an ID is specified.
if (isset($_REQUEST['id']) && isset($p2mConf[$_REQUEST['id']])) {
  $info = $p2mConf[$_REQUEST['id']];
  $info['id'] = $_REQUEST['id'];
  $template->assign(array(
    'info' => $info,
  ));
}

// Save (create or edit) a single wiki's data.
if ($url && isset($_POST['action']) && $_POST['action'] === 'add'
  && isset($_POST['username']) && $_POST['username']
  && isset($_POST['password']) && $_POST['password']
) {
  // Find the API URL or show an error.
  $validUrl = true;
  try {
    $api = MediawikiApi::newFromPage($url);
    $url = $api->getApiUrl();
  } catch (Exception $exception) {
    $msg = l10n(
      'MediaWiki API discovery failed for: %s<br />Error: %s',
      $url,
      $exception->getMessage()
    );
    $template->assign(array(
      'warnings' => $msg,
      'info' => $_POST,
    ));
    $validUrl = false;
  }
  // If we've got a valid API URL, find the site name and save all data.
  if ($validUrl) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $loggedIn = false;
    try
    {
      $loggedIn = $api->login(new ApiUser($username, $password));
    } catch (UsageException $e) {
      $msg = l10n('Authentication failed.<br />Error: %s', $e->getMessage());
      $template->assign(array(
        'warnings' => $msg,
        'info' => $_POST,
      ));
    }

    // If logging in worked, get some more information and save the config.
    if ($loggedIn)
    {
      $siteInfo = $api->getRequest(
        FluentRequest::factory()
          ->setAction('query')
          ->setParam('meta', 'siteinfo')
      );
      $p2mConf[$info['id']] = array(
        'sitename' => $siteInfo['query']['general']['sitename'],
        'url' => $url,
        'username' => $username,
        'password' => $password,
        'wikitext' => isset($_POST['wikitext']) ? $_POST['wikitext'] : '',
      );
      conf_update_param(PIWIGO2MEDIAWIKI_ID, $p2mConf);
      redirect(PIWIGO2MEDIAWIKI_ADMIN);
    }
  }

}

$template_handle = PIWIGO2MEDIAWIKI_ID.'admin';
$template_file = PIWIGO2MEDIAWIKI_DIR.'admin.tpl';
$template->set_filename($template_handle, $template_file);
$template->assign_var_from_handle('ADMIN_CONTENT', $template_handle);
