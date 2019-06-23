<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Latest purchases widget settings
 */

// Check input
$cache->setCache('buycraft_data');

if(Input::exists()){
	if(Token::check(Input::get('token'))){
		if(isset($_POST['limit']) && $_POST['limit'] > 0)
			$cache->store('purchase_limit', (int)$_POST['limit']);
		else
			$cache->store('purchase_limit', 10);

	} else {
		$errors = array($language->get('general', 'invalid_token'));
	}
}

if($cache->isCached('purchase_limit'))
	$purchase_limit = (int)$cache->retrieve('purchase_limit');
else
	$purchase_limit = 10;

$smarty->assign(array(
	'LATEST_PURCHASES_LIMIT' => $buycraft_language->get('language', 'latest_purchases_limit'),
	'LATEST_PURCHASES_LIMIT_VALUE' => Output::getClean($purchase_limit),
	'INFO' => $language->get('general', 'info'),
	'WIDGET_CACHED' => $buycraft_language->get('language', 'widget_cached'),
	'SETTINGS_TEMPLATE' => 'tebex/widgets/latest_purchases.tpl'
));
