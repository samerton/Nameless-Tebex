<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - bans
 */

if (!$user->handlePanelPageLoad('admincp.buycraft.bans')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'buycraft');
define('PANEL_PAGE', 'buycraft_bans');
$page_title = $buycraft_language->get('language', 'bans');
require_once(ROOT_PATH . '/core/templates/backend_init.php');
require_once(ROOT_PATH . '/modules/Tebex/classes/Buycraft.php');

if (isset($_GET['action'])) {
	if ($_GET['action'] == 'new') {
		if (!$user->hasPermission('admincp.buycraft.bans.new')) {
			Redirect::to(URL::build('/panel/tebex/bans'));
		}

		if (Input::exists()) {
			$errors = array();

			if (Token::check(Input::get('token'))) {
				$validation = Validate::check($_POST, [
					'user' => [
						Validate::REQUIRED => true,
					]
				])->messages([
                    'user' => [
                        Validate::REQUIRED => $buycraft_language->get('language', 'must_enter_uuid'),
                    ]
                ]);

				if ($validation->passed()) {
					// POST to Buycraft
					$post_object = new stdClass();
					$post_object->user = Output::getClean(str_replace('-', '', $_POST['user']));

					if (isset($_POST['ip']) && strlen($_POST['ip']) > 0) {
						$post_object->ip = Output::getClean($_POST['ip']);
					}

					if (isset($_POST['reason']) && strlen($_POST['reason']) > 0) {
						$post_object->reason = Output::getPurified($_POST['reason']);
					}

					$json = json_encode($post_object);

					// Get server key
					$server_key = DB::getInstance()->get('buycraft_settings', array('name', '=', 'server_key'));

					if ($server_key->count())
						$server_key = $server_key->first()->value;
					else
						$server_key = null;

                    $result = HttpClient::post('https://plugin.tebex.io/bans', $json, [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'X-Tebex-Secret' => $server_key
                        ]
                    ]);

					if ($result->hasError()) {
						$errors[] = Output::getClean($result->getError());
					} else {
						Buycraft::updateBans($server_key, DB::getInstance());
						Session::flash('buycraft_ban_success', $buycraft_language->get('language', 'ban_created_successfully'));
						Redirect::to(URL::build('/panel/tebex/bans'));
					}
				} else {
					$errors = $validation->errors();
				}
			} else {
				$errors[] = $language->get('general', 'invalid_token');
			}
		}
	}
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (Session::exists('buycraft_ban_success')) {
	$success = Session::flash('buycraft_ban_success');
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
	if ($_GET['action'] == 'new') {
		$smarty->assign(array(
			'UUID' => $buycraft_language->get('language', 'uuid'),
			'IP_ADDRESS' => $buycraft_language->get('language', 'ip_address'),
			'OPTIONAL' => $buycraft_language->get('language', 'optional'),
			'REASON' => $buycraft_language->get('language', 'reason'),
			'CANCEL' => $language->get('general', 'cancel'),
			'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
			'CANCEL_LINK' => URL::build('/panel/tebex/bans'),
			'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
			'YES' => $language->get('general', 'yes'),
			'NO' => $language->get('general', 'no'),
			'CREATING_BAN' => $buycraft_language->get('language', 'creating_ban')
		));

		$template_file = 'tebex/bans_new.tpl';

	} else if ($_GET['action'] == 'view') {
		// Get ban
		if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
			Redirect::to(URL::build('/panel/tebex/bans'));
		}

		$ban = DB::getInstance()->get('buycraft_bans', array('id', '=', $_GET['id']));
		if (!$ban->count()) {
			Redirect::to(URL::build('/panel/tebex/bans'));
		} else
			$ban = $ban->first();

		$smarty->assign(array(
			'VIEWING_BAN' => $buycraft_language->get('language', 'viewing_ban', ['ban' => Output::getClean($ban->id)]),
			'BACK' => $language->get('general', 'back'),
			'BACK_LINK' => URL::build('/panel/tebex/bans'),
			'IGN' => $buycraft_language->get('language', 'ign'),
			'IGN_VALUE' => Output::getClean($ban->user_ign),
			'AVATAR' => AvatarSource::getAvatarFromUUID(Output::getClean($ban->uuid ?? $ban->user_ign)),
			'UUID' => $buycraft_language->get('language', 'uuid'),
			'UUID_VALUE' => Output::getClean($ban->uuid),
			'IP_ADDRESS' => $buycraft_language->get('language', 'ip_address'),
			'IP_ADDRESS_VALUE' => ($user->hasPermission('modcp.ip_lookup') ? Output::getClean($ban->ip) : '-'),
			'REASON' => $buycraft_language->get('language', 'reason'),
			'REASON_VALUE' => Output::getPurified(Output::getDecoded($ban->reason)),
			'DATE' => $buycraft_language->get('language', 'date'),
			'DATE_VALUE' => date('d M Y, H:i', $ban->time),
			'INFO' => $language->get('general', 'info'),
			'UNBAN_IN_BUYCRAFT' => $buycraft_language->get('language', 'remove_ban_in_buycraft')
		));

		$template_file = 'tebex/bans_view.tpl';

	} else {
		Redirect::to(URL::build('/panel/tebex/bans'));
	}

} else {
	// Get all bans
	$bans = DB::getInstance()->get('buycraft_bans', array('id', '<>', 0));

	if ($user->hasPermission('admincp.buycraft.bans.new')) {
		$smarty->assign(array(
			'NEW_BAN' => $buycraft_language->get('language', 'new_ban'),
			'NEW_BAN_LINK' => URL::build('/panel/tebex/bans/', 'action=new')
		));
	}

	if (!$bans->count()) {
		$smarty->assign(array(
			'NO_BANS' => $buycraft_language->get('language', 'no_bans')
		));

	} else {
		$template_array = array();
        $integration = Integrations::getInstance()->getIntegration('Minecraft');

		foreach ($bans->results() as $ban) {
            if (($banned_user = new IntegrationUser($integration, $ban->uuid, 'identifier'))->exists()) {
                $avatar = $banned_user->getUser()->getAvatar();
                $style = $banned_user->getUser()->getGroupStyle();
            } else {
                $avatar = AvatarSource::getAvatarFromUUID(Output::getClean($ban->uuid ?? $ban->user_ign));
                $style = '';
            }

			$template_array[] = array(
				'avatar' => $avatar,
				'style' => $style,
				'ign' => Output::getClean($ban->user_ign),
				'ip' => ($user->hasPermission('modcp.ip_lookup') ? Output::getClean($ban->ip) : '-'),
				'date' => date(DATE_FORMAT, $ban->time),
				'date_unix' => Output::getClean($ban->time),
				'link' => URL::build('/panel/tebex/bans/', 'action=view&id=' . Output::getClean($ban->id))
			);
		}

		$smarty->assign(array(
			'USER' => $buycraft_language->get('language', 'user'),
			'IP_ADDRESS' => $buycraft_language->get('language', 'ip_address'),
			'DATE' => $buycraft_language->get('language', 'date'),
			'VIEW' => $buycraft_language->get('language', 'view'),
			'BAN_LIST' => $template_array
		));

		if (!defined('TEMPLATE_BUYCRAFT_SUPPORT')) {
            $template->assets()->include([
                AssetTree::DATATABLES,
            ]);

			$template->addJSScript('
				$(document).ready(function() {
					$(\'.dataTables-bans\').dataTable({
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
	}

	$template_file = 'tebex/bans.tpl';
}

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'BUYCRAFT' => $buycraft_language->get('language', 'buycraft'),
	'PAGE' => PANEL_PAGE,
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit'),
	'BANS' => $buycraft_language->get('language', 'bans')
));

$template->onPageLoad();

require ROOT_PATH . '/core/templates/panel_navbar.php';

// Display template
$template->displayTemplate($template_file, $smarty);
