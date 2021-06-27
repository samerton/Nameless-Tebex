<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC
 */

// Can the user view the AdminCP?
if($user->isLoggedIn()){
	if(!$user->canViewStaffCP()){
		// No
		Redirect::to(URL::build('/'));
		die();
	} else {
		// Check the user has re-authenticated
		if(!$user->isAdmLoggedIn()){
			// They haven't, do so now
			Redirect::to(URL::build('/panel/auth'));
			die();
		} else {
			if(!$user->hasPermission('admincp.buycraft.settings')){
				Redirect::to(URL::build('/panel'));
				die();
			}
		}
	}
} else {
	// Not logged in
	Redirect::to(URL::build('/login'));
	die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'buycraft');
define('PANEL_PAGE', 'buycraft');
$page_title = $buycraft_language->get('language', 'buycraft');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

if(isset($_POST) && !empty($_POST)){
	$errors = array();

	if(Token::check(Input::get('token'))){
		$validate = new Validate();

		$validation = $validate->check($_POST, array(
			'server_key' => array(
				'min' => 40,
				'max' => 40
			),
			'store_content' => array(
				'max' => 100000
			)
		));

		if($validation->passed()){
			if(isset($_POST['allow_guests']) && $_POST['allow_guests'] == 'on')
				$allow_guests = 1;
			else
				$allow_guests = 0;

            if(isset($_POST['home_tab']) && $_POST['home_tab'] == 'on')
                $home_tab = 1;
            else
                $home_tab = 0;

			try {
				$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

				if(count($server_key)){
					$server_key = $server_key[0]->id;
					$queries->update('buycraft_settings', $server_key, array(
						'value' => Output::getClean(Input::get('server_key'))
					));
				} else {
					$queries->create('buycraft_settings', array(
						'name' => 'server_key',
						'value' => Output::getClean(Input::get('server_key'))
					));
				}

			} catch(Exception $e){
				$errors[] = $e->getMessage();
			}

			try {
				$allow_guests_query = $queries->getWhere('buycraft_settings', array('name', '=', 'allow_guests'));

				if(count($allow_guests_query)){
					$allow_guests_query = $allow_guests_query[0]->id;
					$queries->update('buycraft_settings', $allow_guests_query, array(
						'value' => $allow_guests
					));
				} else {
					$queries->create('buycraft_settings', array(
						'name' => 'allow_guests',
						'value' => $allow_guests
					));
				}

			} catch(Exception $e){
				$errors[] = $e->getMessage();
			}

            try {
                $home_tab_query = $queries->getWhere('buycraft_settings', array('name', '=', 'home_tab'));

                if(count($home_tab_query)){
                    $home_tab_query = $home_tab_query[0]->id;
                    $queries->update('buycraft_settings', $home_tab_query, array(
                        'value' => $home_tab
                    ));
                } else {
                    $queries->create('buycraft_settings', array(
                        'name' => 'home_tab',
                        'value' => $home_tab
                    ));
                }

            } catch(Exception $e){
                $errors[] = $e->getMessage();
            }

			try {
				$store_index_content = $queries->getWhere('buycraft_settings', array('name', '=', 'store_content'));

				if(count($store_index_content)){
					$store_index_content = $store_index_content[0]->id;
					$queries->update('buycraft_settings', $store_index_content, array(
						'value' => Output::getClean(Input::get('store_content'))
					));
				} else {
					$queries->create('buycraft_settings', array(
						'name' => 'store_content',
						'value' => Output::getClean(Input::get('store_content'))
					));
				}

			} catch(Exception $e){
				$errors[] = $e->getMessage();
			}

			try {
				$store_path = $queries->getWhere('buycraft_settings', array('name', '=', 'store_path'));

				if(isset($_POST['store_path']) && strlen(str_replace(' ', '', $_POST['store_path'])) > 0)
					$store_path_input = rtrim(Output::getClean($_POST['store_path']), '/');
				else
					$store_path_input = '/store';

				if(count($store_path)){
					$store_path = $store_path[0]->id;
					$queries->update('buycraft_settings', $store_path, array(
						'value' => $store_path_input
					));
				} else {
					$queries->create('buycraft_settings', array(
						'name' => 'store_path',
						'value' => $store_path_input
					));
				}

				$cache->setCache('buycraft_settings');
				$cache->store('buycraft_url', $store_path_input);

			} catch(Exception $e){
				$errors[] = $e->getMessage();
			}

			if(!count($errors))
				$success = $buycraft_language->get('language', 'updated_successfully');

		} else {
			foreach($validation->errors() as $error){
				if(strpos($error, 'server_key') !== false)
					$errors[] = $buycraft_language->get('language', 'invalid_server_key');
				else
					$errors[] = $buycraft_language->get('language', 'store_content_max');
			}
		}

	} else
		$errors[] = $language->get('general', 'invalid_token');
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

if(isset($success))
	$smarty->assign(array(
		'SUCCESS' => $success,
		'SUCCESS_TITLE' => $language->get('general', 'success')
	));

if(isset($errors) && count($errors))
	$smarty->assign(array(
		'ERRORS' => $errors,
		'ERRORS_TITLE' => $language->get('general', 'error')
	));

$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

if(count($server_key))
	$server_key = Output::getClean($server_key[0]->value);
else
	$server_key = '';

$allow_guests = $queries->getWhere('buycraft_settings', array('name', '=', 'allow_guests'));

if(count($allow_guests))
	$allow_guests = $allow_guests[0]->value;
else
	$allow_guests = 0;

$home_tab = $queries->getWhere('buycraft_settings', array('name', '=', 'home_tab'));

if(count($home_tab))
    $home_tab = $home_tab[0]->value;
else
    $home_tab = 1;

$store_index_content = $queries->getWhere('buycraft_settings', array('name', '=', 'store_content'));
if(count($store_index_content)){
	$store_index_content = Output::getClean(Output::getPurified(Output::getDecoded($store_index_content[0]->value)));
} else {
	$store_index_content = '';
}

$store_path = $queries->getWhere('buycraft_settings', array('name', '=', 'store_path'));
if(count($store_path)){
	$store_path = Output::getClean($store_path[0]->value);
} else {
	$store_path = '/store';
}

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'BUYCRAFT' => $buycraft_language->get('language', 'buycraft'),
	'PAGE' => PANEL_PAGE,
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit'),
	'SETTINGS' => $buycraft_language->get('language', 'settings'),
	'INFO' => $language->get('general', 'info'),
	'SERVER_KEY' => $buycraft_language->get('language', 'server_key'),
	'SERVER_KEY_INFO' => $buycraft_language->get('language', 'server_key_info'),
	'SERVER_KEY_VALUE' => $server_key,
	'ALLOW_GUESTS' => $buycraft_language->get('language', 'allow_guests'),
	'ALLOW_GUESTS_VALUE' => ($allow_guests == 1),
	'HOME_TAB' => $buycraft_language->get('language', 'show_home_tab'),
	'HOME_TAB_VALUE' => ($home_tab == 1),
	'STORE_INDEX_CONTENT' => $buycraft_language->get('language', 'store_index_content'),
	'STORE_INDEX_CONTENT_VALUE' => $store_index_content,
	'STORE_PATH' => $buycraft_language->get('language', 'store_path'),
	'STORE_PATH_VALUE' => $store_path
));

if(!defined('TEMPLATE_BUYCRAFT_SUPPORT')){
	$template->addCSSFiles(array(
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.css' => array()
	));

	$template->addJSFiles(array(
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/emoji/js/emojione.min.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/ckeditor.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/emojione/dialogs/emojione.json' => array()
	));

	$template->addJSScript(Input::createEditor('inputStoreContent', true));
	$template->addJSScript('
	var elems = Array.prototype.slice.call(document.querySelectorAll(\'.js-switch\'));

	elems.forEach(function(html) {
	  var switchery = new Switchery(html, {color: \'#23923d\', secondaryColor: \'#e56464\'});
	});
	');
}

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('tebex/index.tpl', $smarty);