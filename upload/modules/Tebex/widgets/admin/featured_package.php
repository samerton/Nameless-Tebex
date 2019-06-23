<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Featured package widget settings
 */

// Check input
$cache->setCache('buycraft_data');

if(Input::exists()){
	if(Token::check(Input::get('token'))){
		$packages = array();

		if(isset($_POST['featured_packages']) && count($_POST['featured_packages'])){
			foreach($_POST['featured_packages'] as $package){
				$packages[] = intval($package);
			}
		}

		$cache->store('featured_packages', $packages);

	} else {
		$errors = array($language->get('general', 'invalid_token'));
	}
}

if($cache->isCached('featured_packages'))
	$featured_packages = $cache->retrieve('featured_packages');
else
	$featured_packages = array();

$packages = DB::getInstance()->query('SELECT * FROM nl2_buycraft_packages ORDER BY `order` ASC', array());
$template_packages = array();

if($packages->count()){
	$packages = $packages->results();

	foreach($packages as $package){
		$template_packages[] = array(
			'value' => Output::getClean($package->id),
			'name' => Output::getClean($package->name),
			'selected' => in_array($package->id, $featured_packages)
		);
	}
} else
	$smarty->assign('NO_PACKAGES', $buycraft_language->get('language', 'no_packages'));

$smarty->assign(array(
	'INFO' => $language->get('general', 'info'),
	'FEATURED_PACKAGE_INFO' => $buycraft_language->get('language', 'featured_packages_info'),
	'FEATURED_PACKAGES' => $buycraft_language->get('language', 'featured_packages'),
	'SELECT_MULTIPLE_WITH_CTRL' => $buycraft_language->get('language', 'select_multiple_with_ctrl'),
	'PACKAGES' => $template_packages,
	'SETTINGS_TEMPLATE' => 'tebex/widgets/featured_package.tpl'
));