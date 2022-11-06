<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.2
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - payments
 */

if (!$user->handlePanelPageLoad('admincp.buycraft.payments')) {
    require_once ROOT_PATH . '/403.php';
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'buycraft');
define('PANEL_PAGE', 'buycraft_payments');
$page_title = $buycraft_language->get('language', 'payments');
require_once ROOT_PATH . '/core/templates/backend_init.php';
require_once ROOT_PATH . '/modules/Tebex/classes/Buycraft.php';

if (isset($_GET['action']) && $_GET['action'] == 'create' && $user->hasPermission('admincp.buycraft.payments.new')) {
	if (Input::exists()) {
		$errors = [];

		if (Token::check(Input::get('token'))) {
			if (isset($_GET['step'])) {
				if ($_GET['step'] == 2) {
					if (!isset($_SESSION['bc_payment_ign']) || !isset($_SESSION['bc_payment_package'])) {
						Redirect::to(URL::build('/panel/tebex/payments', 'action=create'));
					}

					if (isset($_POST['bc_payment_price']) && strlen($_POST['bc_payment_price']) > 0) {
						// Ensure package exists
						$package = DB::getInstance()->get('buycraft_packages', ['id', '=', $_SESSION['bc_payment_package']]);
						if (!$package->count()) {
							Redirect::to(URL::build('/panel/tebex/payments', 'action=create'));
						}
						$package = $package->first();

						// Get server key
						$server_key = DB::getInstance()->get('buycraft_settings', ['name', '=', 'server_key']);

						if ($server_key->count())
							$server_key = $server_key->first()->value;
						else
							$server_key = null;

						// POST to Buycraft
						$post_object = new stdClass();
						$post_object->price = $package->price + 0;

						foreach ($_POST as $key => $item) {
							if ($key != 'token' && $key != 'bc_payment_price' && $key != 'price') {
								$post_object->{$key} = $item;
							}
						}

						$post = new stdClass();
						$post->ign = $_SESSION['bc_payment_ign'];
						$post->price = $_POST['bc_payment_price'] + 0;

						$package = new stdClass();
						$package->id = $_SESSION['bc_payment_package'] + 0;
						$package->options = $post_object;

						$post->packages = [$package];

						$json = json_encode($post);

                        $result = HttpClient::post('https://plugin.tebex.io/payments', $json, [
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'X-Tebex-Secret' => $server_key
                            ]
                        ]);

						if ($result->hasError()) {
							$errors[] = Output::getClean($result->getError());
						} else {
							Buycraft::updatePayments($server_key, DB::getInstance());
							Session::flash('buycraft_payment_success', $buycraft_language->get('language', 'payment_created_successfully'));
							Redirect::to(URL::build('/panel/tebex/payments'));
						}

					} else
						$errors[] = $buycraft_language->get('language', 'please_enter_valid_price');
				}
			} else {
				$validation = Validate::check($_POST, [
					'ign' => [
						Validate::REQUIRED => true,
					],
					'package' => [
						Validate::REQUIRED => true,
					]
				]);

				if ($validation->passed()) {
					$_SESSION['bc_payment_ign'] = $_POST['ign'];
					$_SESSION['bc_payment_package'] = $_POST['package'];

					Redirect::to(URL::build('/panel/tebex/payments', 'action=create&step=2'));

				} else
					$errors[] = $buycraft_language->get('language', 'please_enter_valid_ign_package');
			}
		} else
			$errors[] = $language->get('general', 'invalid_token');
	}
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (Session::exists('buycraft_payment_success')) {
	$success = Session::flash('buycraft_payment_success');
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

if (isset($_GET['user'])) {
	// Get payments for user
	$payments = DB::getInstance()->query('SELECT * FROM nl2_buycraft_payments WHERE player_uuid = ? ORDER BY `date` DESC', [$_GET['user']]);

	if ($payments->count()) {
		$payments = $payments->results();

        $integration = Integrations::getInstance()->getIntegration('Minecraft');

		if (($payment_user = new IntegrationUser($integration, $_GET['user'], 'identifier'))->exists()) {
			$avatar = $payment_user->getUser()->getAvatar();
			$style = $payment_user->getUser()->getGroupStyle();
		} else {
			$avatar = AvatarSource::getAvatarFromUUID(Output::getClean($_GET['user']));
			$style = '';
		}

		$template_payments = [];

		foreach ($payments as $payment) {
			$template_payments[] = array(
				'user_link' => URL::build('/panel/tebex/payments/', 'user=' . Output::getClean($payment->player_uuid)),
				'user_style' => $style,
				'user_avatar' => $avatar,
				'username' => Output::getClean($payment->player_name),
				'user_uuid' => Output::getClean($payment->player_uuid),
				'amount' => Output::getPurified(
					Buycraft::formatPrice(
						$payment->amount,
						$payment->currency_iso,
						$payment->currency_symbol,
						TEBEX_CURRENCY_FORMAT
					)
				),
				'date' => date(DATE_FORMAT, $payment->date),
				'link' => URL::build('/panel/tebex/payments', 'payment=' . Output::getClean($payment->id))
			);
		}

		$smarty->assign(array(
			'USER' => $buycraft_language->get('language', 'user'),
			'AMOUNT' => $buycraft_language->get('language', 'amount'),
			'DATE' => $buycraft_language->get('language', 'date'),
			'VIEW' => $buycraft_language->get('language', 'view'),
			'USER_PAYMENTS' => $template_payments
		));

		if (!defined('TEMPLATE_BUYCRAFT_SUPPORT')) {
            $template->assets()->include([
                AssetTree::DATATABLES,
            ]);

			$template->addJSScript('
				$(document).ready(function() {
					$(\'.dataTables-payments\').dataTable({
						responsive: true,
						order: [[ 2, "desc" ]],
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

	} else
		$smarty->assign('NO_PAYMENTS', $buycraft_language->get('language', 'no_payments_for_user'));

	$smarty->assign(array(
		'VIEWING_PAYMENTS_FOR_USER' => $buycraft_language->get('language', 'viewing_payments_for_user', ['user' => Output::getClean($_GET['user'])]),
		'BACK' => $language->get('general', 'back'),
		'BACK_LINK' => URL::build('/panel/tebex/payments')
	));

	$template_file = 'tebex/payments_user.tpl';

} else if (isset($_GET['payment'])) {
	// View payment
	$payment = DB::getInstance()->get('buycraft_payments', ['id', '=', $_GET['payment']]);
	if ($payment->count())
		$payment = $payment->first();
	else {
		Redirect::to(URL::build('/panel/tebex/payments'));
	}

    $integration = Integrations::getInstance()->getIntegration('Minecraft');

    if (($payment_user = new IntegrationUser($integration, $_GET['user'], 'identifier'))->exists()) {
		$avatar = $payment_user->getUser()->getAvatar();
		$style = $payment_user->getUser()->getGroupStyle();
	} else {
		$avatar = AvatarSource::getAvatarFromUUID(Output::getClean($payment->player_uuid ?? $payment->player_name));
		$style = '';
	}

	$commands = DB::getInstance()->get('buycraft_commands', ['payment', '=', $payment->id]);

	$smarty->assign(array(
		'VIEWING_PAYMENT' => $buycraft_language->get('language', 'viewing_payment', ['payment' => Output::getClean($payment->id)]),
		'BACK' => $language->get('general', 'back'),
		'BACK_LINK' => URL::build('/panel/tebex/payments'),
		'IGN' => $buycraft_language->get('language', 'ign'),
		'IGN_VALUE' => Output::getClean($payment->player_name),
		'USER_LINK' => URL::build('/panel/tebex/payments/', 'user=' . Output::getClean($payment->player_uuid)),
		'AVATAR' => $avatar,
		'STYLE' => $style,
		'UUID' => $buycraft_language->get('language', 'uuid'),
		'UUID_VALUE' => Output::getClean($payment->player_uuid),
		'PRICE' => $buycraft_language->get('language', 'price'),
		'PRICE_VALUE' => Output::getPurified(
            Buycraft::formatPrice(
                $payment->amount,
                $payment->currency_iso,
                $payment->currency_symbol,
                '{price} {currencyCode}',
            )
        ),
		'DATE' => $buycraft_language->get('language', 'date'),
		'DATE_VALUE' => date(DATE_FORMAT, $payment->date),
		'PENDING_COMMANDS' => $buycraft_language->get('language', 'pending_commands')
	));

	if ($commands->count()) {
		$pending_commands = array();

		foreach ($commands as $command) {
			$pending_commands[] = Output::getClean($command->command);
		}

		$smarty->assign('PENDING_COMMANDS_VALUE', $pending_commands);

	} else
		$smarty->assign('NO_PENDING_COMMANDS', $buycraft_language->get('language', 'no_pending_commands'));

	$template_file = 'tebex/payments_view.tpl';

} else if (isset($_GET['action'])) {
	if ($_GET['action'] == 'create') {
		// New payment
		$smarty->assign(array(
			'NEW_PAYMENT' => $buycraft_language->get('language', 'new_payment'),
			'CANCEL' => $language->get('general', 'cancel'),
			'CANCEL_LINK' => URL::build('/panel/tebex/payments'),
			'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
			'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
			'YES' => $language->get('general', 'yes'),
			'NO' => $language->get('general', 'no')
		));

		if (isset($_GET['step'])) {
			if ($_GET['step'] == 2) {
				if (!isset($_SESSION['bc_payment_ign']) || !isset($_SESSION['bc_payment_package'])) {
					Redirect::to(URL::build('/panel/tebex/payments/', 'action=create'));
				}

				// Ensure package exists
				$package = DB::getInstance()->get('buycraft_packages', ['id', '=', $_SESSION['bc_payment_package']]);
				if (!$package->count()) {
					Redirect::to(URL::build('/panel/tebex/payments/', 'action=create'));
				}
				$package = $package->first();

				// Get server key
				$server_key = DB::getInstance()->get('buycraft_settings', ['name', '=', 'server_key']);

				if ($server_key->count())
					$server_key = $server_key->first()->value;
				else
					$server_key = null;

				// Get fields from Buycraft API
				$cache->setCache('bc_api_new_payment_package');
				if($cache->isCached('package-' . $_SESSION['bc_payment_package'])){
					$package_fields = $cache->retrieve('package-' . $_SESSION['bc_payment_package']);

				} else {
					// Query API
					if ($server_key) {
						$package_fields = Buycraft::getPackageFields($server_key, $_SESSION['bc_payment_package']);

                        if (isset($package_fields['response'])) {
                            $cache->store('package-' . $_SESSION['bc_payment_package'], $package_fields['response'], 120);
                        }

					} else {
						$server_key_error = true;
						$error = $buycraft_language->get('language', 'invalid_server_key');
					}
				}

				if (isset($error))
					$smarty->assign(array(
						'ERRORS' => array($error),
						'ERRORS_TITLE' => $language->get('general', 'error')
					));

				if (!isset($server_key_error)) {
					$template_package_fields = array();
					if (count($package_fields)) {
						foreach ($package_fields as $field) {
							switch ($field->type) {
								case 'numeric':
								case 'text':
								case 'username':
								case 'email':
									$template_package_fields[] = array(
										'type' => $field->type,
										'id' => Output::getClean($field->id),
										'name_title' => Output::getClean(ucfirst($field->name)),
										'name' => Output::getClean($field->name),
										'description' => isset($field->description) && strlen($field->description) > 0 ? Output::getClean($field->description) : null
									);
									break;

								case 'alpha':
									$template_package_fields[] = array(
										'type' => $field->type,
										'id' => Output::getClean($field->id),
										'name_title' => Output::getClean(ucfirst($field->name)),
										'name' => Output::getClean($field->name),
										'description' => isset($field->description) && strlen($field->description) > 0 ? Output::getClean($field->description) : null,
										'min_length' => Output::getClean($field->rules->min_length)
									);
									break;

								case 'dropdown':
									$options = [];

									foreach($field->options as $option){
										$options[] = array(
											'value' => Output::getClean($option->id),
											'label' => Output::getClean($option->label)
										);
									}

									$template_package_fields[] = array(
										'type' => 'dropdown',
										'id' => Output::getClean($field->id),
										'name_title' => Output::getClean(ucfirst($field->name)),
										'name' => Output::getClean($field->name),
										'description' => isset($field->description) && strlen($field->description) > 0 ? Output::getClean($field->description) : null,
										'options' => $options
									);
									break;
							}
						}
					}

					$smarty->assign(array(
						'PRICE' => $buycraft_language->get('language', 'price'),
						'PRICE_VALUE' => Output::getClean($package->price),
						'PACKAGE_FIELDS' => $template_package_fields
					));
				}

				$template_file = 'tebex/payments_new_step_2.tpl';
			}
		} else {
			// Choose package
			$packages = DB::getInstance()->query('SELECT * FROM nl2_buycraft_packages ORDER BY `order` ASC');

			if ($packages->count()) {
				$template_packages = [];

				foreach ($packages->results() as $package) {
					$template_packages[] = array(
						'id' => Output::getClean($package->id),
						'name' => Output::getClean($package->name)
					);
				}

				$smarty->assign(array(
					'IGN' => $buycraft_language->get('language', 'ign'),
					'PACKAGE' => $buycraft_language->get('language', 'package'),
					'PACKAGES' => $template_packages
				));

			} else
				$smarty->assign('NO_PACKAGES', $buycraft_language->get('language', 'no_packages'));

			$template_file = 'tebex/payments_new_step_1.tpl';
		}
	}

} else {
    $payments = DB::getInstance()->query('SELECT * FROM nl2_buycraft_payments ORDER BY `date` DESC');

	if ($payments->count()) {
		$template_payments = [];
		$payment_users = [];

		foreach ($payments->results() as $payment) {
			if (isset($payment_users[$payment->player_uuid])) {
				[$avatar, $style] = $payment_users[$payment->player_uuid];
			} else {
                $integration = Integrations::getInstance()->getIntegration('Minecraft');

                if (($payment_user = new IntegrationUser($integration, $payment->player_uuid, 'identifier'))->exists()) {
                    $avatar = $payment_user->getUser()->getAvatar();
                    $style = $payment_user->getUser()->getGroupStyle();
                } else {
                    $avatar = AvatarSource::getAvatarFromUUID(Output::getClean($payment->player_uuid ?? $payment->player_name));
                    $style = '';
                }

				$payment_users[$payment->player_uuid] = [$avatar, $style];
			}

			$template_payments[] = array(
				'user_link' => 	URL::build('/panel/tebex/payments/', 'user=' . Output::getClean($payment->player_uuid)),
				'user_style' => $style,
				'user_avatar' => $avatar,
				'username' => Output::getClean($payment->player_name),
				'uuid' => Output::getClean($payment->player_uuid),
				'amount' => Output::getPurified(
					Buycraft::formatPrice(
						$payment->amount,
						$payment->currency_iso,
						$payment->currency_symbol,
						TEBEX_CURRENCY_FORMAT,
					)
				),
				'date' => date(DATE_FORMAT, $payment->date),
				'date_unix' => Output::getClean($payment->date),
				'link' => URL::build('/panel/tebex/payments/', 'payment=' . Output::getClean($payment->id))
			);
		}

		$smarty->assign(array(
			'USER' => $buycraft_language->get('language', 'user'),
			'AMOUNT' => $buycraft_language->get('language', 'amount'),
			'DATE' => $buycraft_language->get('language', 'date'),
			'VIEW' => $buycraft_language->get('language', 'view'),
			'ALL_PAYMENTS' => $template_payments
		));

		if (!defined('TEMPLATE_BUYCRAFT_SUPPORT')) {
            $template->assets()->include([
                AssetTree::DATATABLES,
            ]);

			$template->addJSScript('
				$(document).ready(function() {
					$(\'.dataTables-payments\').dataTable({
						responsive: true,
						order: [[ 2, "desc" ]],
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

	} else
		$smarty->assign('NO_PAYMENTS', $buycraft_language->get('language', 'no_payments'));

	$smarty->assign(array(
		'NEW_PAYMENT' => $buycraft_language->get('language', 'new_payment'),
		'NEW_PAYMENT_LINK' => URL::build('/panel/tebex/payments/', 'action=create')
	));

	$template_file = 'tebex/payments.tpl';
}

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'BUYCRAFT' => $buycraft_language->get('language', 'buycraft'),
	'PAGE' => PANEL_PAGE,
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit'),
	'PAYMENTS' => $buycraft_language->get('language', 'payments')
));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
