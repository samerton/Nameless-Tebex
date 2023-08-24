<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Tebex latest purchases widget
 */
class LatestPurchasesWidget extends WidgetBase {
    private Cache $_cache;
    private Language $_language, $_buycraft_language;

	public function __construct(Cache $cache, Smarty $smarty, Language $language, Language $buycraft_language){
		$this->_smarty = $smarty;
		$this->_language = $language;
		$this->_buycraft_language = $buycraft_language;
		$this->_cache = $cache;

		// Set widget variables
		$this->_module = 'Tebex';
		$this->_name = 'Latest Purchases';
		$this->_description = 'Displays a list of your store\'s most recent purchases.';
		$this->_settings = ROOT_PATH . '/modules/Tebex/widgets/admin/latest_purchases.php';
	}

	public function initialise(): void {
		require_once ROOT_PATH . '/modules/Tebex/classes/Buycraft.php';

		// Generate HTML code for widget
		$this->_cache->setCache('buycraft_data');

		if ($this->_cache->isCached('latest_purchases')) {
			$latest_purchases = $this->_cache->retrieve('latest_purchases');

		} else {
			if ($this->_cache->isCached('purchase_limit')) {
				$purchase_limit = intval($this->_cache->retrieve('purchase_limit'));
			} else {
				$purchase_limit = 10;
			}

			$latest_purchases_query = DB::getInstance()->query('SELECT * FROM nl2_buycraft_payments ORDER BY `date` DESC LIMIT ' . $purchase_limit);
			$latest_purchases = [];

			if ($latest_purchases_query->count()) {
				$timeago = new TimeAgo(TIMEZONE);
				$purchase_users = [];

				foreach ($latest_purchases_query->results() as $purchase) {
                    if (isset($purchase_users[$purchase->player_uuid])) {
                        [$user_id, $style, $username] = $purchase_users[$purchase->player_uuid];
                    } else {
                        $integration = Integrations::getInstance()->getIntegration('Minecraft');
                        if (($purchase_user = new IntegrationUser($integration, $purchase->player_uuid, 'identifier'))->exists()) {
                            $purchase_user = $purchase_user->getUser();

                            $user_id = Output::getClean($purchase_user->data()->id);
                            $style = $purchase_user->getGroupStyle();
                            $username = $purchase_user->getDisplayName();
                        } else {
                            $user_id = 0;
                            $style = null;
                            $username = Output::getClean($purchase->player_name);
                        }

                        $purchase_users[$purchase->player_uuid] = [$user_id, $style, $username];
                    }

					$latest_purchases[] = array(
						'avatar' => AvatarSource::getAvatarFromUUID(Output::getClean($purchase->player_uuid)),
						'profile' => URL::build('/profile/' . $username),
						'price' => Output::getPurified(
							Buycraft::formatPrice(
								$purchase->amount,
								$purchase->currency_iso,
								$purchase->currency_symbol,
								TEBEX_CURRENCY_FORMAT,
							)
						),
						'uuid' => Output::getClean($purchase->player_uuid),
						'date_full' => date(DATE_FORMAT, $purchase->date),
						'date_friendly' => $timeago->inWords(date('d M Y, H:i', $purchase->date), $this->_language),
						'style' => $style,
						'username' => $username,
						'user_id' => $user_id
					);

				}
			}

			$this->_cache->store('latest_purchases', $latest_purchases, 120);

			$latest_purchases_query = null;
		}

		if (count($latest_purchases)) {
			$this->_smarty->assign(array(
				'LATEST_PURCHASES' => $this->_buycraft_language->get('language', 'latest_purchases'),
				'PURCHASES' => $latest_purchases
			));

		} else {
            $this->_smarty->assign(array(
                'LATEST_PURCHASES' => $this->_buycraft_language->get('language', 'latest_purchases'),
                'NO_PURCHASES' => $this->_buycraft_language->get('language', 'no_purchases')
            ));
        }

		$this->_content = $this->_smarty->fetch('tebex/widgets/latest_purchases.tpl');
	}
}