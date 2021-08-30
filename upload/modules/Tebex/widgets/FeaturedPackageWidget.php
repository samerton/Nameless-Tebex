<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Store featured package widget
 */
class FeaturedPackageWidget extends WidgetBase {
	private $_smarty, $_language, $_buycraft_language, $_cache;

	public function __construct($pages, $smarty, $language, $buycraft_language, $cache){
		parent::__construct($pages);

		$this->_smarty = $smarty;
		$this->_language = $language;
		$this->_buycraft_language = $buycraft_language;
		$this->_cache = $cache;

		// Get order
		$order = DB::getInstance()->query('SELECT `order` FROM nl2_widgets WHERE `name` = ?', array('Featured Package'))->first();

		// Set widget variables
		$this->_module = 'Tebex';
		$this->_name = 'Featured Package';
		$this->_location = 'right';
		$this->_description = 'Display a store package to feature across your website';
		$this->_settings = ROOT_PATH . '/modules/Tebex/widgets/admin/featured_package.php';
		$this->_order = $order->order;
	}

	public function initialise(){
		// Generate HTML code for widget
		$this->_cache->setCache('buycraft_data');
		if($this->_cache->isCached('featured_packages'))
			$featured_packages = $this->_cache->retrieve('featured_packages');
		else {
			$this->_content = '';
			return;
		}

		$package = $featured_packages[mt_rand(0, count($featured_packages) - 1)];
		$query = DB::getInstance()->query('SELECT packages.id AS id, packages.category_id AS category_id, packages.name AS name, packages.order AS `order`, packages.price AS price, packages.sale_active AS sale_active, packages.sale_discount AS sale_discount, descriptions.description AS description, descriptions.image AS image FROM nl2_buycraft_packages AS packages LEFT JOIN nl2_buycraft_packages_descriptions AS descriptions ON descriptions.package_id = packages.id WHERE packages.id = ? ORDER BY `order` ASC', array($package));

		if(!$query->count()){
			$this->_content = '';
			return;
		}
		$package = $query->first();
		$query = null;

		// Get variables from cache
		$this->_cache->setCache('buycraft_settings');
		if($this->_cache->isCached('buycraft_url')){
			$buycraft_url = Output::getClean(rtrim($this->_cache->retrieve('buycraft_url'), '/'));
		} else {
			$buycraft_url = '/store';
		}

		$currency = DB::getInstance()->query('SELECT * FROM nl2_buycraft_settings WHERE `name` = \'currency_symbol\'', array());
		$currency = $currency->count() ? Output::getPurified($currency->first()->value) : '$';

		require_once(ROOT_PATH . '/core/includes/emojione/autoload.php'); // Emojione
		$emojione = new Emojione\Client(new Emojione\Ruleset());

		$content = Output::getDecoded($package->description);
		$content = $emojione->unicodeToImage($content);
		$content = Output::getPurified($content);

		$image = (isset($package->image) && !is_null($package->image) ? Output::getClean(Output::getDecoded($package->image)) : null);

		$template_package = array(
			'id' => Output::getClean($package->id),
			'name' => Output::getClean($package->name),
			'price' => Output::getClean($package->price),
			'real_price' => $package->sale_active == 1 ? Output::getClean($package->price - $package->sale_discount) : Output::getClean($package->price),
			'sale_active' => $package->sale_active == 1,
			'sale_discount' => Output::getClean($package->sale_discount),
			'description' => $content,
			'image' => $image,
			'link' => URL::build($buycraft_url . '/category/' . Output::getClean($package->category_id))
		);

		$this->_smarty->assign(array(
			'FEATURED_PACKAGE' => $this->_buycraft_language->get('language', 'featured_package'),
			'PACKAGE' => $template_package,
			'VIEW' => $this->_language->get('general', 'view'),
			'SALE' => $this->_buycraft_language->get('language', 'sale'),
			'CURRENCY' => $currency
		));

		$this->_content = $this->_smarty->fetch('tebex/widgets/featured_package.tpl');
	}
}