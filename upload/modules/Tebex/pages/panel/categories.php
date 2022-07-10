<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Tebex integration for NamelessMC - categories
 */

if (!$user->handlePanelPageLoad('admincp.buycraft.categories')) {
    require_once ROOT_PATH . '/403.php';
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'buycraft');
define('PANEL_PAGE', 'buycraft_categories');
$page_title = $buycraft_language->get('language', 'categories');
require_once ROOT_PATH . '/core/templates/backend_init.php';
require_once ROOT_PATH . '/modules/Tebex/classes/Buycraft.php';

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && is_numeric($_GET['id']) && $user->hasPermission('admincp.buycraft.categories.update')) {
	// Get category
	$category = DB::getInstance()->get('buycraft_categories', array('id', '=', $_GET['id']));

	if (!$category->count()) {
		Redirect::to(URL::build('/panel/tebex/categories'));
	}

	$category = $category->first();

	$errors = array();

	if (isset($_POST['description'])) {
		// Update description
		if (Token::check(Input::get('token'))) {
			$validation = Validate::check($_POST, [
				'description' => [
					Validate::MAX => 100000,
				]
			])->messages([
                'description' => [
                    Validate::MAX => $buycraft_language->get('language', 'description_max', ['count' => 100000])
                ]
            ]);

			if ($validation->passed()) {
				$category_description = DB::getInstance()->get('buycraft_categories_descriptions', array('category_id', '=', $category->id));
				if ($category_description->count()) {
					DB::getInstance()->update('buycraft_categories_descriptions', $category_description->first()->id, array(
						'description' => $_POST['description']
					));
				} else {
					DB::getInstance()->insert('buycraft_categories_descriptions', array(
						'category_id' => $category->id,
						'description' => $_POST['description']
					));
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
					->setName('c-' . $category->id)
					->setLocation(ROOT_PATH . '/uploads/store', 0777);

				if ($image['store_image']) {
					$upload = $image->upload();

					if ($upload) {
						$success = $buycraft_language->get('language', 'image_updated_successfully');

						$category_description = DB::getInstance()->get('buycraft_categories_descriptions', array('category_id', '=', $category->id));
						if ($category_description->count()) {
							DB::getInstance()->update('buycraft_categories_descriptions', $category_description->first()->id, array(
								'image' => $image->getName() . '.' . $image->getMime()
							));
						} else {
							DB::getInstance()->insert('buycraft_categories_descriptions', array(
								'category_id' => $category->id,
								'image' => $image->getName() . '.' . $image->getMime()
							));
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

if (isset($_GET['action']) && $_GET['action'] == 'edit' && $user->hasPermission('admincp.buycraft.categories.update')) {
	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		Redirect::to(URL::build('/panel/tebex/categories'));
	}

    $category = DB::getInstance()->query(
        <<<SQL
        SELECT
            c.id,
            c.name,
            d.description,
            d.image
        FROM
            nl2_buycraft_categories c
        LEFT JOIN
            nl2_buycraft_categories_descriptions d
        ON
            d.category_id = c.id
        WHERE
            c.id = ?
        SQL, [$_GET['id']]);

	if (!$category->count()) {
		Redirect::to(URL::build('/panel/tebex/categories'));
	}

	$category = $category->first();

    $description = EventHandler::executeEvent('renderTebexContentEdit', ['content' => $category->description ?? ''])['content'];

	$smarty->assign(array(
		'CATEGORY_NAME' => Output::getClean($category->name),
		'EDITING_CATEGORY' => $buycraft_language->get('language', 'editing_category', ['category' => Output::getClean($category->name)]),
		'CATEGORY_DESCRIPTION' => $buycraft_language->get('language', 'category_description'),
		'CATEGORY_IMAGE' => $buycraft_language->get('language', 'category_image'),
		'CATEGORY_IMAGE_VALUE' => (isset($category->image) ? (defined('CONFIG_PATH') ? CONFIG_PATH . '/' : '/' . 'uploads/store/' . Output::getClean(Output::getDecoded($category->image))) : null),
		'UPLOAD_NEW_IMAGE' => $buycraft_language->get('language', 'upload_new_image'),
		'BROWSE' => $language->get('general', 'browse'),
		'BACK' => $language->get('general', 'back'),
		'BACK_LINK' => URL::build('/panel/tebex/categories')
	));

    $template->assets()->include([
        AssetTree::TINYMCE,
    ]);

	$template->addJSScript(Input::createTinyEditor($language, 'inputDescription', $description));

	$template_file = 'tebex/categories_edit.tpl';

} else {
	// Get all categories
	$categories = DB::getInstance()->query('SELECT * FROM nl2_buycraft_categories WHERE parent_category IS NULL ORDER BY `order` ASC');
	$all_categories = array();

	if ($categories->count()) {
		$categories = $categories->results();

		foreach ($categories as $category) {
			$subcategories = DB::getInstance()->query('SELECT * FROM nl2_buycraft_categories WHERE parent_category = ? ORDER BY `order` ASC', [$category->id]);

			$new_category = array(
				'name' => Output::getClean($category->name),
				'subcategories' => []
			);

			if ($user->hasPermission('admincp.buycraft.categories.update')) {
				$new_category['edit_link'] = URL::build('/panel/tebex/categories/', 'action=edit&id=' . Output::getClean($category->id));
			}

			if ($subcategories->count()) {
				foreach ($subcategories->results() as $subcategory) {
					$new_subcategory = array(
						'name' => Output::getClean($subcategory->name)
					);

					if ($user->hasPermission('admincp.buycraft.categories.update')) {
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

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
