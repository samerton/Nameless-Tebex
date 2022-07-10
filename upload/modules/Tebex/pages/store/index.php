<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Store page
 */

// Always define page name
define('PAGE', 'tebex');
$page_title = $buycraft_language->get('language', 'store');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// Get variables from cache
$cache->setCache('buycraft_settings');
if($cache->isCached('buycraft_url')){
	$buycraft_url = Output::getClean(rtrim($cache->retrieve('buycraft_url'), '/'));
} else {
	$buycraft_url = '/store';
}

// Retrieve store info from database
$store_url = DB::getInstance()->get('buycraft_settings', array('name', '=', 'domain'));
if (!$store_url->count()) {
	die('Please configure and synchronise the Tebex module in the StaffCP first!');
} else {
	$store_url = Output::getClean($store_url->first()->value);
}

// Show home tab?
$home_tab = DB::getInstance()->get('buycraft_settings', array('name', '=', 'home_tab'));

if ($home_tab->count())
    $home_tab = $home_tab->first()->value;
else
    $home_tab = 1;

$content = DB::getInstance()->get('buycraft_settings', array('name', '=', 'store_content'));
if ($content->count()) {
    $content = EventHandler::executeEvent('renderTebexContent', ['content' => $content->first()->value])['content'];
} else {
    $content = '';
}

$categories_query = DB::getInstance()->query('SELECT id, name FROM nl2_buycraft_categories WHERE parent_category IS NULL ORDER BY `order` ASC');
$categories = array();

if ($categories_query->count()) {
	foreach($categories_query->results() as $item) {
		$subcategories_query = DB::getInstance()->query('SELECT id, `name` FROM nl2_buycraft_categories WHERE parent_category = ? ORDER BY `order` ASC', array($item->id))->results();

		$subcategories = array();
		if (count($subcategories_query)) {
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

$template->assets()->include([
    AssetTree::TINYMCE,
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$template->onPageLoad();

$smarty->assign('WIDGETS', $widgets->getWidgets());

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('tebex/index.tpl', $smarty);
