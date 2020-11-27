<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Buycraft sync script
 */

// Check cache
$cache->setCache('buycraft_cache');
if(!$cache->isCached('last_sync')){
    // Query
	$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

	if(count($server_key)){
		$server_key = $server_key[0]->value;

		if(strlen($server_key) == 40){
			require_once(ROOT_PATH . '/modules/Tebex/classes/Buycraft.php');

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$db = DB::getInstance();

			Buycraft::updateInformation($server_key, $ch, $db);
			Buycraft::updateCommandQueue($server_key, $ch, $db);
			Buycraft::updateListing($server_key, $ch, $db);
			Buycraft::updatePackages($server_key, $ch, $db);
			Buycraft::updatePayments($server_key, $ch, $db);
			Buycraft::updateGiftCards($server_key, $ch, $db);
			Buycraft::updateCoupons($server_key, $ch, $db);
			Buycraft::updateBans($server_key, $ch, $db);

			curl_close($ch);

			// 5 minute cache
			$cache->store('last_sync', date('U'), 300);
		}
	}
}

die('Complete');