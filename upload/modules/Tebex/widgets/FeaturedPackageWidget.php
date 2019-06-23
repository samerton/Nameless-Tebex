<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Store featured package widget
 */
class FeaturedPackageWidget extends WidgetBase {
	private $_smarty, $_language;

	public function __construct($pages, $smarty, $language){
		parent::__construct($pages);

		$this->_smarty = $smarty;
		$this->_language = $language;

		// Get order
		$order = DB::getInstance()->query('SELECT `order` FROM nl2_widgets WHERE `name` = ?', array('Featured Package'))->first();

		// Set widget variables
		$this->_module = 'Buycraft';
		$this->_name = 'Featured Package';
		$this->_location = 'right';
		$this->_description = 'Display a store package to feature across your website';
		$this->_settings = ROOT_PATH . '/modules/Buycraft/widgets/admin/featured_package.php';
		$this->_order = $order->order;
	}

	public function initialise(){
		// Generate HTML code for widget
		$package = '';

		$this->_smarty->assign(array(
			'FEATURED_PACKAGE' => $this->_language->get('language', 'featured_package'),
			'PACKAGE' => $package
		));

		$this->_content = $this->_smarty->fetch('buycraft/widgets/featured_package.tpl');
	}
}