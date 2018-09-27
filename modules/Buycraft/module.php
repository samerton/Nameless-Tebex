<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr5
 *
 *  License: MIT
 *
 *  Buycraft module file
 */

class Buycraft_Module extends Module {
	private $_buycraft_language, $_language, $_cache, $_buycraft_url;

	public function __construct($language, $buycraft_language, $pages, $cache){
		$this->_language = $language;
		$this->_buycraft_language = $buycraft_language;
		$this->_cache = $cache;

		$name = 'Buycraft';
		$author = '<a href="https://samerton.me" target="_blank" rel="nofollow noopener">Samerton</a>';
		$module_version = '1.0.0';
		$nameless_version = '2.0.0-pr5';

		parent::__construct($this, $name, $author, $module_version, $nameless_version);

		// Get variables from cache
		$cache->setCache('buycraft_settings');
		if($cache->isCached('buycraft_url')){
			$this->_buycraft_url = Output::getClean($cache->retrieve('buycraft_url'));
		} else {
			$this->_buycraft_url = '/store';
		}

		// Pages
		$pages->add('Buycraft', $this->_buycraft_url, 'pages/store/index.php', 'store', true);
		$pages->add('Buycraft', $this->_buycraft_url . '/category', 'pages/store/category.php', 'package', true);
		$pages->add('Buycraft', $this->_buycraft_url . '/package', 'pages/store/package.php', 'category', true);
		$pages->add('Buycraft', '/admin/buycraft', 'pages/admin/buycraft.php');
		$pages->add('Buycraft', '/admin/buycraft/sync', 'pages/admin/sync.php');
		$pages->add('Buycraft', '/queries/sync', 'queries/sync.php');
	}

	public function onInstall(){
		// Generate tables
	}

	public function onUninstall(){
		// Uninstall module
	}

	public function onEnable(){
	}

	public function onDisable(){
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets){
		// Permissions
		PermissionHandler::registerPermissions('Buycraft', array(
			'admincp.buycraft' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'buycraft'),
			'admincp.buycraft.settings' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'settings'),
			'admincp.buycraft.categories' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'categories'),
			'admincp.buycraft.categories.update' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'update_category'),
			'admincp.buycraft.packages' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'packages'),
			'admincp.buycraft.packages.update' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'update_package'),
			'admincp.buycraft.payments' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'payments'),
			'admincp.buycraft.payments.new' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'new_payment'),
			'admincp.buycraft.giftcards' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'gift_cards'),
			'admincp.buycraft.giftcards.new' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'new_gift_card'),
			'admincp.buycraft.giftcards.update' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'update_gift_card'),
			'admincp.buycraft.coupons' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'coupons'),
			'admincp.buycraft.coupons.new' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'new_coupon'),
			'admincp.buycraft.coupons.delete' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'delete_coupon'),
			'admincp.buycraft.bans' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'bans'),
			'admincp.buycraft.bans.new' => $this->_language->get('admin', 'admin_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'new_ban')
		));

		// Hooks
		HookHandler::registerEvent('userPurchaseBuycraftPackage', $this->_buycraft_language->get('language', 'purchase_hook_info'));
		HookHandler::registerEvent('newBuycraftCoupon', $this->_buycraft_language->get('language', 'new_coupon_hook_info'));
		HookHandler::registerEvent('newBuycraftGiftCard', $this->_buycraft_language->get('language', 'new_gift_card_hook_info'));

		// Classes
		require_once(ROOT_PATH . '/modules/Buycraft/classes/Buycraft.php');

		// Widgets
		// Latest purchases
		require_once(ROOT_PATH . '/modules/Buycraft/widgets/LatestPurchasesWidget.php');
		$module_pages = $widgets->getPages('Latest Purchases');
		$widgets->add(new LatestPurchasesWidget($module_pages, $smarty, $this->_language, $this->_buycraft_language, $cache, $user));

		// Featured package
		require_once(ROOT_PATH . '/modules/Buycraft/widgets/FeaturedPackageWidget.php');
		$widgets->add(new FeaturedPackageWidget($module_pages, $smarty, $this->_buycraft_language));

		if(defined('FRONT_END')){
			// Add link to navbar
			$cache->setCache('navbar_order');
			if(!$cache->isCached('buycraft_order')){
				$buycraft_order = 10;
				$cache->store('buycraft_order', 10);
			} else {
				$buycraft_order = $cache->retrieve('buycraft_order');
			}

			$cache->setCache('navbar_icons');
			if(!$cache->isCached('buycraft_icon'))
				$icon = '';
			else
				$icon = $cache->retrieve('buycraft_icon');

			$cache->setCache('buycraft_settings');
			if($cache->isCached('navbar_position'))
				$navbar_pos = $cache->retrieve('navbar_position');
			else
				$navbar_pos = 'top';

			$navs[0]->add('store', $this->_buycraft_language->get('language', 'store'), URL::build($this->_buycraft_url), $navbar_pos, null, $buycraft_order, $icon);
		} else {

		}
	}
}