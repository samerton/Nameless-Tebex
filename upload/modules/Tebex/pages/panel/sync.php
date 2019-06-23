<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - admin sync
 */

// Can the user view the AdminCP?
if($user->isLoggedIn()){
	if(!$user->canViewACP()){
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
			if(!$user->hasPermission('admincp.buycraft')){
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
define('PANEL_PAGE', 'buycraft_sync');
$page_title = $buycraft_language->get('language', 'force_sync');
require_once(ROOT_PATH . '/core/templates/backend_init.php');
require_once(ROOT_PATH . '/modules/Tebex/classes/Buycraft.php');

$success = array();
$errors = array();

// Get server key
$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

if(count($server_key)){
	$server_key = $server_key[0]->value;

	if(strlen($server_key) == 40){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$db = DB::getInstance();

		$result = Buycraft::updateInformation($server_key, $ch, $db);

		if(!$result || isset($result->error_code)){
			$errors[] = str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_information'));
		} else {
			$success[] = $buycraft_language->get('language', 'information_retrieved_successfully');
		}

		$result = Buycraft::updateCommandQueue($server_key, $ch, $db);

		if(!$result || isset($result->error_code)){
			$errors[] = str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_command_queue'));
		} else {
			$success[] = $buycraft_language->get('language', 'command_queue_retrieved_successfully');
		}

		$result = Buycraft::updateListing($server_key, $ch, $db);

		if(!$result || isset($result->error_code)){
			$errors[] = str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_listing'));
		} else {
			$success[] = $buycraft_language->get('language', 'listing_retrieved_successfully');
		}

		$result = Buycraft::updatePayments($server_key, $ch, $db);
		if(!$result || isset($result->error_code)){
			$errors[] = str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_payments'));
		} else {
			$success[] = $buycraft_language->get('language', 'payments_retrieved_successfully');
		}

		$result = Buycraft::updateGiftCards($server_key, $ch, $db);
		if(!$result || isset($result->error_code)){
			$errors[] = str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_gift_cards'));
		} else {
			$success[] = $buycraft_language->get('language', 'gift_cards_retrieved_successfully');
		}

		$result = Buycraft::updateCoupons($server_key, $ch, $db);
		if(!$result || isset($result->error_code)){
			$errors[] = str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_coupons'));
		} else {
			$success[] = $buycraft_language->get('language', 'coupons_retrieved_successfully');
		}

		$result = Buycraft::updateBans($server_key, $ch, $db);
		if(!$result || isset($result->error_code)){
			$errors[] = str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_bans'));
		} else {
			$success[] = $buycraft_language->get('language', 'bans_retrieved_successfully');
		}

		curl_close($ch);

	} else {
		$errors[] = $buycraft_language->get('language', 'invalid_server_key');
	}
} else {
	$errors[] = $buycraft_language->get('language', 'invalid_server_key');
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

if(isset($success) && count($success))
	$smarty->assign(array(
		'SUCCESS' => $success,
		'SUCCESS_TITLE' => $language->get('general', 'success')
	));

if(isset($errors) && count($errors))
	$smarty->assign(array(
		'ERRORS' => $errors,
		'ERRORS_TITLE' => $language->get('general', 'error')
	));

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'BUYCRAFT' => $buycraft_language->get('language', 'buycraft'),
	'PAGE' => PANEL_PAGE,
	'FORCE_SYNC' => $buycraft_language->get('language', 'force_sync')
));

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('tebex/sync.tpl', $smarty);