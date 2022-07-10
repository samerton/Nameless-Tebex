<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Buycraft sync script
 */

// Check cache
$cache->setCache('buycraft_cache');
if (!$cache->isCached('last_sync')) {
    // Query
    $db = DB::getInstance();
	$server_key = $db->get('buycraft_settings', array('name', '=', 'server_key'));

	if ($server_key->count()) {
		$server_key = $server_key->first()->value;

		if (strlen($server_key) == 40) {
			require_once ROOT_PATH . '/modules/Tebex/classes/Buycraft.php';

			Buycraft::updateInformation($server_key, $db);
			Buycraft::updateCommandQueue($server_key, $db);
			Buycraft::updateListing($server_key, $db);
			Buycraft::updatePackages($server_key, $db);
			Buycraft::updatePayments($server_key, $db);
			Buycraft::updateGiftCards($server_key, $db);
			Buycraft::updateCoupons($server_key, $db);
			Buycraft::updateBans($server_key, $db);

			// 5 minute cache
			$cache->store('last_sync', date('U'), 300);
		}
	}
}

die('Complete');
