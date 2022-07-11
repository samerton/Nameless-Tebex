<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC
 */

if (!$user->handlePanelPageLoad('admincp.buycraft.settings')) {
    require_once ROOT_PATH . '/403.php';
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'buycraft');
define('PANEL_PAGE', 'buycraft');
$page_title = $buycraft_language->get('language', 'buycraft');
require_once ROOT_PATH . '/core/templates/backend_init.php';

$db = DB::getInstance();

if (isset($_POST) && !empty($_POST)) {
	$errors = [];

	if (Token::check(Input::get('token'))) {
		$validation = Validate::check($_POST, [
			'server_key' => [
				Validate::MIN => 40,
				Validate::MAX => 40
			],
			'store_content' => [
				Validate::MAX => 100000
			],
		])->messages([
            'server_key' => [
                Validate::MIN => $buycraft_language->get('language', 'invalid_server_key'),
                Validate::MAX => $buycraft_language->get('language', 'invalid_server_key'),
            ],
            'store_content' => [
                Validate::MAX => $buycraft_language->get('language', 'store_content_max', [100000]),
            ],
        ]);

		if ($validation->passed()) {
			if (isset($_POST['allow_guests']) && $_POST['allow_guests'] == 'on')
				$allow_guests = 1;
			else
				$allow_guests = 0;

            if (isset($_POST['home_tab']) && $_POST['home_tab'] == 'on')
                $home_tab = 1;
            else
                $home_tab = 0;

			try {

				$server_key = $db->get('buycraft_settings', array('name', '=', 'server_key'));

				if ($server_key->count()) {
					$server_key = $server_key->first()->id;
					$db->update('buycraft_settings', $server_key, array(
						'value' => Output::getClean(Input::get('server_key'))
					));
				} else {
					$db->insert('buycraft_settings', array(
						'name' => 'server_key',
						'value' => Output::getClean(Input::get('server_key'))
					));
				}

			} catch(Exception $e){
				$errors[] = $e->getMessage();
			}

			try {
				$allow_guests_query = $db->get('buycraft_settings', array('name', '=', 'allow_guests'));

				if ($allow_guests_query->count()) {
					$allow_guests_query = $allow_guests_query->first()->id;
					$db->update('buycraft_settings', $allow_guests_query, array(
						'value' => $allow_guests
					));
				} else {
					$db->insert('buycraft_settings', array(
						'name' => 'allow_guests',
						'value' => $allow_guests
					));
				}

			} catch(Exception $e){
				$errors[] = $e->getMessage();
			}

            try {
                $home_tab_query = $db->get('buycraft_settings', array('name', '=', 'home_tab'));

                if ($home_tab_query->count()) {
                    $home_tab_query = $home_tab_query->first()->id;
                    $db->update('buycraft_settings', $home_tab_query, array(
                        'value' => $home_tab
                    ));
                } else {
                    $db->insert('buycraft_settings', array(
                        'name' => 'home_tab',
                        'value' => $home_tab
                    ));
                }

            } catch(Exception $e){
                $errors[] = $e->getMessage();
            }

			try {
				$store_index_content = $db->get('buycraft_settings', array('name', '=', 'store_content'));

				if ($store_index_content->count()) {
					$store_index_content = $store_index_content->first()->id;
					$db->update('buycraft_settings', $store_index_content, array(
						'value' => Input::get('store_content')
					));
				} else {
					$db->insert('buycraft_settings', array(
						'name' => 'store_content',
						'value' => Input::get('store_content')
					));
				}

			} catch(Exception $e){
				$errors[] = $e->getMessage();
			}

			try {
				$store_path = $db->get('buycraft_settings', array('name', '=', 'store_path'));

				if(isset($_POST['store_path']) && strlen(str_replace(' ', '', $_POST['store_path'])) > 0)
					$store_path_input = rtrim(Output::getClean($_POST['store_path']), '/');
				else
					$store_path_input = '/store';

				if ($store_path->count()) {
					$store_path = $store_path->first()->id;
					$db->update('buycraft_settings', $store_path, array(
						'value' => $store_path_input
					));
				} else {
					$db->insert('buycraft_settings', array(
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
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

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

$server_key = $db->get('buycraft_settings', array('name', '=', 'server_key'));

if ($server_key->count())
	$server_key = Output::getClean($server_key->first()->value);
else
	$server_key = '';

$allow_guests = $db->get('buycraft_settings', array('name', '=', 'allow_guests'));

if ($allow_guests->count())
	$allow_guests = $allow_guests->first()->value;
else
	$allow_guests = 0;

$home_tab = $db->get('buycraft_settings', array('name', '=', 'home_tab'));

if ($home_tab->count())
    $home_tab = $home_tab->first()->value;
else
    $home_tab = 1;

$store_index_content = $db->get('buycraft_settings', array('name', '=', 'store_content'));

if ($store_index_content->count()) {
	$store_index_content = $store_index_content->first()->value;
} else {
	$store_index_content = '';
}

$store_index_content = EventHandler::executeEvent('renderTebexContentEdit', ['content' => $store_index_content])['content'];

$store_path = $db->get('buycraft_settings', array('name', '=', 'store_path'));
if ($store_path->count()) {
	$store_path = Output::getClean($store_path->first()->value);
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
	'SERVER_KEY_INFO' => $buycraft_language->get('language', 'server_key_info', ['linkStart' => '<a href=&quot;https://server.tebex.io/game-servers&quot; rel=&quot;nofollow&quot; target=&quot;_blank&quot;>', 'linkEnd' => '</a>']),
	'SERVER_KEY_VALUE' => $server_key,
	'ALLOW_GUESTS' => $buycraft_language->get('language', 'allow_guests'),
	'ALLOW_GUESTS_VALUE' => ($allow_guests == 1),
	'HOME_TAB' => $buycraft_language->get('language', 'show_home_tab'),
	'HOME_TAB_VALUE' => ($home_tab == 1),
	'STORE_INDEX_CONTENT' => $buycraft_language->get('language', 'store_index_content'),
	'STORE_PATH' => $buycraft_language->get('language', 'store_path'),
	'STORE_PATH_VALUE' => $store_path
));

if (!defined('TEMPLATE_BUYCRAFT_SUPPORT')) {
    $template->assets()->include([
        AssetTree::TINYMCE,
    ]);

    $template->addJSScript(Input::createTinyEditor($language, 'inputStoreContent', $store_index_content));
}

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('tebex/index.tpl', $smarty);
