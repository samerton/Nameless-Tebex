<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Store latest purchases widget
 */
class LatestPurchasesWidget extends WidgetBase {
	private $_smarty, $_language, $_cache, $_user, $_buycraft_language;

	public function __construct($pages, $smarty, $language, $buycraft_language, $cache, $user){
		parent::__construct($pages);

		$this->_smarty = $smarty;
		$this->_language = $language;
		$this->_buycraft_language = $buycraft_language;
		$this->_cache = $cache;
		$this->_user = $user;

		// Get order
		$order = DB::getInstance()->query('SELECT `order` FROM nl2_widgets WHERE `name` = ?', array('Latest Purchases'))->first();

		// Set widget variables
		$this->_module = 'Tebex';
		$this->_name = 'Latest Purchases';
		$this->_location = 'right';
		$this->_description = 'Displays a list of your store\'s most recent purchases.';
		$this->_settings = ROOT_PATH . '/modules/Tebex/widgets/admin/latest_purchases.php';
		$this->_order = $order->order;
	}

	public function initialise(){
		// Generate HTML code for widget
		$this->_cache->setCache('buycraft_data');
		$queries = new Queries();

		if($this->_cache->isCached('latest_purchases')){
			$latest_purchases = $this->_cache->retrieve('latest_purchases');

		} else {
			if($this->_cache->isCached('purchase_limit')){
				$purchase_limit = intval($this->_cache->retrieve('purchase_limit'));
			} else {
				$purchase_limit = 10;
			}

			$latest_purchases_query = $queries->orderAll('buycraft_payments', '`date`', 'DESC LIMIT ' . $purchase_limit);
			$latest_purchases = array();

			if(count($latest_purchases_query)){
				$timeago = new Timeago(TIMEZONE);

				foreach($latest_purchases_query as $purchase){
					$user_query = $queries->getWhere('users', array('uuid', '=', $purchase->player_uuid));
					if(count($user_query)){
						$user_query = $user_query[0];
						$user_id = Output::getClean($user_query->id);
						$style = $this->_user->getGroupClass($user_query->id);
						$username = Output::getClean($user_query->username);
					} else {
						$user_id = 0;
						$style = null;
						$username = Output::getClean($purchase->player_name);
					}

					$latest_purchases[] = array(
						'avatar' => Util::getAvatarFromUUID(Output::getClean($purchase->player_uuid), 64),
						'profile' => URL::build('/profile/' . $username),
						'price' => Output::getClean($purchase->amount),
						'currency' => Output::getClean($purchase->currency_iso),
						'currency_symbol' => Output::getClean($purchase->currency_symbol),
						'uuid' => Output::getClean($purchase->player_uuid),
						'date_full' => date('d M Y, H:i', $purchase->date),
						'date_friendly' => $timeago->inWords(date('d M Y, H:i', $purchase->date), $this->_language->getTimeLanguage()),
						'style' => $style,
						'username' => $username,
						'user_id' => $user_id
					);

				}
			}

			$this->_cache->store('latest_purchases', $latest_purchases, 120);

			$latest_purchases_query = null;
		}

		if(count($latest_purchases)){
			$this->_smarty->assign(array(
				'LATEST_PURCHASES' => $this->_buycraft_language->get('language', 'latest_purchases'),
				'PURCHASES' => $latest_purchases
			));

		} else
			$this->_smarty->assign(array(
				'LATEST_PURCHASES' => $this->_buycraft_language->get('language', 'latest_purchases'),
				'NO_PURCHASES' => $this->_buycraft_language->get('language', 'no_purchases')
			));

		$this->_content = $this->_smarty->fetch('tebex/widgets/latest_purchases.tpl');
	}
}