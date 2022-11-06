<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - gift cards
 */

if (!$user->handlePanelPageLoad('admincp.buycraft.giftcards')) {
    require_once ROOT_PATH . '/403.php';
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'buycraft');
define('PANEL_PAGE', 'buycraft_giftcards');
$page_title = $buycraft_language->get('language', 'gift_cards');
require_once ROOT_PATH . '/core/templates/backend_init.php';
require_once ROOT_PATH . '/modules/Tebex/classes/Buycraft.php';

if (isset($_GET['action'])) {
	$errors = [];

	if ($_GET['action'] == 'new' && $user->hasPermission('admincp.buycraft.giftcards.new')) {
		if (Input::exists()) {
			if (Token::check(Input::get('token'))) {
				$validation = Validate::check($_POST, [
					'amount' => [
						Validate::REQUIRED => true,
					],
				])->messages([
                    'amount' => [
                        Validate::REQUIRED => $buycraft_language->get('language', 'gift_card_value_required'),
                    ],
                ]);

				if ($validation->passed()) {
					$post_object = new stdClass();
					$post_object->amount = $_POST['amount'] + 0;
					if (isset($_POST['note']) && strlen($_POST['note']) > 0) {
						$post_object->note = Output::getPurified($post_object->note);
					}

					$json = json_encode($post_object);

					// POST to Buycraft
					// Get server key
					$server_key = DB::getInstance()->get('buycraft_settings', ['name', '=', 'server_key']);

					if ($server_key->count())
						$server_key = $server_key->first()->value;
					else
						$server_key = null;

                    $result = HttpClient::post('https://plugin.tebex.io/gift-cards', $json, [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'X-Tebex-Secret' => $server_key
                        ]
                    ]);

					if ($result->hasError()) {
						$errors[] = Output::getClean($result->getError());
					} else {
                        $result = $result->json();

						if (isset($result->data) && isset($result->data->code)) {
							Session::flash('new_gift_card_success', $buycraft_language->get('language', 'gift_card_created_successfully_with_code', ['code' => Output::getClean($result->data->code)]));
						} else {
							Session::flash('new_gift_card_success', $buycraft_language->get('language', 'gift_card_created_successfully'));
						}

						Buycraft::updateGiftCards($server_key, DB::getInstance());
						Redirect::to(URL::build('/panel/tebex/giftcards'));
					}
				} else {
					$errors = $validation->errors();
				}
			} else
				$errors[] = $language->get('general', 'invalid_token');
		}
	} else if ($user->hasPermission('admincp.buycraft.giftcards.update')) {
		if (Input::exists()) {
			if (Token::check(Input::get('token'))) {
				if (isset($_POST['action'])) {
					if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
						Redirect::to(URL::build('/panel/tebex/giftcards'));
					}

					$giftcard = DB::getInstance()->get('buycraft_gift_cards', ['id', '=', $_GET['id']]);
					if (!$giftcard->count()) {
						Redirect::to(URL::build('/panel/tebex/giftcards'));
					}
					$giftcard = $giftcard->first();

					if ($_POST['action'] == 'void') {
						// DELETE request
						// Get server key
						$server_key = DB::getInstance()->get('buycraft_settings', ['name', '=', 'server_key']);

						if ($server_key->count())
							$server_key = $server_key->first()->value;
						else
							$server_key = null;

                        // TODO: convert to HttpClient::delete when available
                        $client = HttpClient::createClient([
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'X-Tebex-Secret' => $server_key
                            ]
                        ]);

                        try {
                            $response = $client->delete('https://plugin.tebex.io/gift-cards/' . $giftcard->id);

                            DB::getInstance()->delete('buycraft_gift_cards', ['id', '=', $giftcard->id]);
                            Session::flash('new_gift_card_success', $buycraft_language->get('language', 'gift_card_voided_successfully'));
                            Redirect::to(URL::build('/panel/tebex/giftcards'));
                        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
                            $errors = [Output::getClean($exception->getMessage())];
                            Log::getInstance()->log(Log::Action('misc/curl_error'), $exception->getMessage());
                        }
					} else if ($_POST['action'] == 'update') {
						$validation = Validate::check($_POST, [
							'credit' => [
								Validate::REQUIRED => true,
							],
						])->messages([
                            'credit' => [
                                Validate::REQUIRED => $buycraft_language->get('language', 'credit_required'),
                            ],
                        ]);

						if ($validation->passed()) {
							$post_object = new stdClass();
							$post_object->amount = $_POST['credit'] + 0;

							$json = json_encode($post_object);

							// PUT to Buycraft
							// Get server key
							$server_key = DB::getInstance()->get('buycraft_settings', ['name', '=', 'server_key']);

							if ($server_key->count())
								$server_key = $server_key->first()->value;
							else
								$server_key = null;

                            // TODO: convert to HttpClient::put when available
                            $client = HttpClient::createClient([
                                'headers' => [
                                    'Content-Type' => 'application/json',
                                    'X-Tebex-Secret' => $server_key
                                ]
                            ]);

                            try {
                                $response = $client->put('https://plugin.tebex.io/gift-cards/' . $giftcard->id, ['body' => $json]);

                                Buycraft::updateGiftCards($server_key, DB::getInstance());
                                Session::flash('new_gift_card_success', $buycraft_language->get('language', 'gift_card_updated_successfully'));
                                Redirect::to(URL::build('/panel/tebex/giftcards'));
                            } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
                                $errors = [Output::getClean($exception->getMessage())];
                                Log::getInstance()->log(Log::Action('misc/curl_error'), $exception->getMessage());
                            }
						} else
							$errors = $validation->errors();
					}
				}
			} else
				$errors[] = $language->get('general', 'invalid_token');
		}
	}
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (Session::exists('new_gift_card_success')) {
	$success = Session::flash('new_gift_card_success');
}

if (isset($success))
	$smarty->assign(array(
		'SUCCESS' => $success,
		'SUCCESS_TITLE' => $language->get('general', 'success')
	));

if (isset($errors) && count($errors))
	$smarty->assign(array(
		'ERRORS' => $errors,
		'ERRORS_TITLE' => $language->get('general', 'error')
	));

if (isset($_GET['action'])) {
	if ($_GET['action'] == 'new' && $user->hasPermission('admincp.buycraft.giftcards.new')) {
		// Get variables
		$currency = DB::getInstance()->get('buycraft_settings', ['name', '=', 'currency_iso']);
		if ($currency->count())
			$currency = Output::getClean($currency->first()->value);
		else
			$currency = '';

		$smarty->assign(array(
			'CANCEL' => $language->get('general', 'cancel'),
			'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
			'CANCEL_LINK' => URL::build('/panel/tebex/giftcards'),
			'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
			'YES' => $language->get('general', 'yes'),
			'NO' => $language->get('general', 'no'),
			'CREATING_GIFT_CARD' => $buycraft_language->get('language', 'creating_gift_card'),
			'GIFT_CARD_VALUE' => $buycraft_language->get('language', 'gift_card_value'),
			'CURRENCY' => $currency,
			'GIFT_CARD_NOTE' => $buycraft_language->get('language', 'gift_card_note'),
			'OPTIONAL' => $buycraft_language->get('language', 'optional'),
		));

		$template_file = 'tebex/giftcards_new.tpl';

	} else if ($_GET['action'] == 'view') {
		if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
			Redirect::to(URL::build('/panel/tebex/giftcards'));
		}

		$giftcard = DB::getInstance()->get('buycraft_gift_cards', ['id', '=', $_GET['id']]);
		if (!$giftcard->count()) {
			Redirect::to(URL::build('/panel/tebex/giftcards'));
		}
		$giftcard = $giftcard->first();

		$smarty->assign(array(
			'VIEWING_GIFT_CARD' => $buycraft_language->get('language', 'viewing_gift_card_x', ['card' => Output::getClean($giftcard->code)]),
			'BACK' => $language->get('general', 'back'),
			'BACK_LINK' => URL::build('/panel/tebex/giftcards'),
			'GIFT_CARD_CODE' => $buycraft_language->get('language', 'gift_card_code'),
			'GIFT_CARD_CODE_VALUE' => Output::getClean($giftcard->code),
			'GIFT_CARD_START_BALANCE' => $buycraft_language->get('language', 'gift_card_start_balance'),
			'GIFT_CARD_START_BALANCE_VALUE' => Output::getClean($giftcard->balance_starting . $giftcard->balance_currency),
			'GIFT_CARD_BALANCE_REMAINING' => $buycraft_language->get('language', 'gift_card_balance_remaining'),
			'GIFT_CARD_BALANCE_REMAINING_VALUE' => Output::getClean($giftcard->balance_remaining . $giftcard->balance_currency),
			'GIFT_CARD_ACTIVE' => $buycraft_language->get('language', 'gift_card_active'),
			'GIFT_CARD_VOID' => $giftcard->void,
			'GIFT_CARD_NOTE' => $buycraft_language->get('language', 'gift_card_note'),
			'GIFT_CARD_NOTE_VALUE' => Output::getPurified($giftcard->note),
			'CAN_UPDATE_GIFT_CARD' => $user->hasPermission('admincp.buycraft.giftcards.update'),
			'ADD_CREDIT' => $buycraft_language->get('language', 'add_credit'),
			'CREDIT' => $buycraft_language->get('language', 'credit'),
			'VOID_GIFT_CARD' => $buycraft_language->get('language', 'void_gift_card'),
			'CONFIRM_VOID_GIFT_CARD' => $buycraft_language->get('language', 'confirm_void_gift_card'),
			'CANCEL' => $language->get('general', 'cancel')
		));

		$template_file = 'tebex/giftcards_view.tpl';
	}
} else {
	// Get all gift cards
    $giftcards = DB::getInstance()->query('SELECT * FROM nl2_buycraft_gift_cards');

	if ($giftcards->count()) {
		$all_giftcards = [];

		foreach ($giftcards->results() as $giftcard) {
			$all_giftcards[] = array(
				'code' => Output::getClean($giftcard->code),
				'note' => Output::getPurified($giftcard->note),
				'remaining' => Output::getClean($giftcard->balance_remaining . $giftcard->balance_currency),
				'void' => $giftcard->void,
				'view_link' => URL::build('/panel/tebex/giftcards/', 'action=view&id=' . Output::getClean($giftcard->id))
			);
		}

		$smarty->assign(array(
			'ALL_GIFT_CARDS' => $all_giftcards,
			'GIFT_CARD_CODE' => $buycraft_language->get('language', 'gift_card_code'),
			'GIFT_CARD_NOTE' => $buycraft_language->get('language', 'gift_card_note'),
			'GIFT_CARD_BALANCE_REMAINING' => $buycraft_language->get('language', 'gift_card_balance_remaining'),
			'GIFT_CARD_ACTIVE' => $buycraft_language->get('language', 'gift_card_active'),
			'VIEW' => $buycraft_language->get('language', 'view'),
		));

		if (!defined('TEMPLATE_BUYCRAFT_SUPPORT')) {
            $template->assets()->include([
                AssetTree::DATATABLES,
            ]);

			$template->addJSScript('
				$(document).ready(function() {
					$(\'.dataTables-giftcards\').dataTable({
						responsive: true,
						language: {
							"lengthMenu": "' . $language->get('table', 'display_records_per_page') . '",
							"zeroRecords": "' . $language->get('table', 'nothing_found') . '",
							"info": "' . $language->get('table', 'page_x_of_y') . '",
							"infoEmpty": "' . $language->get('table', 'no_records') . '",
							"infoFiltered": "' . $language->get('table', 'filtered') . '",
							"search": "' . $language->get('general', 'search') . '",
							"paginate": {
								"next": "' . $language->get('general', 'next') . '",
								"previous": "' . $language->get('general', 'previous') . '"
							}
						}
					});
				});
			');
		}
	} else {
		$smarty->assign('NO_GIFT_CARDS', $buycraft_language->get('language', 'no_gift_cards'));
	}

	$smarty->assign(array(
		'NEW_GIFT_CARD' => $buycraft_language->get('language', 'new_gift_card'),
		'NEW_GIFT_CARD_LINK' => URL::build('/panel/tebex/giftcards/', 'action=new')
	));

	$template_file = 'tebex/giftcards.tpl';
}

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'BUYCRAFT' => $buycraft_language->get('language', 'buycraft'),
	'PAGE' => PANEL_PAGE,
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit'),
	'GIFT_CARDS' => $buycraft_language->get('language', 'gift_cards')
));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
