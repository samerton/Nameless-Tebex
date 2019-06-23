<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - categories
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
			if(!$user->hasPermission('admincp.buycraft.categories')){
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
define('PANEL_PAGE', 'buycraft_categories');
$page_title = $buycraft_language->get('language', 'bans');
require_once(ROOT_PATH . '/core/templates/backend_init.php');
require_once(ROOT_PATH . '/modules/Tebex/classes/Buycraft.php');

if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && is_numeric($_GET['id']) && $user->hasPermission('admincp.buycraft.categories.update')){
	// Get category
	$category = $queries->getWhere('buycraft_categories', array('id', '=', $_GET['id']));

	if(!count($category)){
		Redirect::to(URL::build('/panel/tebex/categories'));
		die();
	}

	$category = $category[0];

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
				$category_description = $queries->getWhere('buycraft_categories_descriptions', array('category_id', '=', $category->id));
				if(count($category_description)){
					$queries->update('buycraft_categories_descriptions', $category_description[0]->id, array(
						'description' => Output::getClean($_POST['description'])
					));
				} else {
					$queries->create('buycraft_categories_descriptions', array(
						'category_id' => $category->id,
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
					->setName('c-' . $category->id)
					->setLocation(ROOT_PATH . '/uploads/store', 0777);

				if($image['store_image']){
					$upload = $image->upload();

					if($upload){
						$success = $buycraft_language->get('language', 'image_updated_successfully');

						$category_description = $queries->getWhere('buycraft_categories_descriptions', array('category_id', '=', $category->id));
						if(count($category_description)){
							$queries->update('buycraft_categories_descriptions', $category_description[0]->id, array(
								'image' => Output::getClean($image->getName() . '.' . $image->getMime())
							));
						} else {
							$queries->create('buycraft_categories_descriptions', array(
								'category_id' => $category->id,
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

if(isset($_GET['action']) && $_GET['action'] == 'edit' && $user->hasPermission('admincp.buycraft.categories.update')){
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
		Redirect::to(URL::build('/panel/tebex/categories'));
		die();
	}

	$category = $queries->getWhere('buycraft_categories', array('id', '=', Output::getClean($_GET['id'])));

	if(!count($category)){
		Redirect::to(URL::build('/panel/tebex/categories'));
		die();
	}

	$category = $category[0];

	$category_description = $queries->getWhere('buycraft_categories_descriptions', array('category_id', '=', $category->id));
	if(count($category_description))
		$category_description = $category_description[0];

	$smarty->assign(array(
		'CATEGORY_NAME' => Output::getClean(Output::getDecoded($category->name)),
		'EDITING_CATEGORY' => str_replace('{x}', Output::getClean($category->name), $buycraft_language->get('language', 'editing_category_x')),
		'CATEGORY_DESCRIPTION' => $buycraft_language->get('language', 'category_description'),
		'CATEGORY_DESCRIPTION_VALUE' => (isset($category_description->description) ? Output::getPurified(Output::getDecoded($category_description->description)) : ''),
		'CATEGORY_IMAGE' => $buycraft_language->get('language', 'category_image'),
		'CATEGORY_IMAGE_VALUE' => (isset($category_description->image) && !is_null($category_description->image) ? (defined('CONFIG_PATH') ? CONFIG_PATH . '/' : '/' . 'uploads/store/' . Output::getClean(Output::getDecoded($category_description->image))) : null),
		'UPLOAD_NEW_IMAGE' => $buycraft_language->get('language', 'upload_new_image'),
		'BROWSE' => $language->get('general', 'browse'),
		'BACK' => $language->get('general', 'back'),
		'BACK_LINK' => URL::build('/panel/tebex/categories')
	));

	$template->addJSFiles(array(
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js' => array(),
		(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/ckeditor.js' => array()
	));

	$template->addJSScript(Input::createEditor('inputDescription'));

	$template_file = 'tebex/categories_edit.tpl';

} else {
	// Get all categories
	$categories = DB::getInstance()->query('SELECT * FROM nl2_buycraft_categories WHERE parent_category IS NULL ORDER BY `order` ASC', array());
	$all_categories = array();

	if($categories->count()){
		$categories = $categories->results();

		foreach($categories as $category){
			$subcategories = $queries->orderWhere('buycraft_categories', 'parent_category = ' . Output::getClean($category->id), '`order`', 'ASC');

			$new_category = array(
				'name' => Output::getClean(Output::getDecoded($category->name)),
				'subcategories' => array()
			);

			if($user->hasPermission('admincp.buycraft.categories.update')){
				$new_category['edit_link'] = URL::build('/panel/tebex/categories/', 'action=edit&id=' . Output::getClean($category->id));
			}

			if(count($subcategories)){
				foreach($subcategories as $subcategory){
					$new_subcategory = array(
						'name' => Output::getClean(Output::getDecoded($subcategory->name))
					);

					if($user->hasPermission('admincp.buycraft.categories.update')){
						$new_subcategory['edit_link'] = URL::build('/panel/tebex/categories/', 'action=edit&id=' . Output::getClean($subcategory->id));
					}

					$new_category['subcategories'][] = $new_subcategory;
				}
			} else {
				$new_category['no_subcategories'] = $buycraft_language->get('language', 'no_subcategories');
			}

			$all_categories[] = $new_category;
		}

		$smarty->assign(array(
			'ALL_CATEGORIES' => $all_categories
		));

	} else {
		$smarty->assign('NO_CATEGORIES', $buycraft_language->get('language', 'no_categories'));
	}

	$template_file = 'tebex/categories.tpl';
}

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'BUYCRAFT' => $buycraft_language->get('language', 'buycraft'),
	'PAGE' => PANEL_PAGE,
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit'),
	'CATEGORIES' => $buycraft_language->get('language', 'categories')
));

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);