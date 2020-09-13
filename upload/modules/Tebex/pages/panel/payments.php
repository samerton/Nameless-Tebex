<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - payments
 */

// Can the user view the AdminCP?
if($user->isLoggedIn()){
	if(!$user->canViewACP()){
		// No
		Redirect::to(URL::build('/'));
		die();
	} else {
		// Check the user has re-authenticated
		if(!$user->isAdmLoggedIn()){
			// They haven't, do so now
			Redirect::to(URL::build('/panel/auth'));
			die();
		} else {
			if(!$user->hasPermission('admincp.buycraft.payments')){
				Redirect::to(URL::build('/panel'));
				die();
			}
		}
	}
} else {
	// Not logged in
	Redirect::to(URL::build('/login'));
	die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'buycraft');
define('PANEL_PAGE', 'buycraft_payments');
$page_title = $buycraft_language->get('language', 'payments');
require_once(ROOT_PATH . '/core/templates/backend_init.php');
require_once(ROOT_PATH . '/modules/Tebex/classes/Buycraft.php');

if(isset($_GET['action']) && $_GET['action'] == 'create' && $user->hasPermission('admincp.buycraft.payments.new')){
	if(Input::exists()){
		$errors = array();

		if(Token::check(Input::get('token'))){
			if(isset($_GET['step'])){
				if($_GET['step'] == 2){
					if(!isset($_SESSION['bc_payment_ign']) || !isset($_SESSION['bc_payment_package'])){
						Redirect::to(URL::build('/panel/tebex/payments', 'action=create'));
						die();
					}

					if(isset($_POST['bc_payment_price']) && strlen($_POST['bc_payment_price']) > 0){
						// Ensure package exists
						$package = $queries->getWhere('buycraft_packages', array('id', '=', $_SESSION['bc_payment_package']));
						if(!count($package)){
							Redirect::to(URL::build('/panel/tebex/payments', 'action=create'));
							die();

						}
						$package = $package[0];

						// Get server key
						$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

						if(count($server_key))
							$server_key = $server_key[0]->value;
						else
							$server_key = null;

						// POST to Buycraft
						$post_object = new stdClass();
						$post_object->price = $package->price + 0;

						foreach($_POST as $key => $item){
							if($key != 'token' && $key != 'bc_payment_price' && $key != 'price'){
								$post_object->{$key} = $item;
							}
						}

						$post = new stdClass();
						$post->ign = $_SESSION['bc_payment_ign'];
						$post->price = $_POST['bc_payment_price'] + 0;

						$package = new stdClass();
						$package->id = $_SESSION['bc_payment_package'] + 0;
						$package->options = $post_object;

						$post->packages = array($package);

						$json = json_encode($post);

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/payments');
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-Buycraft-Secret: ' . $server_key));
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

						$ch_result = curl_exec($ch);

						$result = json_decode($ch_result);

						curl_close($ch);

						if(isset($result->error_code)){
							$errors[] = Output::getClean($result->error_code . ': ' . $result->error_message);
						} else {
							Buycraft::updatePayments($server_key, null, DB::getInstance());
							Session::flash('buycraft_payment_success', $buycraft_language->get('language', 'payment_created_successfully'));
							Redirect::to(URL::build('/panel/tebex/payments'));
							die();
						}

					} else
						$errors[] = $buycraft_language->get('language', 'please_enter_valid_price');
				}
			} else {
				$validate = new Validate();
				$validation = $validate->check($_POST, array(
					'ign' => array(
						'required' => true
					),
					'package' => array(
						'required' => true
					)
				));

				if($validation->passed()){
					$_SESSION['bc_payment_ign'] = $_POST['ign'];
					$_SESSION['bc_payment_package'] = $_POST['package'];

					Redirect::to(URL::build('/panel/tebex/payments', 'action=create&step=2'));
					die();

				} else
					$errors[] = $buycraft_language->get('language', 'please_enter_valid_ign_package');
			}
		} else
			$errors[] = $language->get('general', 'invalid_token');
	}
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

if(Session::exists('buycraft_payment_success')){
	$success = Session::flash('buycraft_payment_success');
}

if(isset($success))
	$smarty->assign(array(
		'SUCCESS' => $success,
		'SUCCESS_TITLE' => $language->get('general', 'success')
	));

if(isset($errors) && count($errors))
	$smarty->assign(array(
		'ERRORS' => $errors,
		'ERRORS_TITLE' => $language->get('general', 'error')
	));

if(isset($_GET['user'])){
	// Get payments for user
	$payments = DB::getInstance()->query('SELECT * FROM nl2_buycraft_payments WHERE player_uuid = ? ORDER BY `date` DESC', array($_GET['user']));

	if($payments->count()){
		$payments = $payments->results();

		$payment_user = $queries->getWhere('users', array('uuid', '=', $_GET['user']));

		if(count($payment_user)){
			$avatar = $user->getAvatar($payment_user[0]->id);
			$style = $user->getGroupClass($payment_user[0]->id);

		} else {
			$avatar = Util::getAvatarFromUUID(Output::getClean($_GET['user']));
			$style = '';

		}

		$template_payments = array();

		foreach($payments as $payment){
			$template_payments[] = array(
				'user_link' => URL::build('/panel/tebex/payments/', 'user=' . Output::getClean($payment->player_uuid)),
				'user_style' => $style,
				'user_avatar' => $avatar,
				'username' => Output::getClean($payment->player_name),
				'user_uuid' => Output::getClean($payment->player_uuid),
				'currency' => Output::getPurified($payment->currency_symbol),
				'amount' => Output::getClean($payment->amount),
				'date' => date('d M Y, H:i', $payment->date),
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

		if(!defined('TEMPLATE_BUYCRAFT_SUPPORT')){
			$template->addCSSFiles(array(
				(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/custom/panel_templates/Default/assets/css/dataTables.bootstrap4.min.css' => array()
			));

			$template->addJSFiles(array(
				(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/dataTables/jquery.dataTables.min.js' => array(),
				(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/custom/panel_templates/Default/assets/js/dataTables.bootstrap4.min.js' => array()
			));

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
		'VIEWING_PAYMENTS_FOR_USER' => str_replace('{x}', Output::getClean($_GET['user']), $buycraft_language->get('language', 'viewing_payments_for_user_x')),
		'BACK' => $language->get('general', 'back'),
		'BACK_LINK' => URL::build('/panel/tebex/payments')
	));

	$template_file = 'tebex/payments_user.tpl';

} else if(isset($_GET['payment'])){
	// View payment
	$payment = $queries->getWhere('buycraft_payments', array('id', '=', $_GET['payment']));
	if(count($payment))
		$payment = $payment[0];
	else {
		Redirect::to(URL::build('/panel/tebex/payments'));
		die();
	}

	$payment_user = $queries->getWhere('users', array('uuid', '=', Output::getClean($payment->player_uuid)));

	if(count($payment_user)){
		$avatar = $user->getAvatar($payment_user[0]->id);
		$style = $user->getGroupClass($payment_user[0]->id);

	} else {
		$avatar = Util::getAvatarFromUUID(Output::getClean($payment->player_uuid));
		$style = '';

	}

	$commands = $queries->getWhere('buycraft_commands', array('payment', '=', $payment->id));

	$smarty->assign(array(
		'VIEWING_PAYMENT' => str_replace('{x}', Output::getClean($payment->id), $buycraft_language->get('language', 'viewing_payment')),
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
		'PRICE_VALUE' => Output::getClean($payment->amount),
		'CURRENCY_SYMBOL' => Output::getClean($payment->currency_symbol),
		'CURRENCY_ISO' => Output::getClean($payment->currency_iso),
		'DATE' => $buycraft_language->get('language', 'date'),
		'DATE_VALUE' => date('d M Y, H:i', $payment->date),
		'PENDING_COMMANDS' => $buycraft_language->get('language', 'pending_commands')
	));

	if(count($commands)){
		$pending_commands = array();

		foreach($commands as $command){
			$pending_commands[] = Output::getClean($command->command);
		}

		$smarty->assign('PENDING_COMMANDS_VALUE', $pending_commands);

	} else
		$smarty->assign('NO_PENDING_COMMANDS', $buycraft_language->get('language', 'no_pending_commands'));

	$template_file = 'tebex/payments_view.tpl';

} else if(isset($_GET['action'])){
	if($_GET['action'] == 'create'){
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

		if(isset($_GET['step'])){
			if($_GET['step'] == 2){
				if(!isset($_SESSION['bc_payment_ign']) || !isset($_SESSION['bc_payment_package'])){
					Redirect::to(URL::build('/panel/tebex/payments/', 'action=create'));
					die();
				}

				// Ensure package exists
				$package = $queries->getWhere('buycraft_packages', array('id', '=', $_SESSION['bc_payment_package']));
				if(!count($package)){
					Redirect::to(URL::build('/panel/tebex/payments/', 'action=create'));
					die();
				}
				$package = $package[0];

				// Get server key
				$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

				if(count($server_key))
					$server_key = $server_key[0]->value;
				else
					$server_key = null;

				// Get fields from Buycraft API
				$cache->setCache('bc_api_new_payment_package');
				if($cache->isCached('package-' . $_SESSION['bc_payment_package'])){
					$package_fields = $cache->retrieve('package-' . $_SESSION['bc_payment_package']);

				} else {
					// Query API
					if($server_key){
						$package_fields = Buycraft::getPackageFields($server_key, $_SESSION['bc_payment_package']);

						$cache->store('package-' . $_SESSION['bc_payment_package'], $package_fields, 120);

					} else {
						$server_key_error = true;
						$error = $buycraft_language->get('language', 'invalid_server_key');
					}

				}

				if(isset($error))
					$smarty->assign(array(
						'ERRORS' => array($error),
						'ERRORS_TITLE' => $language->get('general', 'error')
					));

				if(!isset($server_key_error)){
					$template_package_fields = array();
					if(count($package_fields)){
						foreach($package_fields as $field){
							switch($field->type){
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
									$options = array();

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
			$packages = $queries->orderAll('buycraft_packages', '`order`', 'ASC');

			if(count($packages)){
				$template_packages = array();

				foreach($packages as $package){
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
	$payments = $queries->orderAll('buycraft_payments', 'date', 'DESC');

	if(count($payments)){
		$template_payments = array();

		foreach($payments as $payment){
			$payment_user = $queries->getWhere('users', array('uuid', '=', $payment->player_uuid));

			if(count($payment_user)){
				$avatar = $user->getAvatar($payment_user[0]->id);
				$style = $user->getGroupClass($payment_user[0]->id);

			} else {
				$avatar = Util::getAvatarFromUUID(Output::getClean($payment->player_uuid));
				$style = '';

			}

			$template_payments[] = array(
				'user_link' => 	URL::build('/panel/tebex/payments/', 'user=' . Output::getClean($payment->player_uuid)),
				'user_style' => $style,
				'user_avatar' => $avatar,
				'username' => Output::getClean($payment->player_name),
				'uuid' => Output::getClean($payment->player_uuid),
				'currency_symbol' => Output::getPurified($payment->currency_symbol),
				'amount' => Output::getClean($payment->amount),
				'date' => date('d M Y, H:i', $payment->date),
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

		if(!defined('TEMPLATE_BUYCRAFT_SUPPORT')){
			$template->addCSSFiles(array(
				(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/custom/panel_templates/Default/assets/css/dataTables.bootstrap4.min.css' => array()
			));

			$template->addJSFiles(array(
				(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/dataTables/jquery.dataTables.min.js' => array(),
				(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/custom/panel_templates/Default/assets/js/dataTables.bootstrap4.min.js' => array()
			));

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

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
