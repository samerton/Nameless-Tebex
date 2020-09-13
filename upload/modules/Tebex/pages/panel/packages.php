<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - packages
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
			if(!$user->hasPermission('admincp.buycraft.packages')){
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
define('PANEL_PAGE', 'buycraft_packages');
$page_title = $buycraft_language->get('language', 'packages');
require_once(ROOT_PATH . '/core/templates/backend_init.php');
require_once(ROOT_PATH . '/modules/Tebex/classes/Buycraft.php');

if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && is_numeric($_GET['id']) && $user->hasPermission('admincp.buycraft.packages.update')){
	// Get package
	$package = $queries->getWhere('buycraft_packages', array('id', '=', $_GET['id']));

	if(!count($package)){
		Redirect::to(URL::build('/panel/tebex/packges'));
		die();
	}

	$package = $package[0];

	$errors = array();

	if(isset($_POST['description'])){
		// Update description
		if(Token::check(Input::get('token'))){
			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'description' => array(
					'max' => 100000
				)
			));

			if ($validation->passed()){
				$package_description = $queries->getWhere('buycraft_packages_descriptions', array('package_id', '=', $package->id));
				if(count($package_description)){
					$queries->update('buycraft_packages_descriptions', $package_description[0]->id, array(
						'description' => Output::getClean($_POST['description'])
					));
				} else {
					$queries->create('buycraft_packages_descriptions', array(
						'package_id' => $package->id,
						'description' => Output::getClean($_POST['description'])
					));
				}

				$success = $buycraft_language->get('language', 'description_updated_successfully');

			} else {
				$errors[] = $buycraft_language->get('language', 'description_max_100000');

			}
		} else {
			$errors[] = $language->get('general', 'invalid_token');
		}

	} else if(isset($_POST['type'])){
		// Update image
		if(Token::check(Input::get('token'))){
			if(!is_dir(ROOT_PATH . '/uploads/store')){
				try {
					mkdir(ROOT_PATH . '/uploads/store');
				} catch (Exception $e) {
					$errors[] = $buycraft_language->get('language', 'unable_to_create_image_directory');
				}
			}

			if(!count($errors)){
				require(ROOT_PATH . '/core/includes/bulletproof/bulletproof.php');

				$image = new Bulletproof\Image($_FILES);

				$image->setSize(1000, 2 * 1048576)
					->setMime(array('jpeg', 'png', 'gif'))
					->setDimension(2000, 2000)
					->setName('p-' . $package->id)
					->setLocation(ROOT_PATH . '/uploads/store', 0777);

				if($image['store_image']){
					$upload = $image->upload();

					if($upload){
						$success = $buycraft_language->get('language', 'image_updated_successfully');

						$package_description = $queries->getWhere('buycraft_packages_descriptions', array('package_id', '=', $package->id));
						if(count($package_description)){
							$queries->update('buycraft_packages_descriptions', $package_description[0]->id, array(
								'image' => Output::getClean($image->getName() . '.' . $image->getMime())
							));
						} else {
							$queries->create('buycraft_packages_descriptions', array(
								'package_id' => $package->id,
								'image' => Output::getClean($image->getName() . '.' . $image->getMime())
							));
						}

					} else {
						$errors[] = str_replace('{x}', Output::getClean($image->getError()), $buycraft_language->get('language', 'unable_to_upload_image'));

					}
				}
			}
		} else {
			$errors[] = $language->get('general', 'invalid_token');
		}

	}
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

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

if(isset($_GET['action']) && $_GET['action'] == 'edit' && $user->hasPermission('admincp.buycraft.packages.update')){
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
		Redirect::to(URL::build('/panel/tebex/packages'));
		die();
	}

	$package = $queries->getWhere('buycraft_packages', array('id', '=', Output::getClean($_GET['id'])));

	if(!count($package)){
		Redirect::to(URL::build('/panel/tebex/packages'));
		die();
	}

	$package = $package[0];

	$package_description = $queries->getWhere('buycraft_packages_descriptions', array('package_id', '=', $package->id));
	if(count($package_description))
		$package_description = $package_description[0];

	$smarty->assign(array(
		'PACKAGE_NAME' => Output::getClean(Output::getDecoded($package->name)),
		'EDITING_PACKAGE' => str_replace('{x}', Output::getClean($package->name), $buycraft_language->get('language', 'editing_package_x')),
		'PACKAGE_DESCRIPTION' => $buycraft_language->get('language', 'package_description'),
		'PACKAGE_DESCRIPTION_VALUE' => (isset($package_description->description) ? Output::getPurified(Output::getDecoded($package_description->description)) : ''),
		'PACKAGE_IMAGE' => $buycraft_language->get('language', 'package_image'),
		'PACKAGE_IMAGE_VALUE' => (isset($package_description->image) && !is_null($package_description->image) ? ((defined('CONFIG_PATH') ? CONFIG_PATH . '/' : '/') . 'uploads/store/' . Output::getClean(Output::getDecoded($package_description->image))) : null),
		'UPLOAD_NEW_IMAGE' => $buycraft_language->get('language', 'upload_new_image'),
		'BROWSE' => $language->get('general', 'browse'),
		'BACK' => $language->get('general', 'back'),
		'BACK_LINK' => URL::build('/panel/tebex/packages')
	));

	$template->addJSFiles(array(
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/ckeditor.js' => array()
	));

	$template->addJSScript(Input::createEditor('inputDescription'));

	$template_file = 'tebex/packages_edit.tpl';

} else {
	// Get all categories
	$categories = DB::getInstance()->query('SELECT * FROM nl2_buycraft_categories ORDER BY `order` ASC', array());
	$all_categories = [];

	if($categories->count()){
		$categories = $categories->results();

		foreach($categories as $category){
			$new_category = array(
				'name' => Output::getClean(Output::getDecoded($category->name)),
				'packages' => array()
			);

			$packages = DB::getInstance()->query('SELECT * FROM nl2_buycraft_packages WHERE category_id = ? ORDER BY `order` ASC', array(Output::getClean($category->id)));

			if($packages->count()){
				$packages = $packages->results();

				foreach($packages as $package){
					$new_package = array(
						'id' => Output::getClean($package->id),
						'id_x' => str_replace('{x}', Output::getClean($package->id), $buycraft_language->get('language', 'id_x')),
						'name' => Output::getClean($package->name),
						'price' => Output::getClean($package->price),
						'sale_discount' => Output::getClean($package->sale_discount)
					);

					if($user->hasPermission('admincp.buycraft.packages.update')){
						$new_package['edit_link'] = URL::build('/panel/tebex/packages/', 'action=edit&id=' . Output::getClean($package->id));
					}

					$new_category['packages'][] = $new_package;
				}
			}

			$all_categories[] = $new_category;
		}

		$currency = $queries->getWhere('buycraft_settings', array('name', '=', 'currency_symbol'));
		if(count($currency))
			$currency = Output::getPurified($currency[0]->value);
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

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
