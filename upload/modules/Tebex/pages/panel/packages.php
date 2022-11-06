<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - packages
 */

if (!$user->handlePanelPageLoad('admincp.buycraft.packages')) {
    require_once ROOT_PATH . '/403.php';
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'buycraft');
define('PANEL_PAGE', 'buycraft_packages');
$page_title = $buycraft_language->get('language', 'packages');
require_once ROOT_PATH . '/core/templates/backend_init.php';
require_once ROOT_PATH . '/modules/Tebex/classes/Buycraft.php';

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && is_numeric($_GET['id']) && $user->hasPermission('admincp.buycraft.packages.update')) {
	// Get package
	$package = DB::getInstance()->get('buycraft_packages', ['id', '=', $_GET['id']]);

	if (!$package->count()) {
		Redirect::to(URL::build('/panel/tebex/packges'));
	}

	$package = $package->first();

	$errors = [];

	if (isset($_POST['description'])) {
		// Update description
		if (Token::check(Input::get('token'))) {
			$validation = Validate::check($_POST, [
				'description' => [
					Validate::MAX => 100000,
				],
			])->messages([
                'description' => [
                    Validate::MAX => $buycraft_language->get('language', 'description_max_100000', ['count' => 100000]),
                ],
            ]);

			if ($validation->passed()){
				$package_description = DB::getInstance()->get('buycraft_packages_descriptions', ['package_id', '=', $package->id]);
				if ($package_description->count()) {
					DB::getInstance()->update('buycraft_packages_descriptions', $package_description->first()->id, [
						'description' => Output::getClean($_POST['description'])
					]);
				} else {
					DB::getInstance()->insert('buycraft_packages_descriptions', [
						'package_id' => $package->id,
						'description' => Output::getClean($_POST['description'])
					]);
				}

				$success = $buycraft_language->get('language', 'description_updated_successfully');
			} else {
				$errors = $validation->errors();
			}
		} else {
			$errors[] = $language->get('general', 'invalid_token');
		}

	} else if (isset($_POST['type'])) {
		// Update image
		if (Token::check(Input::get('token'))) {
			if (!is_dir(ROOT_PATH . '/uploads/store')) {
				try {
					mkdir(ROOT_PATH . '/uploads/store');
				} catch (Exception $e) {
					$errors[] = $buycraft_language->get('language', 'unable_to_create_image_directory');
				}
			}

			if (!count($errors)) {
				$image = new \Bulletproof\Image($_FILES);

				$image->setSize(1000, 2 * 1048576)
					->setMime(array('jpeg', 'png', 'gif'))
					->setDimension(2000, 2000)
					->setName('p-' . $package->id)
					->setLocation(ROOT_PATH . '/uploads/store', 0777);

				if ($image['store_image']) {
					$upload = $image->upload();

					if ($upload) {
						$success = $buycraft_language->get('language', 'image_updated_successfully');

						$package_description = DB::getInstance()->get('buycraft_packages_descriptions', ['package_id', '=', $package->id]);
						if ($package_description->count()) {
							DB::getInstance()->update('buycraft_packages_descriptions', $package_description->first()->id, [
								'image' => Output::getClean($image->getName() . '.' . $image->getMime())
							]);
						} else {
							DB::getInstance()->insert('buycraft_packages_descriptions', [
								'package_id' => $package->id,
								'image' => Output::getClean($image->getName() . '.' . $image->getMime())
							]);
						}
					} else {
						$errors[] = $buycraft_language->get('language', 'unable_to_upload_image', ['error' => Output::getClean($image->getError())]);
					}
				}
			}
		} else {
			$errors[] = $language->get('general', 'invalid_token');
		}
	}
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

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

if (isset($_GET['action']) && $_GET['action'] == 'edit' && $user->hasPermission('admincp.buycraft.packages.update')) {
	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		Redirect::to(URL::build('/panel/tebex/packages'));
	}

    $package = DB::getInstance()->query(
        <<<SQL
        SELECT
            p.id,
            p.name,
            d.description,
            d.image
        FROM
            nl2_buycraft_packages p
        LEFT JOIN
            nl2_buycraft_packages_descriptions d
        ON
            d.package_id = p.id
        WHERE
            p.id = ?
        SQL, [$_GET['id']]);

	if (!$package->count()) {
		Redirect::to(URL::build('/panel/tebex/packages'));
	}

	$package = $package->first();

    $description = EventHandler::executeEvent('renderTebexContentEdit', ['content' => $package->description ?? ''])['content'];

    if (isset($package->image) && $package->image) {
        if (strpos($package->image, 'https') !== false) {
            $image = Output::getClean($package->image);
        } else {
            $image = (defined('CONFIG_PATH') ? CONFIG_PATH . '/' : '/') . 'uploads/store/' . Output::getClean($package->image);
        }
    } else {
        $image = null;
    }

	$smarty->assign(array(
		'PACKAGE_NAME' => Output::getClean(Output::getDecoded($package->name)),
		'EDITING_PACKAGE' => $buycraft_language->get('language', 'editing_package', ['package' => Output::getClean($package->name)]),
		'PACKAGE_DESCRIPTION' => $buycraft_language->get('language', 'package_description'),
		'PACKAGE_IMAGE' => $buycraft_language->get('language', 'package_image'),
		'PACKAGE_IMAGE_VALUE' => $image,
		'UPLOAD_NEW_IMAGE' => $buycraft_language->get('language', 'upload_new_image'),
		'BROWSE' => $language->get('general', 'browse'),
		'BACK' => $language->get('general', 'back'),
		'BACK_LINK' => URL::build('/panel/tebex/packages')
	));

    $template->assets()->include([
        AssetTree::TINYMCE,
    ]);

	$template->addJSScript(Input::createTinyEditor($language, 'inputDescription', $description));

	$template_file = 'tebex/packages_edit.tpl';

} else {
	// Get all categories
	$categories = DB::getInstance()->query('SELECT * FROM nl2_buycraft_categories ORDER BY `order` ASC');
	$all_categories = [];

	if ($categories->count()) {
		$categories = $categories->results();

		foreach ($categories as $category) {
			$new_category = array(
				'name' => Output::getClean($category->name),
				'packages' => []
			);

			$packages = DB::getInstance()->query('SELECT * FROM nl2_buycraft_packages WHERE category_id = ? ORDER BY `order` ASC', [$category->id]);

			if ($packages->count()) {
				$packages = $packages->results();

				foreach ($packages as $package) {
					$new_package = array(
						'id' => Output::getClean($package->id),
						'id_x' => $buycraft_language->get('language', 'id_x', ['id' => Output::getClean($package->id)]),
						'name' => Output::getClean($package->name),
						'price' => Output::getClean($package->price),
						'sale_discount' => Output::getClean($package->sale_discount)
					);

					if ($user->hasPermission('admincp.buycraft.packages.update')) {
						$new_package['edit_link'] = URL::build('/panel/tebex/packages/', 'action=edit&id=' . Output::getClean($package->id));
					}

					$new_category['packages'][] = $new_package;
				}
			}

			$all_categories[] = $new_category;
		}

		$currency = DB::getInstance()->get('buycraft_settings', ['name', '=', 'currency_iso']);
		if ($currency->count())
			$currency = Output::getPurified($currency->first()->value);
		else
			$currency = '';

		$smarty->assign(array(
			'ALL_CATEGORIES' => $all_categories,
			'CURRENCY' => $currency
		));

	} else {
		$smarty->assign('NO_PACKAGES', $buycraft_language->get('language', 'no_packages'));
	}

	$template_file = 'tebex/packages.tpl';
}

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'BUYCRAFT' => $buycraft_language->get('language', 'buycraft'),
	'PAGE' => PANEL_PAGE,
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit'),
	'PACKAGES' => $buycraft_language->get('language', 'packages')
));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
