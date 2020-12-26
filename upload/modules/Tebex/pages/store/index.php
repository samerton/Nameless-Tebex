<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Store page
 */

// Always define page name
define('PAGE', 'buycraft');
$page_title = $buycraft_language->get('language', 'store');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

require(ROOT_PATH . '/core/includes/emojione/autoload.php'); // Emojione
$emojione = new Emojione\Client(new Emojione\Ruleset());

// Get variables from cache
$cache->setCache('buycraft_settings');
if($cache->isCached('buycraft_url')){
	$buycraft_url = Output::getClean(rtrim($cache->retrieve('buycraft_url'), '/'));
} else {
	$buycraft_url = '/store';
}

// Retrieve store info from database
$store_url = $queries->getWhere('buycraft_settings', array('name', '=', 'domain'));
if(!count($store_url)){
	die('Please configure and synchronise the Tebex module in the StaffCP first!');
} else {
	$store_url = Output::getClean($store_url[0]->value);
}

// Show home tab?
$home_tab = $queries->getWhere('buycraft_settings', array('name', '=', 'home_tab'));

if(count($home_tab))
    $home_tab = $home_tab[0]->value;
else
    $home_tab = 1;

$content = $queries->getWhere('buycraft_settings', array('name', '=', 'store_content'));
$content = Output::getDecoded($content[0]->value);
$content = $emojione->unicodeToImage($content);
$content = Output::getPurified($content);

$categories_query = $queries->orderWhere('buycraft_categories', 'parent_category IS NULL', '`order`', 'ASC');
$categories = array();

if(count($categories_query)){
	foreach($categories_query as $item){
		$subcategories_query = DB::getInstance()->query('SELECT id, `name` FROM nl2_buycraft_categories WHERE parent_category = ? ORDER BY `order` ASC', array($item->id))->results();

		$subcategories = array();
		if(count($subcategories_query)){
			foreach($subcategories_query as $subcategory){
				$subcategories[] = array(
					'url' => URL::build($buycraft_url . '/category/' . Output::getClean($subcategory->id)),
					'title' => Output::getClean($subcategory->name)
				);
			}
		}

		$categories[$item->id] = array(
			'url' => URL::build($buycraft_url . '/category/' . Output::getClean($item->id)),
			'title' => Output::getClean($item->name),
			'subcategories' => $subcategories
		);
	}
}

if ($home_tab == 0) {
    Redirect::to($categories[current(array_keys($categories))]['url']);
    die();
}

$smarty->assign(array(
	'STORE' => $buycraft_language->get('language', 'store'),
	'STORE_URL' => $store_url,
	'VIEW_FULL_STORE' => $buycraft_language->get('language', 'view_full_store'),
	'SHOW_HOME_TAB' => $home_tab,
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
$template->displayTemplate('tebex/index.tpl', $smarty);
