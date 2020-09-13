<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Store page - category view
 */

// Always define page name
define('PAGE', 'buycraft');

// Get category ID
$category_id = explode('/', $route);
$category_id = $category_id[count($category_id) - 1];

if(!isset($category_id[count($category_id) - 1])){
	require_once(ROOT_PATH . '/404.php');
	die();
}

$category_id = explode('-', $category_id);
if(!is_numeric($category_id[0])){
	require_once(ROOT_PATH . '/404.php');
	die();
}
$category_id = $category_id[0];

// Query category
$category = DB::getInstance()->query('SELECT categories.id AS id, categories.name AS name, categories.parent_category AS parent_category, descriptions.description AS description, descriptions.image AS image FROM nl2_buycraft_categories AS categories LEFT JOIN nl2_buycraft_categories_descriptions AS descriptions ON descriptions.category_id = categories.id WHERE categories.id = ?', array($category_id));

if(!$category->count()){
	require_once(ROOT_PATH . '/404.php');
	die();
}

$category = $category->first();

// Get variables from cache
$cache->setCache('buycraft_settings');
if($cache->isCached('buycraft_url')){
	$buycraft_url = Output::getClean(rtrim($cache->retrieve('buycraft_url'), '/'));
} else {
	$buycraft_url = '/store';
}

$page_metadata = $queries->getWhere('page_descriptions', array('page', '=', $buycraft_url . '/view'));
if(count($page_metadata)){
	define('PAGE_DESCRIPTION', str_replace(array('{site}', '{category_title}', '{description}'), array(SITE_NAME, Output::getClean($category->name), Output::getClean(strip_tags(Output::getDecoded($category->description)))), $page_metadata[0]->description));
	define('PAGE_KEYWORDS', $page_metadata[0]->tags);
}

$page_title = Output::getClean($category->name);
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

require(ROOT_PATH . '/core/includes/emojione/autoload.php'); // Emojione
$emojione = new Emojione\Client(new Emojione\Ruleset());

// Retrieve store info from database
$store_url = $queries->getWhere('buycraft_settings', array('name', '=', 'domain'));
if(!count($store_url)){
	die('Please configure and synchronise the Tebex module in the StaffCP first!');
} else {
	$store_url = Output::getClean(rtrim($store_url[0]->value, '/'));
}

$currency = $queries->getWhere('buycraft_settings', array('name', '=', 'currency_symbol'));
$currency = Output::getPurified($currency[0]->value);

// Get packages
$packages = DB::getInstance()->query('SELECT packages.id AS id, packages.category_id AS category_id, packages.name AS name, packages.order AS `order`, packages.price AS price, packages.sale_active AS sale_active, packages.sale_discount AS sale_discount, descriptions.description AS description, descriptions.image AS image FROM nl2_buycraft_packages AS packages LEFT JOIN nl2_buycraft_packages_descriptions AS descriptions ON descriptions.package_id = packages.id WHERE packages.category_id = ? ORDER BY `order` ASC', array($category_id));

if(!$packages->count()){
	$smarty->assign('NO_PACKAGES', $buycraft_language->get('language', 'no_packages'));
} else {
	$packages = $packages->results();
	$category_packages = array();

	foreach($packages as $package){
		$content = Output::getDecoded($package->description);
		$content = $emojione->unicodeToImage($content);
		$content = Output::getPurified($content);

		$image = (isset($package->image) && !is_null($package->image) ? (defined('CONFIG_PATH') ? CONFIG_PATH . '/' : '/') . 'uploads/store/' . Output::getClean(Output::getDecoded($package->image)) : null);

		$category_packages[] = array(
			'id' => Output::getClean($package->id),
			'name' => Output::getClean($package->name),
			'price' => Output::getClean($package->price),
			'real_price' => $package->sale_active == 1 ? Output::getClean($package->price - $package->sale_discount) : Output::getClean($package->price),
			'sale_active' => $package->sale_active == 1,
			'sale_discount' => Output::getClean($package->sale_discount),
			'description' => $content,
			'image' => $image,
			'link' => $store_url . '/checkout/packages/add/' . Output::getClean($package->id) . '/single'
		);
	}

	$smarty->assign('PACKAGES', $category_packages);
}

$smarty->assign(array(
	'ACTIVE_CATEGORY' => Output::getClean($category->name),
	'BUY' => $buycraft_language->get('language', 'buy'),
	'CLOSE' => $language->get('general', 'close'),
	'CURRENCY' => $currency,
	'SALE' => $buycraft_language->get('language', 'sale')
));

// Query categories
$categories_query = $queries->orderWhere('buycraft_categories', 'parent_category IS NULL', '`order`', 'ASC');
$categories = array();

if(count($categories_query)){
	foreach($categories_query as $item){
		$subcategories_query = DB::getInstance()->query('SELECT id, `name` FROM nl2_buycraft_categories WHERE parent_category = ? ORDER BY `order` ASC', array($item->id))->results();

		$subcategories = array();
		$active = false;
		if(count($subcategories_query)){
			foreach($subcategories_query as $subcategory){
				$active = Output::getClean($category->name) == Output::getClean($subcategory->name);

				$subcategories[] = array(
					'url' => URL::build($buycraft_url . '/category/' . Output::getClean($subcategory->id)),
					'title' => Output::getClean($subcategory->name),
					'active' => $active
				);
			}
		}

		$categories[$item->id] = array(
			'url' => URL::build($buycraft_url . '/category/' . Output::getClean($item->id)),
			'title' => Output::getClean($item->name),
			'subcategories' => $subcategories,
			'active' => !$active && Output::getClean($category->name) == Output::getClean($item->name)
		);
	}
}

$smarty->assign(array(
	'STORE' => $buycraft_language->get('language', 'store'),
	'STORE_URL' => $store_url,
	'VIEW_FULL_STORE' => $buycraft_language->get('language', 'view_full_store'),
	'HOME' => $buycraft_language->get('language', 'home'),
	'HOME_URL' => URL::build($buycraft_url),
	'CATEGORIES' => $categories,
	'CONTENT' => $content
));

$template->addCSSFiles(array(
	(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/css/spoiler.css' => array(),
	(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/emoji/css/emojione.min.css' => array()
));

$template->addJSFiles(array(
	(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js' => array()
));

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets, $template);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

$smarty->assign('WIDGETS', $widgets->getWidgets());

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('tebex/category.tpl', $smarty);
