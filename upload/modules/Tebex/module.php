<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr8
 *
 *  License: MIT
 *
 *  Tebex module file
 */

class Tebex_Module extends Module {
	private $_buycraft_language, $_language, $_cache, $_buycraft_url;

	public function __construct($language, $buycraft_language, $pages, $cache){
		$this->_language = $language;
		$this->_buycraft_language = $buycraft_language;
		$this->_cache = $cache;

		$name = 'Tebex';
		$author = '<a href="https://samerton.me" target="_blank" rel="nofollow noopener">Samerton</a>';
		$module_version = '1.1.2';
		$nameless_version = '2.0.0-pr12';

		parent::__construct($this, $name, $author, $module_version, $nameless_version);

		// Get variables from cache
		$cache->setCache('buycraft_settings');
		if($cache->isCached('buycraft_url')){
			$this->_buycraft_url = Output::getClean(rtrim($cache->retrieve('buycraft_url'), '/'));
		} else {
			$this->_buycraft_url = '/store';
		}

		// Pages
		$pages->add('Tebex', $this->_buycraft_url, 'pages/store/index.php', 'tebex', true);
		$pages->add('Tebex', $this->_buycraft_url . '/category', 'pages/store/category.php', 'package', true);
		$pages->add('Tebex', '/panel/tebex', 'pages/panel/index.php');
		$pages->add('Tebex', '/panel/tebex/bans', 'pages/panel/bans.php');
		$pages->add('Tebex', '/panel/tebex/categories', 'pages/panel/categories.php');
		$pages->add('Tebex', '/panel/tebex/coupons', 'pages/panel/coupons.php');
		$pages->add('Tebex', '/panel/tebex/giftcards', 'pages/panel/giftcards.php');
		$pages->add('Tebex', '/panel/tebex/packages', 'pages/panel/packages.php');
		$pages->add('Tebex', '/panel/tebex/payments', 'pages/panel/payments.php');
		$pages->add('Tebex', '/panel/tebex/sync', 'pages/panel/sync.php');
		$pages->add('Tebex', '/queries/store/sync', 'queries/sync.php');

		// Ajax GET requests
		$pages->addAjaxScript(URL::build('/queries/store/sync'));
	}

	public function onInstall(){
		// Initialise
		$this->initialise();
	}

	public function onUninstall(){
		// Uninstall module
	}

	public function onEnable(){
		// Check if we need to initialise again
		$this->initialise();
	}

	public function onDisable(){
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template){
		// Permissions
		PermissionHandler::registerPermissions('Buycraft', array(
			'admincp.buycraft' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'buycraft'),
			'admincp.buycraft.settings' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'settings'),
			'admincp.buycraft.categories' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'categories'),
			'admincp.buycraft.categories.update' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'update_category'),
			'admincp.buycraft.packages' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'packages'),
			'admincp.buycraft.packages.update' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'update_package'),
			'admincp.buycraft.payments' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'payments'),
			'admincp.buycraft.payments.new' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'new_payment'),
			'admincp.buycraft.giftcards' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'gift_cards'),
			'admincp.buycraft.giftcards.new' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'new_gift_card'),
			'admincp.buycraft.giftcards.update' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'update_gift_card'),
			'admincp.buycraft.coupons' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'coupons'),
			'admincp.buycraft.coupons.new' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'new_coupon'),
			'admincp.buycraft.coupons.delete' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'delete_coupon'),
			'admincp.buycraft.bans' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'bans'),
			'admincp.buycraft.bans.new' => $this->_language->get('moderator', 'staff_cp') . ' &raquo; ' . $this->_buycraft_language->get('language', 'new_ban')
		));

		// Hooks
		// TODO
		// HookHandler::registerEvent('userPurchaseBuycraftPackage', $this->_buycraft_language->get('language', 'purchase_hook_info'));
		// HookHandler::registerEvent('newBuycraftCoupon', $this->_buycraft_language->get('language', 'new_coupon_hook_info'));
		// HookHandler::registerEvent('newBuycraftGiftCard', $this->_buycraft_language->get('language', 'new_gift_card_hook_info'));

		// Classes
		require_once(ROOT_PATH . '/modules/Tebex/classes/Buycraft.php');

		// Widgets
		// Latest purchases
		require_once(ROOT_PATH . '/modules/Tebex/widgets/LatestPurchasesWidget.php');
		$module_pages = $widgets->getPages('Latest Purchases');
		$widgets->add(new LatestPurchasesWidget($module_pages, $smarty, $this->_language, $this->_buycraft_language, $cache, $user));

		// Featured package
		require_once(ROOT_PATH . '/modules/Tebex/widgets/FeaturedPackageWidget.php');
		$module_pages = $widgets->getPages('Featured Package');
		$widgets->add(new FeaturedPackageWidget($module_pages, $smarty, $this->_language, $this->_buycraft_language, $cache));

		// Add link to navbar
		$cache->setCache('navbar_order');
		if(!$cache->isCached('buycraft_order')){
			$buycraft_order = 10;
			$cache->store('buycraft_order', 10);
		} else {
			$buycraft_order = $cache->retrieve('buycraft_order');
		}

		$cache->setCache('navbar_icons');
		if(!$cache->isCached('tebex_icon'))
			$icon = '';
		else
			$icon = $cache->retrieve('tebex_icon');

		$cache->setCache('buycraft_settings');
		if($cache->isCached('navbar_position'))
			$navbar_pos = $cache->retrieve('navbar_position');
		else
			$navbar_pos = 'top';

		$navs[0]->add('tebex', $this->_buycraft_language->get('language', 'store'), URL::build($this->_buycraft_url), $navbar_pos, null, $buycraft_order, $icon);

		if(defined('BACK_END')){
			if($user->hasPermission('admincp.buycraft')){
				$cache->setCache('panel_sidebar');
				
				if($cache->isCached('buycraft_order') && $cache->retrieve('buycraft_order') == 15){
					$cache->erase('buycraft_order');
				}
				
				if(!$cache->isCached('buycraft_order')){
					$order = 21;
					$cache->store('buycraft_order', 21);
				} else {
					$order = $cache->retrieve('buycraft_order');
				}

				$navs[2]->add('buycraft_divider', mb_strtoupper($this->_buycraft_language->get('language', 'buycraft')), 'divider', 'top', null, $order, '');

				if($user->hasPermission('admincp.buycraft.settings')){
					if(!$cache->isCached('buycraft_icon')){
						$icon = '<i class="nav-icon fas fa-shopping-cart"></i>';
						$cache->store('buycraft_icon', $icon);
					} else
						$icon = $cache->retrieve('buycraft_icon');

					$navs[2]->add('buycraft', $this->_buycraft_language->get('language', 'buycraft'), URL::build('/panel/tebex'), 'top', null, ($order + 0.1), $icon);
				}

				if($user->hasPermission('admincp.buycraft.bans')){
					if(!$cache->isCached('buycraft_bans_icon')){
						$icon = '<i class="nav-icon fas fa-gavel"></i>';
						$cache->store('buycraft_bans_icon', $icon);
					} else
						$icon = $cache->retrieve('buycraft_bans_icon');

					$navs[2]->add('buycraft_bans', $this->_buycraft_language->get('language', 'bans'), URL::build('/panel/tebex/bans'), 'top', null, ($order + 0.2), $icon);
				}

				if($user->hasPermission('admincp.buycraft.categories')){
					if(!$cache->isCached('buycraft_categories_icon')){
						$icon = '<i class="nav-icon fas fa-align-justify"></i>';
						$cache->store('buycraft_categories_icon', $icon);
					} else
						$icon = $cache->retrieve('buycraft_categories_icon');

					$navs[2]->add('buycraft_categories', $this->_buycraft_language->get('language', 'categories'), URL::build('/panel/tebex/categories'), 'top', null, ($order + 0.3), $icon);
				}

				if($user->hasPermission('admincp.buycraft.coupons')){
					if(!$cache->isCached('buycraft_coupons_icon')){
						$icon = '<i class="nav-icon fas fa-ticket-alt"></i>';
						$cache->store('buycraft_coupons_icon', $icon);
					} else
						$icon = $cache->retrieve('buycraft_coupons_icon');

					$navs[2]->add('buycraft_coupons', $this->_buycraft_language->get('language', 'coupons'), URL::build('/panel/tebex/coupons'), 'top', null, ($order + 0.4), $icon);
				}

				if($user->hasPermission('admincp.buycraft.giftcards')){
					if(!$cache->isCached('buycraft_giftcards_icon')){
						$icon = '<i class="nav-icon fas fa-gift"></i>';
						$cache->store('buycraft_giftcards_icon', $icon);
					} else
						$icon = $cache->retrieve('buycraft_giftcards_icon');

					$navs[2]->add('buycraft_giftcards', $this->_buycraft_language->get('language', 'gift_cards'), URL::build('/panel/tebex/giftcards'), 'top', null, ($order + 0.5), $icon);
				}

				if($user->hasPermission('admincp.buycraft.packages')){
					if(!$cache->isCached('buycraft_packages_icon')){
						$icon = '<i class="nav-icon fas fa-box-open"></i>';
						$cache->store('buycraft_packages_icon', $icon);
					} else
						$icon = $cache->retrieve('buycraft_packages_icon');

					$navs[2]->add('buycraft_packages', $this->_buycraft_language->get('language', 'packages'), URL::build('/panel/tebex/packages'), 'top', null, ($order + 0.6), $icon);
				}

				if($user->hasPermission('admincp.buycraft.payments')){
					if(!$cache->isCached('buycraft_payments_icon')){
						$icon = '<i class="nav-icon fas fa-donate"></i>';
						$cache->store('buycraft_payments_icon', $icon);
					} else
						$icon = $cache->retrieve('buycraft_payments_icon');

					$navs[2]->add('buycraft_payments', $this->_buycraft_language->get('language', 'payments'), URL::build('/panel/tebex/payments'), 'top', null, ($order + 0.7), $icon);
				}

				if(!$cache->isCached('buycraft_sync_icon')){
					$icon = '<i class="nav-icon fas fa-sync-alt"></i>';
					$cache->store('buycraft_sync_icon', $icon);
				} else
					$icon = $cache->retrieve('buycraft_sync_icon');

				$navs[2]->add('buycraft_sync', $this->_buycraft_language->get('language', 'force_sync'), URL::build('/panel/tebex/sync'), 'top', null, ($order + 0.8), $icon);

				if(!$cache->isCached('buycraft_panel_icon')){
					$icon = '<i class="nav-icon fas fa-external-link-alt"></i>';
					$cache->store('buycraft_panel_icon', $icon);
				} else
					$icon = $cache->retrieve('buycraft_panel_icon');

				$navs[2]->add('buycraft_panel', $this->_buycraft_language->get('language', 'buycraft'), 'https://server.tebex.io/dashboard', 'top', '_blank', ($order + 0.9), $icon);
			}
		}
	}

	private function initialise(){
		// Generate tables
		try {
			$engine = Config::get('mysql/engine');
			$charset = Config::get('mysql/charset');
		} catch(Exception $e){
			$engine = 'InnoDB';
			$charset = 'utf8mb4';
		}

		if(!$engine || is_array($engine))
			$engine = 'InnoDB';

		if(!$charset || is_array($charset))
			$charset = 'latin1';

		$queries = new Queries();

		if(!$queries->tableExists('buycraft_bans')){
			try {
				$queries->createTable('buycraft_bans', ' `id` int(11) NOT NULL AUTO_INCREMENT, `time` int(11) NOT NULL DEFAULT \'0\', `ip` varchar(64) DEFAULT NULL, `payment_email` varchar(256) DEFAULT NULL, `reason` text, `user_ign` varchar(20) DEFAULT NULL, `uuid` varchar(32) DEFAULT NULL, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		if(!$queries->tableExists('buycraft_categories')){
			try {
				$queries->createTable('buycraft_categories', ' `id` int(11) NOT NULL AUTO_INCREMENT, `order` int(11) NOT NULL, `name` varchar(256) NOT NULL, `only_subcategories` tinyint(1) NOT NULL DEFAULT \'0\', `parent_category` int(11) DEFAULT NULL, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		if(!$queries->tableExists('buycraft_categories_descriptions')){
			try {
				$queries->createTable('buycraft_categories_descriptions', ' `id` int(11) NOT NULL AUTO_INCREMENT, `category_id` int(11) NOT NULL, `description` mediumtext, `image` varchar(128) DEFAULT NULL, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		if(!$queries->tableExists('buycraft_commands')){
			try {
				$queries->createTable('buycraft_commands', ' `id` int(11) NOT NULL AUTO_INCREMENT, `type` tinyint(1) NOT NULL DEFAULT \'0\', `command` varchar(256) DEFAULT NULL, `payment` int(11) NOT NULL, `package` int(11) NOT NULL, `player_name` varchar(20) DEFAULT NULL, `player_uuid` varchar(32) DEFAULT NULL, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		if(!$queries->tableExists('buycraft_coupons')){
			try {
				$queries->createTable('buycraft_coupons', '`id` int(11) NOT NULL AUTO_INCREMENT, `code` varchar(64) NOT NULL, `effective_type` varchar(64) NOT NULL, `effective_packages` text, `effective_categories` text, `discount_type` varchar(64) NOT NULL, `discount_percentage` varchar(20) NOT NULL DEFAULT \'0\', `discount_value` varchar(20) NOT NULL DEFAULT \'0\', `redeem_unlimited` tinyint(1) NOT NULL DEFAULT \'0\', `expires` tinyint(1) NOT NULL DEFAULT \'0\', `redeem_limit` int(11) NOT NULL DEFAULT \'0\', `date` int(11) NOT NULL DEFAULT \'0\', `basket_type` varchar(64) DEFAULT NULL, `start_date` int(11) NOT NULL DEFAULT \'0\', `user_limit` int(11) NOT NULL DEFAULT \'0\', `minimum` varchar(20) DEFAULT NULL, `username` varchar(64) DEFAULT NULL, `note` varchar(512) DEFAULT NULL, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		if(!$queries->tableExists('buycraft_gift_cards')){
			try {
				$queries->createTable('buycraft_gift_cards', ' `id` int(11) NOT NULL AUTO_INCREMENT, `code` varchar(64) NOT NULL, `balance_starting` varchar(8) NOT NULL, `balance_remaining` varchar(8) NOT NULL, `balance_currency` varchar(3) NOT NULL, `note` varchar(512) DEFAULT NULL, `void` tinyint(1) NOT NULL DEFAULT \'0\', PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		if(!$queries->tableExists('buycraft_packages')){
			try {
				$queries->createTable('buycraft_packages', ' `id` int(11) NOT NULL AUTO_INCREMENT, `category_id` int(11) NOT NULL, `order` int(11) NOT NULL, `name` varchar(256) NOT NULL, `price` varchar(8) NOT NULL, `sale_active` tinyint(1) NOT NULL DEFAULT \'0\', `sale_discount` varchar(8) DEFAULT NULL, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		if(!$queries->tableExists('buycraft_packages_descriptions')){
			try {
				$queries->createTable('buycraft_packages_descriptions', ' `id` int(11) NOT NULL AUTO_INCREMENT, `package_id` int(11) NOT NULL, `description` mediumtext, `image` varchar(128) DEFAULT NULL, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		if(!$queries->tableExists('buycraft_payments')){
			try {
				$queries->createTable('buycraft_payments', ' `id` int(11) NOT NULL AUTO_INCREMENT, `amount` varchar(8) NOT NULL, `date` int(11) NOT NULL, `currency_iso` varchar(3) NOT NULL, `currency_symbol` varchar(3) NOT NULL, `player_uuid` varchar(32) NOT NULL, `player_name` varchar(32) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `id` (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		if(!$queries->tableExists('buycraft_settings')){
			try {
				$queries->createTable('buycraft_settings', ' `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(16) NOT NULL, `value` text, PRIMARY KEY (`id`)', "ENGINE=$engine DEFAULT CHARSET=$charset");
			} catch(Exception $e){
				// Error
			}
		}

		// Permissions
		$admin_permissions = $queries->getWhere('groups', array('id', '=', 2));
		$admin_permissions = $admin_permissions[0]->permissions;

		$admin_permissions = json_decode($admin_permissions, true);
		$admin_permissions['admincp.buycraft'] = 1;
		$admin_permissions['admincp.buycraft.settings'] = 1;
		$admin_permissions['admincp.buycraft.categories'] = 1;
		$admin_permissions['admincp.buycraft.categories.update'] = 1;
		$admin_permissions['admincp.buycraft.packages'] = 1;
		$admin_permissions['admincp.buycraft.packages.update'] = 1;
		$admin_permissions['admincp.buycraft.payments'] = 1;
		$admin_permissions['admincp.buycraft.payments.new'] = 1;
		$admin_permissions['admincp.buycraft.giftcards'] = 1;
		$admin_permissions['admincp.buycraft.giftcards.new'] = 1;
		$admin_permissions['admincp.buycraft.giftcards.update'] = 1;
		$admin_permissions['admincp.buycraft.coupons'] = 1;
		$admin_permissions['admincp.buycraft.coupons.new'] = 1;
		$admin_permissions['admincp.buycraft.coupons.delete'] = 1;
		$admin_permissions['admincp.buycraft.bans'] = 1;
		$admin_permissions['admincp.buycraft.bans.new'] = 1;

		$admin_permissions_updated = json_encode($admin_permissions);

		$queries->update('groups', 2, array(
			'permissions' => $admin_permissions_updated
		));
	}
}
