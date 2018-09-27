<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Store page
 */

// Always define page name
define('PAGE', 'store');

// Get variables from cache
$cache->setCache('buycraft_settings');
if($cache->isCached('buycraft_url')){
	$buycraft_url = Output::getClean($cache->retrieve('buycraft_url'));
} else {
	$buycraft_url = '/store';
}
?>
<!DOCTYPE html>
<html<?php if(defined('HTML_CLASS')) echo ' class="' . HTML_CLASS . '"'; ?> lang="<?php echo (defined('HTML_LANG') ? HTML_LANG : 'en'); ?>" <?php if(defined('HTML_RTL') && HTML_RTL === true) echo ' dir="rtl"'; ?>>
<head>
	<!-- Standard Meta -->
	<meta charset="<?php echo (defined('LANG_CHARSET') ? LANG_CHARSET : 'utf-8'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

	<!-- Site Properties -->
	<?php
	$title = $buycraft_language->get('language', 'store');
	require(ROOT_PATH . '/core/templates/header.php');
	?>
	<link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/plugins/spoiler/css/spoiler.css">

</head>
<body>
<?php
require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Retrieve store info from database
$store_url = $queries->getWhere('buycraft_settings', array('name', '=', 'domain'));
if(!count($store_url)){
	die('Please configure and synchronise the Buycraft module in the AdminCP first!');
} else {
	$store_url = Output::getClean($store_url[0]->value);
}

$content = $queries->getWhere('buycraft_settings', array('name', '=', 'store_content'));
$content = Output::getDecoded($content[0]->value);

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

$smarty->assign(array(
	'STORE' => $buycraft_language->get('language', 'store'),
	'STORE_URL' => $store_url,
	'VIEW_FULL_STORE' => $buycraft_language->get('language', 'view_full_store'),
	'HOME' => $buycraft_language->get('language', 'home'),
	'HOME_URL' => $buycraft_url,
	'CATEGORIES' => $categories,
	'CONTENT' => $content
));

$smarty->display(ROOT_PATH . '/custom/templates/' . TEMPLATE . '/buycraft/index.tpl');

require(ROOT_PATH . '/core/templates/scripts.php'); ?>
<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js"></script>

</body>
</html>