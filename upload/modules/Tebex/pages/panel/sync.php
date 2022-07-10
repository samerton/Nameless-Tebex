<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - admin sync
 */

if (!$user->handlePanelPageLoad('admincp.buycraft')) {
    require_once ROOT_PATH . '/403.php';
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'buycraft');
define('PANEL_PAGE', 'buycraft_sync');
$page_title = $buycraft_language->get('language', 'force_sync');
require_once ROOT_PATH . '/core/templates/backend_init.php';
require_once ROOT_PATH . '/modules/Tebex/classes/Buycraft.php';

$success = [];
$success_debug = [];
$errors = [];

// Get server key
$server_key = DB::getInstance()->get('buycraft_settings', array('name', '=', 'server_key'));

if ($server_key->count()) {
	$server_key = $server_key->first()->value;

	if (strlen($server_key) == 40) {
		$db = DB::getInstance();

		$result = Buycraft::updateInformation($server_key, $db);

		if (!$result || isset($result['error'])) {
			$errors[] = $buycraft_language->get('language', 'unable_to_get_information', ['error' => isset($result['error']) ? Output::getClean($result['error']) : '']);
		} else {
			$success[] = $buycraft_language->get('language', 'information_retrieved_successfully');
            $success_debug[] = json_encode($result, JSON_PRETTY_PRINT);
		}

		$result = Buycraft::updateCommandQueue($server_key, $db);

		if (!$result || isset($result['error'])) {
			$errors[] = $buycraft_language->get('language', 'unable_to_get_command_queue', ['error' => isset($result['error']) ? Output::getClean($result['error']) : '']);
		} else {
			$success[] = $buycraft_language->get('language', 'command_queue_retrieved_successfully');
            $success_debug[] = json_encode($result, JSON_PRETTY_PRINT);
		}

		$result = Buycraft::updateListing($server_key, $db);

		if (!$result || isset($result['error'])) {
			$errors[] = $buycraft_language->get('language', 'unable_to_get_listing', ['error' => isset($result['error']) ? Output::getClean($result['error']) : '']);
		} else {
			$success[] = $buycraft_language->get('language', 'listing_retrieved_successfully');
            $success_debug[] = json_encode($result, JSON_PRETTY_PRINT);
		}

		$result = Buycraft::updatePackages($server_key, $db);

		if (!$result || isset($result['error'])) {
			$errors[] = $buycraft_language->get('language', 'unable_to_get_packages', ['error' => isset($result['error']) ? Output::getClean($result['error']) : '']);
		} else {
			$success[] = $buycraft_language->get('language', 'packages_retrieved_successfully');
            $success_debug[] = json_encode($result, JSON_PRETTY_PRINT);
		}

        $result = Buycraft::updatePayments($server_key, $db);
		if (!$result || isset($result['error'])) {
			$errors[] = $buycraft_language->get('language', 'unable_to_get_payments', ['error' => isset($result['error']) ? Output::getClean($result['error']) : '']);
		} else {
			$success[] = $buycraft_language->get('language', 'payments_retrieved_successfully');
            $success_debug[] = json_encode($result, JSON_PRETTY_PRINT);
		}

		$result = Buycraft::updateGiftCards($server_key, $db);
		if (!$result || isset($result['error'])) {
			$errors[] = $buycraft_language->get('language', 'unable_to_get_gift_cards', ['error' => isset($result['error']) ? Output::getClean($result['error']) : '']);
		} else {
			$success[] = $buycraft_language->get('language', 'gift_cards_retrieved_successfully');
            $success_debug[] = json_encode($result, JSON_PRETTY_PRINT);
		}

		$result = Buycraft::updateCoupons($server_key, $db);
		if (!$result || isset($result['error'])) {
			$errors[] = $buycraft_language->get('language', 'unable_to_get_coupons', ['error' => isset($result['error']) ? Output::getClean($result['error']) : '']);
		} else {
			$success[] = $buycraft_language->get('language', 'coupons_retrieved_successfully');
            $success_debug[] = json_encode($result, JSON_PRETTY_PRINT);
		}

		$result = Buycraft::updateBans($server_key, $db);
		if (!$result || isset($result['error'])) {
			$errors[] = $buycraft_language->get('language', 'unable_to_get_bans', ['error' => isset($result['error']) ? Output::getClean($result['error']) : '']);
		} else {
			$success[] = $buycraft_language->get('language', 'bans_retrieved_successfully');
            $success_debug[] = json_encode($result, JSON_PRETTY_PRINT);
		}

	} else {
		$errors[] = $buycraft_language->get('language', 'invalid_server_key');
	}
} else {
	$errors[] = $buycraft_language->get('language', 'invalid_server_key');
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (isset($success) && count($success))
	$smarty->assign(array(
		'SUCCESS' => $success,
		'SUCCESS_TITLE' => $language->get('general', 'success')
	));

if (isset($errors) && count($errors))
	$smarty->assign(array(
		'ERRORS' => $errors,
		'ERRORS_TITLE' => $language->get('general', 'error')
	));

if (defined('DEBUGGING') && DEBUGGING && isset($success_debug)) {
    $smarty->assign('DEBUG_RESPONSE', $success_debug);
}

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'BUYCRAFT' => $buycraft_language->get('language', 'buycraft'),
	'PAGE' => PANEL_PAGE,
	'FORCE_SYNC' => $buycraft_language->get('language', 'force_sync')
));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('tebex/sync.tpl', $smarty);
