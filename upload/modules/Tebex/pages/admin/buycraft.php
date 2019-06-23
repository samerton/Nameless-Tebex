<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Buycraft integration for NamelessMC
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
            Redirect::to(URL::build('/admin/auth'));
            die();
        } else {
            if(!$user->hasPermission('admincp.buycraft')){
                Redirect::to(URL::build('/admin'));
                die();
            }
        }
    }
} else {
    // Not logged in
    Redirect::to(URL::build('/login'));
    die();
}

$page = 'admin';
$admin_page = 'buycraft';

if(!isset($_GET['view'])){
    if(isset($_POST) && !empty($_POST)){
        $errors = array();

        if(Token::check(Input::get('token'))){
            $validate = new Validate();

            $validation = $validate->check($_POST, array(
                'server_key' => array(
                    'min' => 40,
                    'max' => 40
                ),
				'store_content' => array(
					'max' => 100000
				)
            ));

            if($validation->passed()){
                if(isset($_POST['allow_guests']) && $_POST['allow_guests'] == 'on')
                    $allow_guests = 1;
                else
                    $allow_guests = 0;

                try {
                    $server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

                    if(count($server_key)){
                        $server_key = $server_key[0]->id;
                        $queries->update('buycraft_settings', $server_key, array(
                            'value' => Output::getClean(Input::get('server_key'))
                        ));
                    } else {
                        $queries->create('buycraft_settings', array(
                            'name' => 'server_key',
                            'value' => Output::getClean(Input::get('server_key'))
                        ));
                    }

                } catch(Exception $e){
                    $errors[] = $e->getMessage();
                }

                try {
                    $allow_guests_query = $queries->getWhere('buycraft_settings', array('name', '=', 'allow_guests'));

                    if(count($allow_guests_query)){
                        $allow_guests_query = $allow_guests_query[0]->id;
                        $queries->update('buycraft_settings', $allow_guests_query, array(
                            'value' => $allow_guests
                        ));
                    } else {
                        $queries->create('buycraft_settings', array(
                            'name' => 'allow_guests',
                            'value' => $allow_guests
                        ));
                    }

                } catch(Exception $e){
                    $errors[] = $e->getMessage();
                }

	            try {
		            $store_index_content = $queries->getWhere('buycraft_settings', array('name', '=', 'store_content'));

		            if(count($store_index_content)){
			            $store_index_content = $store_index_content[0]->id;
			            $queries->update('buycraft_settings', $store_index_content, array(
				            'value' => Output::getClean(Input::get('store_content'))
			            ));
		            } else {
			            $queries->create('buycraft_settings', array(
				            'name' => 'store_content',
				            'value' => Output::getClean(Input::get('store_content'))
			            ));
		            }

	            } catch(Exception $e){
		            $errors[] = $e->getMessage();
	            }

	            try {
		            $store_path = $queries->getWhere('buycraft_settings', array('name', '=', 'store_path'));

		            if(isset($_POST['store_path']) && strlen(str_replace(' ', '', $_POST['store_path'])) > 0)
		            	$store_path_input = rtrim(Output::getClean($_POST['store_path']), '/');
		            else
		            	$store_path_input = '/store';

		            if(count($store_path)){
			            $store_path = $store_path[0]->id;
			            $queries->update('buycraft_settings', $store_path, array(
				            'value' => $store_path_input
			            ));
		            } else {
			            $queries->create('buycraft_settings', array(
				            'name' => 'store_path',
				            'value' => $store_path_input
			            ));
		            }

		            $cache->setCache('buycraft_settings');
		            $cache->store('buycraft_url', $store_path_input1);

	            } catch(Exception $e){
		            $errors[] = $e->getMessage();
	            }

                if(!count($errors))
                    $success = $buycraft_language->get('language', 'updated_successfully');

            } else {
            	foreach($validation->errors() as $error){
            		if(strpos($error, 'server_key') !== false)
		            	$errors[] = $buycraft_language->get('language', 'invalid_server_key');
            		else
            			$errors[] = $buycraft_language->get('language', 'store_content_max');
				}
			}

        } else
            $errors[] = $language->get('general', 'invalid_token');
    }
}

?>
<!DOCTYPE html>
<html lang="<?php echo(defined('HTML_LANG') ? HTML_LANG : 'en'); ?>" <?php if(defined('HTML_RTL') && HTML_RTL === true) echo ' dir="rtl"'; ?>>
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <?php
    $title = $language->get('admin', 'admin_cp');
    require(ROOT_PATH . '/core/templates/admin_header.php');
    ?>

    <link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.css">

</head>
<body>
<?php require(ROOT_PATH . '/modules/Core/pages/admin/navbar.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <?php require(ROOT_PATH . '/modules/Core/pages/admin/sidebar.php'); ?>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-block">
                    <h3 style="display:inline;"><?php echo $buycraft_language->get('language', 'buycraft'); ?></h3>

                    <a href="<?php echo URL::build('/admin/buycraft/sync'); ?>" class="btn btn-primary float-right"><?php echo $buycraft_language->get('language', 'force_sync'); ?></a>

                    <hr />

                    <ul class="nav nav-pills flex-column flex-xl-row">
                        <li class="nav-item">
                            <a class="nav-link<?php if(!isset($_GET['view'])) echo ' active'; ?>" href="<?php echo URL::build('/admin/buycraft'); ?>"><?php echo $buycraft_language->get('language', 'settings'); ?></a>
                        </li>
                        <?php if($user->hasPermission('admincp.buycraft.categories')){ ?>
                        <li class="nav-item">
                            <a class="nav-link<?php if(isset($_GET['view']) && $_GET['view'] == 'categories') echo ' active'; ?>" href="<?php echo URL::build('/admin/buycraft/', 'view=categories'); ?>"><?php echo $buycraft_language->get('language', 'categories'); ?></a>
                        </li>
                        <?php
                        }
                        if($user->hasPermission('admincp.buycraft.packages')){
                        ?>
                        <li class="nav-item">
                            <a class="nav-link<?php if(isset($_GET['view']) && $_GET['view'] == 'packages') echo ' active'; ?>" href="<?php echo URL::build('/admin/buycraft/', 'view=packages'); ?>"><?php echo $buycraft_language->get('language', 'packages'); ?></a>
                        </li>
                        <?php
                        }
                        if($user->hasPermission('admincp.buycraft.payments')){
                        ?>
                        <li class="nav-item">
                            <a class="nav-link<?php if(isset($_GET['view']) && $_GET['view'] == 'payments') echo ' active'; ?>" href="<?php echo URL::build('/admin/buycraft/', 'view=payments'); ?>"><?php echo $buycraft_language->get('language', 'payments'); ?></a>
                        </li>
                        <?php
                        }
                        if($user->hasPermission('admincp.buycraft.bans')){
                        ?>
                        <li class="nav-item">
                            <a class="nav-link<?php if(isset($_GET['view']) && $_GET['view'] == 'bans') echo ' active'; ?>" href="<?php echo URL::build('/admin/buycraft/', 'view=bans'); ?>"><?php echo $buycraft_language->get('language', 'bans'); ?></a>
                        </li>
                        <?php
                        }
                        if($user->hasPermission('admincp.buycraft.coupons')){
                        ?>
                        <li class="nav-item">
                            <a class="nav-link<?php if(isset($_GET['view']) && $_GET['view'] == 'coupons') echo ' active'; ?>" href="<?php echo URL::build('/admin/buycraft/', 'view=coupons'); ?>"><?php echo $buycraft_language->get('language', 'coupons'); ?></a>
                        </li>
                        <?php
                        }
                        if($user->hasPermission('admincp.buycraft.giftcards')){
                        ?>
                        <li class="nav-item">
                            <a class="nav-link<?php if(isset($_GET['view']) && $_GET['view'] == 'giftcards') echo ' active'; ?>" href="<?php echo URL::build('/admin/buycraft/', 'view=giftcards'); ?>"><?php echo $buycraft_language->get('language', 'gift_cards'); ?></a>
                        </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" href="https://server.tebex.io/dashboard" target="_blank" rel="noopener nofollow"><?php echo $buycraft_language->get('language', 'buycraft'); ?></a>
                        </li>
                    </ul>

                    <br />

                    <?php
                    if(isset($errors) && !empty($errors)){
                        echo '<div class="alert alert-danger"><ul style="margin-bottom:0">';
                        foreach($errors as $error)
                            echo '<li>' . $error . '</li>';
                        echo '</ul></div>';
                    }
                    if(isset($success))
                        echo '<div class="alert alert-success">' . $success . '</div>';
                    ?>

                    <?php if(!isset($_GET['view'])){ ?>
                        <h5 style="display:inline;"><?php echo $buycraft_language->get('language', 'settings'); ?></h5>
                        <hr />
                        <?php
                        if($user->hasPermission('admincp.buycraft.settings')){
                            $server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

                            if(count($server_key))
                                $server_key = Output::getClean($server_key[0]->value);
                            else
                                $server_key = '';

                            $allow_guests = $queries->getWhere('buycraft_settings', array('name', '=', 'allow_guests'));

                            if(count($allow_guests))
                                $allow_guests = $allow_guests[0]->value;
                            else
                                $allow_guests = 0;

                            $store_index_content = $queries->getWhere('buycraft_settings', array('name', '=', 'store_content'));
                            if(count($store_index_content)){
                            	$store_index_content = $store_index_content[0]->value;
							} else {
                            	$store_index_content = '';
							}

							$store_path = $queries->getWhere('buycraft_settings', array('name', '=', 'store_path'));
                            if(count($store_path)){
                            	$store_path = Output::getClean($store_path[0]->value);
							} else {
                            	$store_path = '/store';
							}

                            ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="inputSecretKey"><?php echo $buycraft_language->get('language', 'server_key'); ?></label>
                            <span class="badge badge-info"><i class="fa fa-question" data-container="body" data-html="true" rel="popover" data-placement="top" title="<?php echo $language->get('general', 'info'); ?>" data-content="<?php echo $buycraft_language->get('language', 'server_key_info'); ?>"></i></span>
                            <input id="inputSecretKey" name="server_key" class="form-control" placeholder="<?php echo $buycraft_language->get('language', 'server_key'); ?>" value="<?php echo $server_key; ?>">
                        </div>

                        <div class="form-group">
                            <label for="inputAllowGuests"><?php echo $buycraft_language->get('language', 'allow_guests'); ?></label>
                            <input type="checkbox" name="allow_guests" id="inputAllowGuests" class="js-switch" <?php if($allow_guests == 1) echo 'checked '; ?>/>
                        </div>

						<div class="form-group">
							<label for="inputStorePath"><?php echo $buycraft_language->get('language', 'store_path'); ?></label>
							<input type="text" class="form-control" id="inputStorePath" name="store_path" placeholder="<?php echo $buycraft_language->get('language', 'store_path'); ?>" value="<?php echo $store_path; ?>">
						</div>

						<div class="form-group">
							<label for="inputStoreContent"><?php echo $buycraft_language->get('language', 'store_index_content'); ?></label>
							<textarea id="inputStoreContent" name="store_content"><?php echo $store_index_content; ?></textarea>
						</div>

                        <div class="form-group">
                            <input type="hidden" name="token" value="<?php echo Token::get(); ?>">
                            <input type="submit" value="<?php echo $language->get('general', 'submit'); ?>" class="btn btn-primary">
                        </div>
                    </form>
                            <?php } ?>

                    <?php
                    } else {
                        switch($_GET['view']){
                            case 'categories':
                                if(!$user->hasPermission('admincp.buycraft.categories')){
                                    Redirect::to(URL::build('/admin'));
                                    die();
                                }

                                if(isset($_GET['edit']) && is_numeric($_GET['edit']) && $user->hasPermission('admincp.buycraft.categories.update')){
                                    // Get category
                                    $category = $queries->getWhere('buycraft_categories', array('id', '=', $_GET['edit']));

                                    if(!count($category)){
                                        Redirect::to(URL::build('/admin/buycraft/', 'view=categories'));
                                        die();
                                    }

                                    $category = $category[0];

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
                                                $error = $buycraft_language->get('language', 'description_max_100000');

                                            }
                                        } else {
                                            $error = $language->get('general', 'invalid_token');
                                        }

                                    } else if(isset($_POST['type'])){
                                        // Update image
                                        if(Token::check(Input::get('token'))){
                                            if(!is_dir(ROOT_PATH . '/uploads/store')){
                                                try {
                                                    mkdir(ROOT_PATH . '/uploads/store');
                                                } catch (Exception $e) {
                                                    $error = $buycraft_language->get('language', 'unable_to_create_image_directory');
                                                }
                                            }

                                            if(!isset($error)){
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
                                                        $error = str_replace('{x}', Output::getClean($image->getError()), $buycraft_language->get('language', 'unable_to_upload_image'));

                                                    }
                                                }
                                            }
                                        } else {
                                            $error = $language->get('general', 'invalid_token');
                                        }

                                    }

                                    $category_description = $queries->getWhere('buycraft_categories_descriptions', array('category_id', '=', $category->id));
                                    if(count($category_description))
                                        $category_description = $category_description[0];

                                    echo '<h5 style="display:inline">' . str_replace('{x}', Output::getClean($category->name), $buycraft_language->get('language', 'editing_category_x')) . '</h5>';
                                    echo '<span class="float-md-right"><a class="btn btn-danger" href="' . URL::build('/admin/buycraft/', 'view=categories') . '" onclick="return confirm(\'' . $language->get('general', 'confirm_cancel') . '\');">' . $language->get('general', 'cancel') . '</a></span>';
                                    echo '<hr />';

                                    if(isset($success))
                                        echo '<div class="alert alert-success">' . $success . '</div>';

                                    if(isset($error))
                                        echo '<div class="alert alert-danger">' . $error . '</div>';

                                    ?>
                                    <form action="" method="post">
                                        <div class="form-group">
                                            <strong><label for="inputDescription"><?php echo $buycraft_language->get('language', 'category_description'); ?></label></strong>
                                            <textarea id="inputDescription" name="description"><?php if(isset($category_description->description)) echo Output::getPurified(htmlspecialchars_decode($category_description->description)); ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <input type="hidden" name="token" value="<?php echo Token::get(); ?>">
                                            <input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
                                        </div>
                                    </form>

                                    <form action="" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <strong><?php echo $buycraft_language->get('language', 'category_image'); ?></strong><br />
                                            <?php if(isset($category_description->image) && !is_null($category_description->image)){ ?>
                                                <img src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>uploads/store/<?php echo Output::getClean($category_description->image); ?>" style="max-height:200px;max-width:200px;"><br />
                                            <?php } ?>
                                            <strong><?php echo $buycraft_language->get('language', 'upload_new_image'); ?></strong><br />
                                            <label class="btn btn-secondary">
                                                <?php echo $language->get('general', 'browse'); ?> <input type="file" name="store_image" hidden/>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="token" value="<?php echo Token::get(); ?>">
                                            <input type="hidden" name="type" value="image">
                                            <input type="submit" value="<?php echo $language->get('general', 'submit'); ?>" class="btn btn-primary">
                                        </div>
                                    </form>
                                    <?php

                                } else {
                                    echo '<h5 style="display:inline;">' . $buycraft_language->get('language', 'categories') . '</h5><hr />';
                                    $categories = DB::getInstance()->query('SELECT * FROM nl2_buycraft_categories WHERE parent_category IS NULL ORDER BY `order` ASC', array());
                                    if($categories->count()){
                                        $categories = $categories->results();

                                        foreach($categories as $category){
                                            $i = 0;
                                            $subcategories = $queries->orderWhere('buycraft_categories', 'parent_category = ' . $category->id, '`order`', 'ASC');

                                            echo '<div class="card card-default"><div class="card-header">';
                                            echo Output::getClean(htmlspecialchars_decode($category->name));

                                            if($user->hasPermission('admincp.buycraft.categories.update'))
                                                echo '<span class="float-right"><a class="btn btn-info btn-sm" href="' . URL::build('/admin/buycraft/', 'view=categories&amp;edit=' . $category->id) . '"><i class="fa fa-pencil"></i></a></span>';

                                            echo '</div><div class="card-block">';

                                            if(count($subcategories)){
                                                foreach($subcategories as $subcategory){
                                                    echo Output::getClean($subcategory->name);

                                                    if($user->hasPermission('admincp.buycraft.categories.update'))
                                                        echo '<span class="float-right"><a class="btn btn-info btn-sm" href="' . URL::build('/admin/buycraft/', 'view=categories&amp;edit=' . $subcategory->id) . '"><i class="fa fa-pencil"></i></a></span>';

                                                    if($i++ < count($subcategories) - 1)
                                                        echo '<hr />';
                                                }
                                            } else {
                                                echo $buycraft_language->get('language', 'no_subcategories');
                                            }

                                            echo '</div></div><br />';
                                        }
                                    } else {
                                        echo '<div class="alert alert-info">' . $buycraft_language->get('language', 'no_categories') . '</div>';
                                    }
                                }

                                break;
                            case 'packages':
                                if(!$user->hasPermission('admincp.buycraft.packages')){
                                    Redirect::to(URL::build('/admin'));
                                    die();
                                }

                                if(isset($_GET['edit']) && is_numeric($_GET['edit']) && $user->hasPermission('admincp.buycraft.packages.update')){
                                    // Get package
                                    $package = $queries->getWhere('buycraft_packages', array('id', '=', $_GET['edit']));

                                    if(!count($package)){
                                        Redirect::to(URL::build('/admin/buycraft/', 'view=packages'));
                                        die();
                                    }

                                    $package = $package[0];

                                    if(isset($_POST['description'])){
                                        // Update description
                                        if(Token::check(Input::get('token'))) {
                                            $validate = new Validate();
                                            $validation = $validate->check($_POST, array(
                                                'description' => array(
                                                    'max' => 100000
                                                )
                                            ));

                                            if($validation->passed()){
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
                                                $error = $buycraft_language->get('language', 'description_max_100000');

                                            }
                                        } else {
                                            $error = $language->get('general', 'invalid_token');
                                        }

                                    } else if(isset($_POST['type'])){
                                        // Update image
                                        if(Token::check(Input::get('token'))){
                                            if(!is_dir(ROOT_PATH . '/uploads/store')){
                                                try {
                                                    mkdir(ROOT_PATH . '/uploads/store');
                                                } catch (Exception $e) {
                                                    $error = $buycraft_language->get('language', 'unable_to_create_image_directory');
                                                }
                                            }

                                            if(!isset($error)){
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
                                                        $error = str_replace('{x}', Output::getClean($image->getError()), $buycraft_language->get('language', 'unable_to_upload_image'));

                                                    }
                                                }
                                            }
                                        } else {
                                            $error = $language->get('general', 'invalid_token');
                                        }

                                    }

                                    $package_description = $queries->getWhere('buycraft_packages_descriptions', array('package_id', '=', $package->id));
                                    if(count($package_description))
                                        $package_description = $package_description[0];

                                    echo '<h5 style="display:inline">' . str_replace('{x}', Output::getClean($package->name), $buycraft_language->get('language', 'editing_package_x')) . '</h5>';
                                    echo '<span class="float-md-right"><a class="btn btn-danger" href="' . URL::build('/admin/buycraft/', 'view=packages') . '" onclick="return confirm(\'' . $language->get('general', 'confirm_cancel') . '\');">' . $language->get('general', 'cancel') . '</a></span>';
                                    echo '<hr />';

                                    if(isset($success))
                                        echo '<div class="alert alert-success">' . $success . '</div>';

                                    if(isset($error))
                                        echo '<div class="alert alert-danger">' . $error . '</div>';

                                    ?>
                                    <form action="" method="post">
                                        <div class="form-group">
                                            <strong><label for="inputDescription"><?php echo $buycraft_language->get('language', 'package_description'); ?></label></strong>
                                            <textarea id="inputDescription" name="description"><?php if(isset($package_description->description)) echo Output::getPurified(htmlspecialchars_decode($package_description->description)); ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <input type="hidden" name="token" value="<?php echo Token::get(); ?>">
                                            <input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
                                        </div>
                                    </form>

                                    <form action="" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <strong><?php echo $buycraft_language->get('language', 'package_image'); ?></strong><br />
                                            <?php if(isset($package_description->image) && !is_null($package_description->image)){ ?>
                                                <img src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>uploads/store/<?php echo Output::getClean($package_description->image); ?>" style="max-height:200px;max-width:200px;"><br />
                                            <?php } ?>
                                            <strong><?php echo $buycraft_language->get('language', 'upload_new_image'); ?></strong><br />
                                            <label class="btn btn-secondary">
                                                <?php echo $language->get('general', 'browse'); ?> <input type="file" name="store_image" hidden/>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="token" value="<?php echo Token::get(); ?>">
                                            <input type="hidden" name="type" value="image">
                                            <input type="submit" value="<?php echo $language->get('general', 'submit'); ?>" class="btn btn-primary">
                                        </div>
                                    </form>
                                    <?php

                                } else {
                                    echo '<h5 style="display:inline;">' . $buycraft_language->get('language', 'packages') . '</h5><hr />';
                                    $categories = DB::getInstance()->query('SELECT * FROM nl2_buycraft_categories ORDER BY `order` ASC', array());
                                    if($categories->count()){
                                        $categories = $categories->results();

                                        foreach($categories as $category){
                                            $i = 0;
                                            $packages = $queries->orderWhere('buycraft_packages', 'category_id = ' . $category->id, '`order`', 'ASC');

                                            if(count($packages)){
                                                echo '<div class="card card-default"><div class="card-header">';
                                                echo Output::getClean(htmlspecialchars_decode($category->name));
                                                echo '</div><div class="card-block">';

                                                foreach($packages as $package){
                                                    echo Output::getClean($package->name);

                                                    if($user->hasPermission('admincp.buycraft.packages.update'))
                                                        echo '<span class="float-right"><a class="btn btn-info btn-sm" href="' . URL::build('/admin/buycraft/', 'view=packages&amp;edit=' . $package->id) . '"><i class="fa fa-pencil"></i></a></span>';

                                                    if($i++ < count($packages) - 1)
                                                        echo '<hr />';
                                                }

                                                echo '</div></div><br />';
                                            }
                                        }
                                    } else {
                                        echo '<div class="alert alert-info">' . $buycraft_language->get('language', 'no_packages') . '</div>';
                                    }
                                }

                                break;

                            case 'payments':
                                if(!$user->hasPermission('admincp.buycraft.payments')){
                                    Redirect::to(URL::build('/admin'));
                                    die();
                                }

                                if(isset($_GET['user'])){
                                    echo '<h5 style="display:inline;">' . str_replace('{x}', Output::getClean($_GET['user']), $buycraft_language->get('language', 'viewing_payments_for_user_x')) . '</h5>';
                                    echo '<a class="btn btn-info float-xl-right" href="' . URL::build('/admin/buycraft', 'view=payments') . '">' . $language->get('general', 'back') . '</a><hr />';

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
                                        ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered dataTables-payments">
                                                <colgroup>
                                                    <col span="1" style="width: 40%;">
                                                    <col span="1" style="width: 25%;">
                                                    <col span="1" style="width: 20%">
                                                    <col span="1" style="width: 15%">
                                                </colgroup>
                                                <thead>
                                                <tr>
                                                    <th><?php echo $buycraft_language->get('language', 'user'); ?></th>
                                                    <th><?php echo $buycraft_language->get('language', 'amount'); ?></th>
                                                    <th><?php echo $buycraft_language->get('language', 'date'); ?></th>
                                                    <th><?php echo $buycraft_language->get('language', 'view'); ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($payments as $payment){
                                                    ?>
                                                    <tr>
                                                        <td><a href="<?php echo URL::build('/admin/buycraft/', 'view=payments&amp;user=' . Output::getClean($payment->player_uuid)); ?>" style="<?php echo $style; ?>"><img src="<?php echo $avatar; ?>" class="rounded" style="max-width:32px;max-height:32px;" alt="<?php echo Output::getClean($payment->player_name); ?>" /> <?php echo Output::getClean($payment->player_name); ?></td>
                                                        <td><?php echo Output::getPurified($payment->currency_symbol) . Output::getClean($payment->amount); ?></td>
                                                        <td><?php echo date('d M Y, H:i', $payment->date); ?></td>
                                                        <td>
                                                            <a href="<?php echo URL::build('/admin/buycraft/', 'view=payments&amp;payment=' . Output::getClean($payment->id)); ?>"
                                                               class="btn btn-primary btn-sm"><?php echo $buycraft_language->get('language', 'view'); ?></a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                    } else {
                                        echo '<div class="alert alert-info">' . $buycraft_language->get('language', 'no_payments_for_user') . '</div>';
                                    }


                                } else if(isset($_GET['payment'])){
                                    // View payment
									$payment = $queries->getWhere('buycraft_payments', array('id', '=', $_GET['payment']));
									if(count($payment))
										$payment = $payment[0];
									else {
										Redirect::to(URL::build('/admin/buycraft/', 'view=payments'));
										die();
									}

									$commands = $queries->getWhere('buycraft_commands', array('payment', '=', $payment->id));

									echo '<h5 style="display:inline;">' . str_replace('{x}', Output::getClean($payment->id), $buycraft_language->get('language', 'viewing_payment')) . '</h5>';
									echo '<a class="btn btn-info float-lg-right" href="' . URL::build('/admin/buycraft/', 'view=payments') . '">' . $language->get('general', 'back') . '</a>';
									echo '<hr />';

									?>

									<div class="table-responsive">
										<table class="table table-striped">
											<col width="50%"></col>
											<col width="50%"></col>
											<tbody>
											<tr>
												<td><strong><?php echo $buycraft_language->get('language', 'ign'); ?></strong></td>
												<td><?php echo '<img src="' . Util::getAvatarFromUUID(Output::getClean($payment->player_uuid)) . '" class="rounded" style="max-height:32px;max-width:32px;" alt="' . Output::getClean($payment->player_name) . '"> ' . Output::getClean($payment->player_name); ?></td>
											</tr>
											<tr>
												<td><strong><?php echo $buycraft_language->get('language', 'uuid'); ?></strong></td>
												<td><?php echo Output::getClean($payment->player_uuid); ?></td>
											</tr>
											<tr>
												<td><strong><?php echo $buycraft_language->get('language', 'price'); ?></strong></td>
												<td><?php echo Output::getClean($payment->currency_symbol) . Output::getClean($payment->amount) . ' (' . Output::getClean($payment->currency_iso) . ')'; ?></td>
											</tr>
											<tr>
												<td><strong><?php echo $buycraft_language->get('language', 'date'); ?></strong></td>
												<td><?php echo date('d M Y, H:i', $payment->date); ?></td>
											</tr>
											</tbody>
										</table>
									</div>

									<?php
									echo '<hr />';
									echo '<h3>' . $buycraft_language->get('language', 'pending_commands') . '</h3>';

									if(count($commands)){
										echo '<div class="table-responsive">';
										echo '<table class="table table-striped">';
										foreach($commands as $command){
											echo '<tr><td> ' . Output::getClean($command->command) . '</td></tr>';
										}
										echo '</table>';
										echo '</div>';
									} else {
										echo '<div class="alert alert-info">' . $buycraft_language->get('language', 'no_pending_commands') . '</div>';
									}

                                } else if(isset($_GET['action'])){
                                    if($_GET['action'] == 'create'){
                                        // New payment
                                        echo '<h5 style="display:inline;">' . $buycraft_language->get('language', 'new_payment') . '</h5>';
                                        echo '<a class="btn btn-danger float-lg-right" href="' . URL::build('/admin/buycraft/', 'view=payments') . '" onclick="return confirm(\'' . $language->get('general', 'confirm_cancel') . '\');">' . $language->get('general', 'cancel') . '</a><hr />';

                                        if(isset($_GET['step'])){
                                            if($_GET['step'] == 2){
                                                if(!isset($_SESSION['bc_payment_ign']) || !isset($_SESSION['bc_payment_package'])){
                                                    Redirect::to(URL::build('/admin/buycraft/', 'view=payments&action=create'));
                                                    die();
                                                }

                                                // Ensure package exists
                                                $package = $queries->getWhere('buycraft_packages', array('id', '=', $_SESSION['bc_payment_package']));
                                                if(!count($package)){
                                                    Redirect::to(URL::build('/admin/buycraft/', 'view=payments&action=create'));
                                                    die();

                                                }
                                                $package = $package[0];

                                                // Get server key
                                                $server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

                                                if(count($server_key))
                                                    $server_key = $server_key[0]->value;
                                                else
                                                    $server_key = null;

                                                if(Input::exists()){
                                                    if(Token::check(Input::get('token'))){
                                                        if(isset($_POST['bc_payment_price']) && strlen($_POST['bc_payment_price']) > 0){
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
                                                                $error = Output::getClean($result->error_code . ': ' . $result->error_message);
                                                            } else {
                                                                Buycraft::updatePayments($server_key, null, DB::getInstance());
                                                                Session::flash('buycraft_payment_success', $buycraft_language->get('language', 'payment_created_successfully'));
                                                                Redirect::to(URL::build('/admin/buycraft/', 'view=payments'));
                                                                die();
                                                            }

                                                        } else
                                                            $error = $buycraft_language->get('language', 'please_enter_valid_price');

                                                    } else
                                                        $error = $language->get('general', 'invalid_token');
                                                }

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
                                                    echo '<div class="alert alert-danger">' . $error . '</div>';

                                                if(!isset($server_key_error)){
                                                    ?>
                                                    <form action="" method="post">
                                                        <div class="form-group">
                                                            <label for="inputPrice"><?php echo $buycraft_language->get('language', 'price'); ?></label>
                                                            <input id="inputPrice" name="bc_payment_price" class="form-control" type="number" step="0.01" value="<?php echo Output::getClean($package->price); ?>">
                                                        </div>
                                                        <?php
                                                        if(count($package_fields)){
                                                            foreach($package_fields as $field){
                                                                ?>
                                                                <div class="form-group">
                                                                    <?php
                                                                    switch($field->type){
                                                                        case 'numeric':
                                                                            ?>
                                                                            <div class="form-group">
                                                                                <label for="input<?php echo Output::getClean($field->id); ?>"><?php echo Output::getClean(ucfirst($field->name)); ?></label><br />
                                                                                <?php if(isset($field->description) && strlen($field->description) > 0) echo Output::getClean($field->description); ?>
                                                                                <input <?php if($field->id == 'price') echo 'disabled '; ?>id="input<?php echo Output::getClean($field->id); ?>" name="<?php echo Output::getClean($field->name); ?>" class="form-control" type="number" step="0.01">
                                                                            </div>
                                                                            <?php
                                                                            break;

                                                                        case 'dropdown':
                                                                            ?>
                                                                            <div class="form-group">
                                                                                <label for="input<?php echo Output::getClean($field->id); ?>"><?php echo Output::getClean(ucfirst($field->name)); ?></label><br />
                                                                                <?php if(isset($field->description) && strlen($field->description) > 0) echo Output::getClean($field->description); ?>
                                                                                <select class="form-control" id="input<?php echo Output::getClean($field->id); ?>" name="<?php echo Output::getClean($field->name); ?>">
                                                                                    <?php
                                                                                    foreach($field->options as $option){
                                                                                        echo '<option value="' . Output::getClean($option->id) . '">' . Output::getClean($option->label) . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                            <?php
                                                                            break;

                                                                        case 'text':
                                                                            ?>
                                                                            <div class="form-group">
                                                                                <label for="input<?php echo Output::getClean($field->id); ?>"><?php echo Output::getClean(ucfirst($field->name)); ?></label><br />
                                                                                <?php if(isset($field->description) && strlen($field->description) > 0) echo Output::getClean($field->description); ?>
                                                                                <textarea id="input<?php echo Output::getClean($field->id); ?>" name="<?php echo Output::getClean($field->name); ?>" class="form-control"></textarea>
                                                                            </div>
                                                                            <?php
                                                                            break;

                                                                        case 'alpha':
                                                                            ?>
                                                                            <div class="form-group">
                                                                                <label for="input<?php echo Output::getClean($field->id); ?>"><?php echo Output::getClean(ucfirst($field->name)); ?></label><br />
                                                                                <?php if(isset($field->description) && strlen($field->description) > 0) echo Output::getClean($field->description); ?>
                                                                                <input id="input<?php echo Output::getClean($field->id); ?>" name="<?php echo Output::getClean($field->name); ?>" class="form-control" type="text" pattern="([A-z0-9À-ž\s]){<?php echo Output::getClean($field->rules->min_length); ?>,}">
                                                                            </div>
                                                                            <?php
                                                                            break;

                                                                        case 'username':
                                                                            ?>
                                                                            <div class="form-group">
                                                                                <label for="input<?php echo Output::getClean($field->id); ?>"><?php echo Output::getClean(ucfirst($field->name)); ?></label><br />
                                                                                <?php if(isset($field->description) && strlen($field->description) > 0) echo Output::getClean($field->description); ?>
                                                                                <input id="input<?php echo Output::getClean($field->id); ?>" name="<?php echo Output::getClean($field->name); ?>" class="form-control" type="text">
                                                                            </div>
                                                                            <?php
                                                                            break;

                                                                        case 'email':
                                                                            ?>
                                                                            <div class="form-group">
                                                                                <label for="input<?php echo Output::getClean($field->id); ?>"><?php echo Output::getClean(ucfirst($field->name)); ?></label><br />
                                                                                <?php if(isset($field->description) && strlen($field->description) > 0) echo Output::getClean($field->description); ?>
                                                                                <input id="input<?php echo Output::getClean($field->id); ?>" name="<?php echo Output::getClean($field->name); ?>" class="form-control" type="email">
                                                                            </div>
                                                                            <?php
                                                                            break;
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                        <div class="form-group">
                                                            <input type="hidden" name="token" value="<?php echo Token::get(); ?>">
                                                            <input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
                                                        </div>
                                                    </form>
                                                    <?php
                                                }

                                            }
                                        } else {
                                            // Check input
                                            if(Input::exists()){
                                                if(Token::check(Input::get('token'))){
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

                                                        Redirect::to(URL::build('/admin/buycraft/', 'view=payments&action=create&step=2'));
                                                        die();

                                                    } else {
                                                        $error = $buycraft_language->get('language', 'please_enter_valid_ign_package');
                                                    }
                                                } else {
                                                    $error = $language->get('general', 'invalid_token');
                                                }
                                            }

                                            // Choose package
                                            $packages = $queries->orderAll('buycraft_packages', '`order`', 'ASC');

                                            if(isset($error))
                                                echo '<div class="alert alert-danger">' . $error . '</div>';

                                            if(count($packages)){
                                                ?>
                                                <form action="" method="post">
                                                    <div class="form-group">
                                                        <label for="inputIGN"><?php echo $buycraft_language->get('language', 'ign'); ?></label>
                                                        <input type="text" class="form-control" id="inputIGN" name="ign" placeholder="<?php echo $buycraft_language->get('language', 'ign'); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="inputPackage"><?php echo $buycraft_language->get('language', 'package'); ?></label>
                                                        <select class="form-control" id="inputPackage" name="package">
                                                            <?php foreach($packages as $package){ ?>
                                                            <option value="<?php echo Output::getClean($package->id); ?>"><?php echo Output::getClean($package->name); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="hidden" name="token" value="<?php echo Token::get(); ?>">
                                                        <input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
                                                    </div>
                                                </form>
                                                <?php
                                            } else {
                                                echo '<div class="alert alert-info">' . $buycraft_language->get('language', 'no_packages') . '</div>';
                                            }

                                        }

                                    }

                                } else {
                                    $payments = $queries->orderAll('buycraft_payments', 'date', 'DESC');
                                    ?>

                                    <h5 style="display:inline;"><?php echo $buycraft_language->get('language', 'payments'); ?></h5>
                                    <a class="btn btn-primary float-lg-right" href="<?php echo URL::build('/admin/buycraft/', 'view=payments&amp;action=create'); ?>"><?php echo $buycraft_language->get('language', 'new_payment'); ?></a>
                                    <hr />

                                    <?php
	                                if(Session::exists('buycraft_payment_success')){
		                                echo '<div class="alert alert-success">' . Session::flash('buycraft_payment_success') . '</div>';
	                                }

                                    if(count($payments)){
                                        ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered dataTables-payments">
                                                <colgroup>
                                                    <col span="1" style="width: 40%;">
                                                    <col span="1" style="width: 25%;">
                                                    <col span="1" style="width: 20%">
                                                    <col span="1" style="width: 15%">
                                                </colgroup>
                                                <thead>
                                                <tr>
                                                    <th><?php echo $buycraft_language->get('language', 'user'); ?></th>
                                                    <th><?php echo $buycraft_language->get('language', 'amount'); ?></th>
                                                    <th><?php echo $buycraft_language->get('language', 'date'); ?></th>
                                                    <th><?php echo $buycraft_language->get('language', 'view'); ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($payments as $payment){
                                                    $payment_user = $queries->getWhere('users', array('uuid', '=', $payment->player_uuid));

                                                    if(count($payment_user)){
                                                        $avatar = $user->getAvatar($payment_user[0]->id);
                                                        $style = $user->getGroupClass($payment_user[0]->id);

                                                    } else {
                                                        $avatar = Util::getAvatarFromUUID(Output::getClean($payment->player_uuid));
                                                        $style = '';

                                                    }
                                                    ?>
                                                    <tr>
														<td><a href="<?php echo URL::build('/admin/buycraft/', 'view=payments&amp;user=' . Output::getClean($payment->player_uuid)); ?>" style="<?php echo $style; ?>"><img src="<?php echo $avatar; ?>" class="rounded" style="max-width:32px;max-height:32px;" alt="<?php echo Output::getClean($payment->player_name); ?>" /> <?php echo Output::getClean($payment->player_name); ?></a></td>
                                                        <td><?php echo Output::getPurified($payment->currency_symbol) . Output::getClean($payment->amount); ?></td>
                                                        <td><?php echo date('d M Y, H:i', $payment->date); ?></td>
                                                        <td>
                                                            <a href="<?php echo URL::build('/admin/buycraft/', 'view=payments&amp;payment=' . Output::getClean($payment->id)); ?>"
                                                               class="btn btn-primary btn-sm"><?php echo $buycraft_language->get('language', 'view'); ?></a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php

                                    } else {
                                        echo '<div class="alert alert-info">' . $buycraft_language->get('language', 'no_payments') . '</div>';
                                    }

                                }

                                break;

							case 'bans':
								if(!$user->hasPermission('admincp.buycraft.bans')){
									Redirect::to(URL::build('/admin'));
									die();
								}

								if(!isset($_GET['action']) || $_GET['action'] == 'new'){
									echo '<h5 style="display:inline;">' . $buycraft_language->get('language', 'bans') . '</h5>';

									if(isset($_GET['action'])){
										echo '<a class="btn btn-danger float-lg-right" onclick="return confirm(\'' . $language->get('general', 'confirm_cancel') . '\');" href="' . URL::build('/admin/buycraft/', 'view=bans') . '">' . $language->get('general', 'cancel') . '</a>';

									} else if($user->hasPermission('admincp.buycraft.bans.new')){
										echo '<a class="btn btn-primary float-lg-right" href="' . URL::build('/admin/buycraft/', 'view=bans&amp;action=new') . '">' . $buycraft_language->get('language', 'new_ban') . '</a>';

									}
									echo '<hr />';
								}

								if(isset($_GET['action'])){
									if($_GET['action'] == 'new'){
										if(!$user->hasPermission('admincp.buycraft.bans.new')){
											Redirect::to(URL::build('/admin/buycraft/', 'view=bans'));
											die();
										}

										if(Input::exists()){
											if(Token::check(Input::get('token'))){
												$validate = new Validate();
												$validation = $validate->check($_POST, array(
													'user' => array(
														'required' => true
													)
												));

												if($validation->passed()){
													// POST to Buycraft
													$post_object = new stdClass();
													$post_object->user = Output::getClean(str_replace('-', '', $_POST['user']));

													if(isset($_POST['ip']) && strlen($_POST['ip']) > 0){
														$post_object->ip = Output::getClean($_POST['ip']);
													}

													if(isset($_POST['reason']) && strlen($_POST['reason']) > 0){
														$post_object->reason = Output::getPurified($_POST['reason']);
													}

													$json = json_encode($post_object);

													// Get server key
													$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

													if(count($server_key))
														$server_key = $server_key[0]->value;
													else
														$server_key = null;

													$ch = curl_init();
													curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/bans');
													curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-Buycraft-Secret: ' . $server_key));
													curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
													curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
													curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

													$ch_result = curl_exec($ch);

													$result = json_decode($ch_result);

													curl_close($ch);

													if(isset($result->error_code)){
														$error = Output::getClean($result->error_code . ': ' . $result->error_message);
													} else {
														Buycraft::updateBans($server_key, null, DB::getInstance());
														Session::flash('buycraft_ban_success', $buycraft_language->get('language', 'ban_created_successfully'));
														Redirect::to(URL::build('/admin/buycraft/', 'view=bans'));
														die();
													}

												} else {
													$error = $buycraft_language->get('language', 'must_enter_uuid');
												}
											} else {
												$error = $language->get('general', 'invalid_token');
											}
										}

										if(isset($error))
											echo '<div class="alert alert-danger">' . $error . '</div>';

										?>

										<form action="" method="post">
											<div class="form-group">
												<label for="inputUser"><?php echo $buycraft_language->get('language', 'uuid'); ?></label>
												<input type="text" class="form-control" name="user" id="inputUser" placeholder="<?php echo $buycraft_language->get('language', 'uuid'); ?>">
											</div>
											<div class="form-group">
												<label for="inputIP"><?php echo $buycraft_language->get('language', 'ip_address'); ?></label> <small><?php echo $buycraft_language->get('language', 'optional'); ?></small>
												<input type="text" class="form-control" name="ip" id="inputIP" placeholder="<?php echo $buycraft_language->get('language', 'ip_address'); ?>">
											</div>
											<div class="form-group">
												<label for="inputReason"><?php echo $buycraft_language->get('language', 'reason'); ?></label> <small><?php echo $buycraft_language->get('language', 'optional'); ?></small>
												<textarea id="inputReason" name="reason" class="form-control" rows="5"></textarea>
											</div>
											<div class="form-group">
												<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
												<input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
											</div>
										</form>

										<?php

									} else if($_GET['action'] == 'view'){
										// Get ban
										if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
											Redirect::to(URL::build('/admin/buycraft/', 'view=bans'));
											die();
										}

										$ban = $queries->getWhere('buycraft_bans', array('id', '=', $_GET['id']));
										if(!count($ban)){
											Redirect::to(URL::build('/admin/buycraft/', 'view=bans'));
											die();
										} else
											$ban = $ban[0];

										echo '<h5 style="display:inline">' . str_replace('{x}', Output::getClean($ban->id), $buycraft_language->get('language', 'viewing_ban_x')) . '</h5>';
										echo '<a class="btn btn-info float-lg-right" href="' . URL::build('/admin/buycraft/', 'view=bans') . '">' . $language->get('general', 'back') . '</a>';
										echo '<hr />';

										?>

										<div class="table-responsive">
											<table class="table table-striped">
												<col width="50%"></col>
												<col width="50%"></col>
												<tbody>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'ign'); ?></strong></td>
													<td><?php echo '<img src="' . Util::getAvatarFromUUID(Output::getClean($ban->uuid)) . '" class="rounded" style="max-height:32px;max-width:32px;" alt="' . Output::getClean($ban->user_ign) . '"> ' . Output::getClean($ban->user_ign); ?></td>
												</tr>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'uuid'); ?></strong></td>
													<td><?php echo Output::getClean($ban->uuid); ?></td>
												</tr>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'ip_address'); ?></strong></td>
													<td><?php echo Output::getClean($ban->ip); ?></td>
												</tr>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'reason'); ?></strong></td>
													<td><?php echo Output::getPurified($ban->reason); ?></td>
												</tr>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'date'); ?></strong></td>
													<td><?php echo date('d M Y, H:i', $ban->time); ?></td>
												</tr>
												</tbody>
											</table>
										</div>

										<div class="alert alert-info"><?php echo $buycraft_language->get('language', 'remove_ban_in_buycraft'); ?></div>

										<?php

									} else {
										Redirect::to(URL::build('/admin/buycraft/', 'view=bans'));
										die();
									}

								} else {
									// Get all bans
									$bans = $queries->getWhere('buycraft_bans', array('id', '<>', 0));

									if(Session::exists('buycraft_ban_success'))
										echo '<div class="alert alert-success">' . Session::flash('buycraft_ban_success') . '</div>';

									if(!count($bans))
										echo '<div class="alert alert-info">' . $buycraft_language->get('language', 'no_bans') . '</div>';
									else {
										?>
										<div class="table-responsive">
											<table class="table table-bordered dataTables-bans">
												<colgroup>
													<col span="1" style="width: 35%;">
													<col span="1" style="width: 30%;">
													<col span="1" style="width: 20%">
													<col span="1" style="width: 15%">
												</colgroup>
												<thead>
												<tr>
													<th><?php echo $buycraft_language->get('language', 'user'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'ip_address'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'date'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'view'); ?></th>
												</tr>
												</thead>
												<tbody>
												<?php
												foreach($bans as $ban){
													$ban_user = $queries->getWhere('users', array('uuid', '=', $ban->uuid));

													if(count($ban_user)){
														$avatar = $user->getAvatar($ban_user[0]->id);
														$style = $user->getGroupClass($ban_user[0]->id);

													} else {
														$avatar = Util::getAvatarFromUUID(Output::getClean($ban->uuid));
														$style = '';

													}
													?>
													<tr>
														<td><img src="<?php echo $avatar; ?>" class="rounded" style="max-width:32px;max-height:32px;" alt="<?php echo Output::getClean($ban->user_ign); ?>" /> <span style="<?php echo $style; ?>"><?php echo Output::getClean($ban->user_ign); ?></span></td>
														<td><?php echo Output::getClean($ban->ip); ?></td>
														<td><?php echo date('d M Y, H:i', $ban->time); ?></td>
														<td>
															<a href="<?php echo URL::build('/admin/buycraft/', 'view=bans&amp;action=view&amp;id=' . Output::getClean($ban->id)); ?>"
															   class="btn btn-primary btn-sm"><?php echo $buycraft_language->get('language', 'view'); ?></a>
														</td>
													</tr>
												<?php } ?>
												</tbody>
											</table>
										</div>
										<?php
									}

								}

								break;

							case 'coupons':
								if(!$user->hasPermission('admincp.buycraft.coupons')){
									Redirect::to(URL::build('/admin/buycraft/'));
									die();
								}

								if(!isset($_GET['action'])){
									// List coupons
									echo '<h5 style="display:inline;">' . $buycraft_language->get('language', 'coupons') . '</h5>';
									echo '<a class="btn btn-primary float-lg-right" href="' . URL::build('/admin/buycraft/', 'view=coupons&amp;action=new') . '">' . $buycraft_language->get('language', 'new_coupon') . '</a>';
									echo '<hr />';

									if(Session::exists('new_coupon_success'))
										echo '<div class="alert alert-success">' . Session::flash('new_coupon_success') . '</div>';

									$coupons = $queries->getWhere('buycraft_coupons', array('id', '<>', 0));

									if(count($coupons)){
										?>
										<div class="table-responsive">
											<table class="table table-striped">
												<colgroup>
													<col span="1" style="width: 35%;">
													<col span="1" style="width: 30%;">
													<col span="1" style="width: 20%">
													<col span="1" style="width: 15%">
												</colgroup>
												<thead>
												<tr>
													<th><?php echo $buycraft_language->get('language', 'coupon_code'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'expiry_date_table'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'uses'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'view'); ?></th>
												</tr>
												</thead>
												<tbody>
												<?php
												foreach($coupons as $coupon){
													?>
													<tr>
														<td><?php echo Output::getClean($coupon->code); ?></td>
														<td><?php echo date('d M Y, H:i', $coupon->date); ?></td>
														<td><?php echo Output::getClean($coupon->redeem_limit); ?></td>
														<td>
															<a href="<?php echo URL::build('/admin/buycraft/', 'view=coupons&amp;action=view&amp;id=' . Output::getClean($coupon->id)); ?>"
															   class="btn btn-primary btn-sm"><?php echo $buycraft_language->get('language', 'view'); ?></a>
														</td>
													</tr>
												<?php } ?>
												</tbody>
											</table>
										</div>
										<?php
									} else
										echo '<div class="alert alert-info">' . $buycraft_language->get('language', 'no_coupons') . '</div>';

								} else {
									if($_GET['action'] == 'new'){
										if(!$user->hasPermission('admincp.buycraft.coupons.new')){
											Redirect::to(URL::build('/admin/buycraft/', 'view=coupons'));
											die();
										}

										// New coupon
										if(Input::exists()){
											$errors = array();

											if(Token::check(Input::get('token'))){
												$validate = new Validate();
												$validation = $validate->check($_POST, array(
													'code' => array(
														'required' => true,
														'alphanumeric' => true
													),
													'note' => array(
														'required' => true,
													)
												));

												if($validation->passed()){
													if(isset($_POST['expire_date']) && !empty($_POST['expire_date'])){
														if(Util::validateDate($_POST['expire_date'], 'Y-m-d')){
															$expire_date = $_POST['expire_date'];
														} else {
															$errors[] = $buycraft_language->get('language', 'invalid_expire_date');
														}
													} else {
														$expire_date = '-0001-11-30';
													}

													if(isset($_POST['start_date']) && !empty($_POST['start_date'])){
														if(Util::validateDate($_POST['start_date'], 'Y-m-d')){
															$start_date = $_POST['start_date'];
														} else {
															$errors[] = $buycraft_language->get('language', 'invalid_start_date');
														}
													} else {
														$start_date = date('Y-m-d');
													}

													if(!count($errors)){
														// Create coupon
														$post_object = new stdClass();

														if(isset($_POST['basket_type'])){
															switch($_POST['basket_type']){
																case 2:
																	$post_object->basket_type = 'single';

																	break;

																case 3:
																	$post_object->basket_type = 'subscription';

																	break;

																default:
																	$post_object->basket_type = 'both';

																	break;
															}
														} else {
															$post_object->basket_type = 'both';
														}

														if(isset($_POST['minimum'])){
															$post_object->minimum = $_POST['minimum'] + 0;
														} else {
															$post_object->minimum = 0;
														}

														if(isset($_POST['discount_application_method'])){
															switch($_POST['discount_application_method']){
																case 2:
																	$post_object->discount_application_method = 1;

																	break;

																case 3:
																	$post_object->discount_application_method = 2;

																	break;

																default:
																	$post_object->discount_application_method = 0;

																	break;
															}
														} else {
															$post_object->discount_application_method = 0;
														}

														if(isset($_POST['username']) && !empty($_POST['username'])){
															$post_object->username = Output::getClean($_POST['username']);
														}

														if(isset($_POST['effective_on'])){
															switch($_POST['effective_on']){
																case 2:
																	$post_object->effective_on = 'package';

																	break;

																case 3:
																	$post_object->effective_on = 'category';

																	break;

																default:
																	$post_object->effective_on = 'cart';

																	break;
															}
														} else
															$post_object->effective_on = 'cart';

														if(isset($_POST['packages']) && is_array($_POST['packages'])){
															$packages = array();
															foreach($_POST['packages'] as $package){
																$packages[] = $package + 0;
															}
															$post_object->packages = $packages;
														} else {
															$post_object->packages = array();
														}

														if(isset($_POST['categories']) && is_array($_POST['categories'])){
															$categories = array();
															foreach($_POST['categories'] as $category){
																$categories[] = $category + 0;
															}
															$post_object->categories = $categories;
														} else {
															$post_object->categories = array();
														}

														if(isset($_POST['discount_type'])){
															if($_POST['discount_type'] == 'percentage'){
																$post_object->discount_type = 'percentage';
															} else {
																$post_object->discount_type = 'value';
															}
														} else {
															$post_object->discount_type = 'value';
														}

														if(isset($_POST['discount_amount'])){
															$post_object->discount_amount = $_POST['discount_amount'] + 0;
														} else {
															$post_object->discount_amount = 0;
														}

														if(isset($_POST['discount_percentage'])){
															$post_object->discount_percentage = $_POST['discount_percentage'] + 0;
														} else {
															$post_object->discount_percentage = 0;
														}

														if(isset($_POST['redeem_unlimited']) && $_POST['redeem_unlimited'] == 'on'){
															$post_object->redeem_unlimited = 'true';
														} else {
															$post_object->redeem_unlimited = 'false';
														}

														if(isset($_POST['expire_never']) && $_POST['expire_never'] == 'on'){
															$post_object->expire_never = 'true';
														} else {
															$post_object->expire_never = 'false';
														}

														if(isset($_POST['expire_limit'])){
															$post_object->expire_limit = $_POST['expire_limit'] + 0;
														} else {
															$post_object->expire_limit = 0;
														}

														$post_object->code = Output::getClean($_POST['code']);
														$post_object->note = Output::getPurified($_POST['note']);
														$post_object->expire_date = $expire_date;
														$post_object->start_date = $start_date;

														$json = json_encode($post_object);

														// POST to Buycraft
														// Get server key
														$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

														if(count($server_key))
															$server_key = $server_key[0]->value;
														else
															$server_key = null;

														$ch = curl_init();
														curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/coupons');
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
															Buycraft::updateCoupons($server_key, null, DB::getInstance());
															Session::flash('new_coupon_success', $buycraft_language->get('language', 'coupon_created_successfully'));
															Redirect::to(URL::build('/admin/buycraft/', 'view=coupons'));
															die();
														}

													}

												} else {
													foreach($validation->errors() as $error){
														if(strpos($error, 'alphanumeric') !== false){
															$errors[] = $buycraft_language->get('language', 'coupon_code_alphanumeric');
														} else {
															// required
															if(strpos($error, 'code') !== false){
																$errors[] = $buycraft_language->get('language', 'coupon_code_required');
															} else {
																$errors[] = $buycraft_language->get('language', 'coupon_note_required');
															}
														}
													}
												}

											} else
												$errors[] = $language->get('general', 'invalid_token');
										}

										echo '<h5 style="display:inline">' . $buycraft_language->get('language', 'coupons') . '</h5>';
										echo '<a href="' . URL::build('/admin/buycraft/', 'view=coupons') . '" class="btn btn-danger float-lg-right" onclick="return confirm(\'' . $language->get('general', 'confirm_cancel') . '\');">' . $language->get('general', 'cancel') . '</a>';
										echo '<hr />';

										if(isset($errors) && count($errors)){
											echo '<div class="alert alert-danger"><ul>';
											foreach($errors as $error){
												echo '<li>' . $error . '</li>';
											}
											echo '</ul></div>';
										}

										$packages = $queries->orderAll('buycraft_packages', '`order`', 'ASC');
										$categories= $queries->orderAll('buycraft_categories', '`order`', 'ASC');
										$currency = $queries->getWhere('buycraft_settings', array('name', '=', 'currency_symbol'));
										if(count($currency))
											$currency = Output::getPurified($currency[0]->value);
										else
											$currency = '';
										?>

										<form action="" method="post">
											<div class="form-group">
												<label for="inputCode"><?php echo $buycraft_language->get('language', 'coupon_code'); ?></label>
												<input type="text" class="form-control" name="code" placeholder="<?php echo $buycraft_language->get('language', 'coupon_code'); ?>" value="<?php echo Output::getClean(Input::get('code')); ?>">
											</div>

											<div class="form-group">
												<label for="inputNote"><?php echo $buycraft_language->get('language', 'coupon_note'); ?></label>
												<textarea class="form-control" id="inputNote" name="note"><?php echo Output::getClean(Input::get('note')); ?></textarea>
											</div>

											<div class="form-group">
												<label for="inputEffectiveOn"><?php echo $buycraft_language->get('language', 'effective_on'); ?></label>
												<select class="form-control" name="effective_on" id="inputEffectiveOn">
													<option value="1"><?php echo $buycraft_language->get('language', 'cart'); ?></option>
													<option value="2"><?php echo $buycraft_language->get('language', 'package'); ?></option>
													<option value="3"><?php echo $buycraft_language->get('language', 'category'); ?></option>
												</select>
											</div>

											<div class="form-group" id="effectiveOnPackages">
												<label for="inputEffectiveOnPackages"><?php echo $buycraft_language->get('language', 'packages'); ?></label> <small><?php echo $buycraft_language->get('language', 'select_multiple_with_ctrl'); ?></small>
												<select class="form-control" id="inputEffectiveOnPackages" name="packages[]" multiple>
													<?php
													if(count($packages)){
														foreach($packages as $package){
															echo '<option value="' . Output::getClean($package->id) . '">' . Output::getClean($package->name) . '</option>';
														}
													}
													?>
												</select>
											</div>

											<div class="form-group" id="effectiveOnCategories">
												<label for="inputEffectiveOnCategories"><?php echo $buycraft_language->get('language', 'categories'); ?></label> <small><?php echo $buycraft_language->get('language', 'select_multiple_with_ctrl'); ?></small>
												<select class="form-control" id="inputEffectiveOnCategories" name="categories[]" multiple>
													<?php
													if(count($categories)){
														foreach($categories as $category){
															echo '<option value="' . Output::getClean($category->id) . '">' . Output::getClean($category->name) . '</option>';
														}
													}
													?>
												</select>
											</div>

											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="inputDiscountType"><?php echo $buycraft_language->get('language', 'discount_type'); ?></label>
														<select class="form-control" id="inputDiscountType" name="discount_type">
															<option value="value"><?php echo $buycraft_language->get('language', 'value'); ?></option>
															<option value="percentage"><?php echo $buycraft_language->get('language', 'percentage'); ?></option>
														</select>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group" id="discountTypeValue">
														<label for="inputDiscountTypeValue"><?php echo $buycraft_language->get('language', 'value'); ?></label>
														<div class="input-group mb-2 mr-sm-2 mb-sm-0">
															<div class="input-group-addon"><?php echo Output::getClean($currency); ?></div>
															<input type="number" class="form-control" name="discount_amount" id="inputDiscountTypeValue" placeholder="<?php echo $buycraft_language->get('language', 'value'); ?>" value="<?php echo Output::getClean(Input::get('discount_amount')); ?>">
														</div>
													</div>
													<div class="form-group" id="discountTypePercentage">
														<label for="inputDiscountTypePercentage"><?php echo $buycraft_language->get('language', 'percentage'); ?></label>
														<div class="input-group mb-2 mr-sm-2 mb-sm-0">
															<input type="number" class="form-control" name="discount_percentage" id="inputDiscountTypePercentage" placeholder="<?php echo $buycraft_language->get('language', 'percentage'); ?>" value="<?php echo Output::getClean(Input::get('discount_percentage')); ?>">
															<div class="input-group-addon">%</div>
														</div>
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-6 align-self-center">
													<div class="form-group">
														<label for="inputRedeemUnlimited"><?php echo $buycraft_language->get('language', 'unlimited_usage'); ?></label>
														<input type="checkbox" name="redeem_unlimited" id="inputRedeemUnlimited" class="js-switch" <?php if(isset($_POST['redeem_unlimited']) && $_POST['redeem_unlimited'] == 'on') echo 'checked '; ?>/>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group" id="redeemLimit">
														<label for="inputRedeemLimit"><?php echo $buycraft_language->get('language', 'uses'); ?></label>
														<input type="number" class="form-control" name="expire_limit" id="inputRedeemLimit" placeholder="<?php echo $buycraft_language->get('language', 'uses'); ?>" value="<?php echo Output::getClean(Input::get('expire_limit')); ?>">
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-6 align-self-center">
													<div class="form-group">
														<label for="inputExpireNever"><?php echo $buycraft_language->get('language', 'never_expire'); ?></label>
														<input type="checkbox" name="expire_never" id="inputExpireNever" class="js-switch" <?php if(isset($_POST['expire_never']) && $_POST['expire_never'] == 'on') echo 'checked '; ?>/>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group" id="expireDate">
														<label for="inputExpiryDate"><?php echo $buycraft_language->get('language', 'expiry_date'); ?></label>
														<input type="text" class="form-control" name="expire_date" id="inputExpiryDate" placeholder="<?php echo $buycraft_language->get('language', 'expiry_date'); ?>" value="<?php echo Output::getClean(Input::get('expire_date')); ?>">
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="inputStartDate"><?php echo $buycraft_language->get('language', 'start_date'); ?></label>
												<input type="text" class="form-control" name="start_date" id="inputStartDate" placeholder="<?php echo $buycraft_language->get('language', 'start_date'); ?>" value="<?php echo (isset($_POST['start_date']) ? Output::getClean($_POST['start_date']) : date('Y-m-d')); ?>">
											</div>

											<div class="form-group">
												<label for="inputBasketType"><?php echo $buycraft_language->get('language', 'basket_type'); ?></label>
												<select class="form-control" name="basket_type" id="inputBasketType">
													<option value="both"><?php echo $buycraft_language->get('language', 'all_purchases'); ?></option>
													<option value="single"><?php echo $buycraft_language->get('language', 'one_off_purchases'); ?></option>
													<option value="subscription"><?php echo $buycraft_language->get('language', 'subscriptions'); ?></option>
												</select>
											</div>

											<div class="form-group">
												<label for="inputDiscountApplicationType"><?php echo $buycraft_language->get('language', 'discount_application_type'); ?></label>
												<select class="form-control" name="discount_application_method" id="inputDiscountApplicationType">
													<option value="1"><?php echo $buycraft_language->get('language', 'each_package'); ?></option>
													<option value="2"><?php echo $buycraft_language->get('language', 'basket_before_sales'); ?></option>
													<option value="3"><?php echo $buycraft_language->get('language', 'basket_after_sales'); ?></option>
												</select>
											</div>

											<div class="form-group">
												<label for="inputMinimum"><?php echo $buycraft_language->get('language', 'minimum_spend'); ?></label>
												<div class="input-group mb-2 mr-sm-2 mb-sm-0">
													<div class="input-group-addon"><?php echo Output::getClean($currency); ?></div>
													<input type="number" class="form-control" name="minimum" id="inputMinimum" placeholder="<?php echo $buycraft_language->get('language', 'minimum_spend'); ?>" value="<?php echo Output::getClean(Input::get('minimum')); ?>">
												</div>
											</div>

											<div class="form-group">
												<label for="inputUsername"><?php echo $buycraft_language->get('language', 'user_coupon_for'); ?></label> <small><?php echo $buycraft_language->get('language', 'optional'); ?></small>
												<input type="text" class="form-control" id="inputUsername" name="username" placeholder="<?php echo $buycraft_language->get('language', 'user_coupon_for'); ?>" value="<?php echo Output::getClean(Input::get('username')); ?>">
											</div>

											<div class="form-group">
												<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
												<input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
											</div>
										</form>

										<?php

									} else if($_GET['action'] == 'view'){
										// View coupon
										if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
											Redirect::to(URL::build('/admin/buycraft/', 'view=coupons'));
											die();
										}

										// Ensure coupon exists
										$coupon = $queries->getWhere('buycraft_coupons', array('id', '=', $_GET['id']));
										if(!count($coupon)){
											Redirect::to(URL::build('/admin/buycraft/', 'view=coupons'));
											die();
										}
										$coupon = $coupon[0];

										if(Input::exists()){
											if(Token::check(Input::get('token'))){
												if($user->hasPermission('admincp.buycraft.coupons.delete') && isset($_POST['action']) && $_POST['action'] == 'delete'){
													// DELETE request
													// Get server key
													$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

													if(count($server_key))
														$server_key = $server_key[0]->value;
													else
														$server_key = null;

													$ch = curl_init();
													curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/coupons/' . $coupon->id);
													curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-Buycraft-Secret: ' . $server_key));
													curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
													curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
													curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

													$ch_result = curl_exec($ch);

													$result = json_decode($ch_result);

													curl_close($ch);

													if(isset($result->error_code)){
														$error = Output::getClean($result->error_code . ': ' . $result->error_message);
													} else {
														$queries->delete('buycraft_coupons', array('id', '=', $coupon->id));
														Session::flash('new_coupon_success', $buycraft_language->get('language', 'coupon_deleted_successfully'));
														Redirect::to(URL::build('/admin/buycraft/', 'view=coupons'));
														die();
													}
												}
											} else
												$error = $language->get('general', 'invalid_token');
										}

										echo '<h5 style="display:inline">' . str_replace('{x}', Output::getClean($coupon->code), $buycraft_language->get('language', 'viewing_coupon_x')) . '</h5>';
										echo '<a href="' . URL::build('/admin/buycraft/', 'view=coupons') . '" class="btn btn-primary float-lg-right">' . $language->get('general', 'back') . '</a>';
										echo '<hr />';

										if(isset($error))
											echo '<div class="alert alert-danger">' . $error . '</div>';

										$packages = $queries->orderAll('buycraft_packages', '`order`', 'ASC');
										$categories= $queries->orderAll('buycraft_categories', '`order`', 'ASC');
										$currency = $queries->getWhere('buycraft_settings', array('name', '=', 'currency_symbol'));
										if(count($currency))
											$currency = $currency[0]->value;
										else
											$currency = '$'; // fallback to $
										?>

										<form>
											<div class="form-group">
												<label for="inputCode"><?php echo $buycraft_language->get('language', 'coupon_code'); ?></label>
												<input disabled type="text" class="form-control" name="code" placeholder="<?php echo $buycraft_language->get('language', 'coupon_code'); ?>" value="<?php echo Output::getClean($coupon->code); ?>">
											</div>

											<div class="form-group">
												<label for="inputNote"><?php echo $buycraft_language->get('language', 'coupon_note'); ?></label>
												<textarea disabled class="form-control" id="inputNote" name="note"><?php echo Output::getClean($coupon->note); ?></textarea>
											</div>

											<div class="form-group">
												<label for="inputEffectiveOn"><?php echo $buycraft_language->get('language', 'effective_on'); ?></label>
												<select disabled class="form-control" name="effective_on" id="inputEffectiveOn">
													<option><?php echo $buycraft_language->get('language', $coupon->effective_type); ?></option>
												</select>
											</div>

											<div class="form-group" id="effectiveOnPackages">
												<label for="inputEffectiveOnPackages"><?php echo $buycraft_language->get('language', 'packages'); ?></label>
												<select disabled class="form-control" id="inputEffectiveOnPackages" name="packages[]" multiple>
													<?php
													if(count($packages)){
														foreach($packages as $package){
															echo '<option value="' . Output::getClean($package->id) . '"' . (in_array($package->id, json_decode($coupon->effective_packages)) ? ' selected' : '') . '>' . Output::getClean($package->name) . '</option>';
														}
													}
													?>
												</select>
											</div>

											<div class="form-group" id="effectiveOnCategories">
												<label for="inputEffectiveOnCategories"><?php echo $buycraft_language->get('language', 'categories'); ?></label>
												<select disabled class="form-control" id="inputEffectiveOnCategories" name="categories[]" multiple>
													<?php
													if(count($categories)){
														foreach($categories as $category){
															echo '<option value="' . Output::getClean($category->id) . '"' . (in_array($category->id, json_decode($coupon->effective_categories)) ? ' selected' : '') . '>' . Output::getClean($category->name) . '</option>';
														}
													}
													?>
												</select>
											</div>

											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="inputDiscountType"><?php echo $buycraft_language->get('language', 'discount_type'); ?></label>
														<select disabled class="form-control" id="inputDiscountType" name="discount_type">
															<option><?php echo $buycraft_language->get('language', $coupon->discount_type); ?></option>
														</select>
													</div>
												</div>
												<div class="col-md-6">
													<?php if($coupon->discount_type == 'value'){ ?>
													<div class="form-group" id="discountTypeValue">
														<label for="inputDiscountTypeValue"><?php echo $buycraft_language->get('language', 'value'); ?></label>
														<div class="input-group mb-2 mr-sm-2 mb-sm-0">
															<div class="input-group-addon"><?php echo Output::getClean($currency); ?></div>
															<input disabled type="number" class="form-control" name="discount_amount" id="inputDiscountTypeValue" placeholder="<?php echo $buycraft_language->get('language', 'value'); ?>" value="<?php echo Output::getClean($coupon->discount_value); ?>">
														</div>
													</div>
													<?php } else { ?>
													<div class="form-group" id="discountTypePercentage">
														<label for="inputDiscountTypePercentage"><?php echo $buycraft_language->get('language', 'percentage'); ?></label>
														<div class="input-group mb-2 mr-sm-2 mb-sm-0">
															<input disabled type="number" class="form-control" name="discount_percentage" id="inputDiscountTypePercentage" placeholder="<?php echo $buycraft_language->get('language', 'percentage'); ?>" value="<?php echo Output::getClean($coupon->discount_percentage); ?>">
															<div class="input-group-addon">%</div>
														</div>
													</div>
													<?php } ?>
												</div>
											</div>

											<div class="row">
												<div class="col-md-6 align-self-center">
													<div class="form-group">
														<label for="inputRedeemUnlimited"><?php echo $buycraft_language->get('language', 'unlimited_usage'); ?></label>
														<input disabled type="checkbox" name="redeem_unlimited" id="inputRedeemUnlimited" class="js-switch" <?php if($coupon->redeem_unlimited) echo 'checked '; ?>/>
													</div>
												</div>
												<?php if(!$coupon->redeem_unlimited){ ?>
												<div class="col-md-6">
													<div class="form-group" id="redeemLimit">
														<label for="inputRedeemLimit"><?php echo $buycraft_language->get('language', 'uses'); ?></label>
														<input disabled type="number" class="form-control" name="expire_limit" id="inputRedeemLimit" placeholder="<?php echo $buycraft_language->get('language', 'uses'); ?>" value="<?php echo Output::getClean($coupon->redeem_limit); ?>">
													</div>
												</div>
												<?php } ?>
											</div>

											<div class="row">
												<div class="col-md-6 align-self-center">
													<div class="form-group">
														<label for="inputExpireNever"><?php echo $buycraft_language->get('language', 'never_expire'); ?></label>
														<input disabled type="checkbox" name="expire_never" id="inputExpireNever" class="js-switch" <?php if(!$coupon->expires) echo 'checked '; ?>/>
													</div>
												</div>
												<?php if($coupon->expires){ ?>
												<div class="col-md-6">
													<div class="form-group" id="expireDate">
														<label for="inputExpiryDate"><?php echo $buycraft_language->get('language', 'expiry_date'); ?></label>
														<input disabled type="text" class="form-control" name="expire_date" id="inputExpiryDate" placeholder="<?php echo $buycraft_language->get('language', 'expiry_date'); ?>" value="<?php echo date('Y-m-d', $coupon->date); ?>">
													</div>
												</div>
												<?php } ?>
											</div>

											<div class="form-group">
												<label for="inputStartDate"><?php echo $buycraft_language->get('language', 'start_date'); ?></label>
												<input disabled type="text" class="form-control" name="start_date" id="inputStartDate" placeholder="<?php echo $buycraft_language->get('language', 'start_date'); ?>" value="<?php echo date('Y-m-d', $coupon->start_date); ?>">
											</div>

											<div class="form-group">
												<label for="inputBasketType"><?php echo $buycraft_language->get('language', 'basket_type'); ?></label>
												<select disabled class="form-control" name="basket_type" id="inputBasketType">
													<?php
													switch($coupon->basket_type){
														case 'single':
															echo '<option>' . $buycraft_language->get('language', 'one_off_purchases') . '</option>';
															break;

														case 'subscription':
															echo '<option>' . $buycraft_language->get('language', 'subscriptions') . '</option>';
															break;

														default:
															echo '<option>' . $buycraft_language->get('language', 'all_purchases') . '</option>';
															break;
													}
													?>
												</select>
											</div>

											<div class="form-group">
												<label for="inputMinimum"><?php echo $buycraft_language->get('language', 'minimum_spend'); ?></label>
												<div class="input-group mb-2 mr-sm-2 mb-sm-0">
													<div class="input-group-addon"><?php echo Output::getClean($currency); ?></div>
													<input disabled type="number" class="form-control" name="minimum" id="inputMinimum" placeholder="<?php echo $buycraft_language->get('language', 'minimum_spend'); ?>" value="<?php echo Output::getClean($coupon->minimum); ?>">
												</div>
											</div>

											<?php if($coupon->username){ ?>
											<div class="form-group">
												<label for="inputUsername"><?php echo $buycraft_language->get('language', 'user_coupon_for'); ?></label> <small><?php echo $buycraft_language->get('language', 'optional'); ?></small>
												<input disabled type="text" class="form-control" id="inputUsername" name="username" placeholder="<?php echo $buycraft_language->get('language', 'user_coupon_for'); ?>" value="<?php echo Output::getClean($coupon->username); ?>">
											</div>
											<?php } ?>
										</form>

										<hr />

										<?php if($user->hasPermission('admincp.buycraft.coupons.delete')){ ?>
										<h5><?php echo $buycraft_language->get('language', 'delete_coupon'); ?></h5>
										<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal"><?php echo $buycraft_language->get('language', 'delete_coupon'); ?></button>

										<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="deleteModalLabel"><?php echo $buycraft_language->get('language', 'delete_coupon'); ?></h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<?php echo $buycraft_language->get('language', 'confirm_delete_coupon'); ?>
													</div>
													<form action="" method="post">
														<input type="hidden" name="action" value="delete">
														<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
														<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $language->get('general', 'cancel'); ?></button>
															<button type="submit" class="btn btn-danger"><?php echo $buycraft_language->get('language', 'delete_coupon'); ?></button>
														</div>
													</form>
												</div>
											</div>
										</div>
										<?php
										}
									}
								}

								break;

							case 'giftcards':
								if(!$user->hasPermission('admincp.buycraft.giftcards')){
									Redirect::to(URL::build('/admin/buycraft'));
									die();
								}

								if(!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == 'new')){
									echo '<h5 style="display:inline">' . $buycraft_language->get('language', 'gift_cards') . '</h5>';
									if(isset($_GET['action'])){
										echo '<a class="btn btn-danger float-lg-right" onclick="return confirm(\'' . $language->get('general', 'confirm_cancel') . '\')" href="' . URL::build('/admin/buycraft/', 'view=giftcards') . '">' . $language->get('general', 'cancel') . '</a>';
									} else {
										if($user->hasPermission('admincp.buycraft.giftcards.new')){
											echo '<a class="btn btn-primary float-lg-right" href="' . URL::build('/admin/buycraft/', 'view=giftcards&amp;action=new') . '">' . $buycraft_language->get('language', 'new_gift_card') . '</a>';
										}
									}
									echo '<hr />';

								}

								if(!isset($_GET['action'])){
									// Get giftcards
									$giftcards = $queries->getWhere('buycraft_gift_cards', array('id', '<>', 0));

									if(Session::exists('new_gift_card_success')){
										echo '<div class="alert alert-success">' . Session::flash('new_gift_card_success') . '</div>';
									}

									if(count($giftcards)){
										?>
										<div class="table-responsive">
											<table class="table table-striped">
												<colgroup>
													<col span="1" style="width: 25%;">
													<col span="1" style="width: 25%;">
													<col span="1" style="width: 25%">
													<col span="1" style="width: 10%">
													<col span="1" style="width: 15%">
												</colgroup>
												<thead>
												<tr>
													<th><?php echo $buycraft_language->get('language', 'gift_card_code'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'gift_card_note'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'gift_card_balance_remaining'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'gift_card_active'); ?></th>
													<th><?php echo $buycraft_language->get('language', 'view'); ?></th>
												</tr>
												</thead>
												<tbody>
												<?php
												foreach($giftcards as $giftcard){
													?>
													<tr>
														<td><?php echo Output::getClean($giftcard->code); ?></td>
														<td><?php echo Output::getPurified($giftcard->note); ?></td>
														<td><?php echo Output::getClean($giftcard->balance_remaining . $giftcard->balance_currency); ?></td>
														<td><?php echo ($giftcard->void ? '<i class="fa fa-times-circle fa-2x text-danger"></i>' : '<i class="fa fa-check-circle fa-2x text-success"></i>'); ?></td>
														<td>
															<a href="<?php echo URL::build('/admin/buycraft/', 'view=giftcards&amp;action=view&amp;id=' . Output::getClean($giftcard->id)); ?>"
															   class="btn btn-primary btn-sm"><?php echo $buycraft_language->get('language', 'view'); ?></a>
														</td>
													</tr>
												<?php } ?>
												</tbody>
											</table>
										</div>
										<?php
									} else {
										echo '<div class="alert alert-info">' . $buycraft_language->get('language', 'no_gift_cards') . '</div>';
									}

								} else {
									if($_GET['action'] == 'new'){
										if(!$user->hasPermission('admincp.buycraft.giftcards.new')){
											Redirect::to(URL::build('/admin/buycraft/', 'view=giftcards'));
											die();
										}

										if(Input::exists()){
											if(Token::check(Input::get('token'))){
												$validate = new Validate();
												$validation = $validate->check($_POST, array(
													'amount' => array(
														'required' => true
													)
												));

												if($validation->passed()){
													$post_object = new stdClass();
													$post_object->amount = $_POST['amount'] + 0;
													if(isset($_POST['note']) && strlen($_POST['note']) > 0){
														$post_object->note = Output::getPurified($post_object->note);
													}

													$json = json_encode($post_object);

													// POST to Buycraft
													// Get server key
													$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

													if(count($server_key))
														$server_key = $server_key[0]->value;
													else
														$server_key = null;

													$ch = curl_init();
													curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/gift-cards');
													curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-Buycraft-Secret: ' . $server_key));
													curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
													curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
													curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

													$ch_result = curl_exec($ch);

													$result = json_decode($ch_result);

													curl_close($ch);

													if(isset($result->error_code)){
														$error = Output::getClean($result->error_code . ': ' . $result->error_message);
													} else {
														Buycraft::updateGiftCards($server_key, null, DB::getInstance());
														Session::flash('new_gift_card_success', $buycraft_language->get('language', 'gift_card_created_successfully'));
														Redirect::to(URL::build('/admin/buycraft/', 'view=giftcards'));
														die();
													}

												} else {
													$error = $buycraft_language->get('language', 'gift_card_value_required');
												}

											} else
												$error = $language->get('general', 'invalid_token');
										}

										$currency = $queries->getWhere('buycraft_settings', array('name', '=', 'currency_symbol'));
										if(count($currency))
											$currency = $currency[0]->value;
										else
											$currency = '$'; // fallback to $

										if(isset($error))
											echo '<div class="alert alert-danger">' . $error . '</div>';

										?>

										<form action="" method="post">
											<div class="form-group">
												<label for="inputValue"><?php echo $buycraft_language->get('language', 'gift_card_value'); ?></label>
												<div class="input-group mb-2 mr-sm-2 mb-sm-0">
													<div class="input-group-addon"><?php echo Output::getClean($currency); ?></div>
													<input type="number" class="form-control" id="inputValue" name="amount" placeholder="<?php echo $buycraft_language->get('language', 'gift_card_value'); ?>">
												</div>
											</div>
											<div class="form-group">
												<label for="inputNote"><?php echo $buycraft_language->get('language', 'gift_card_note'); ?></label> <small><?php echo $buycraft_language->get('language', 'optional'); ?></small>
												<textarea class="form-control" id="inputNote" name="note"></textarea>
											</div>
											<div class="form-group">
												<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
												<input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
											</div>
										</form>

										<?php

									} else if($_GET['action'] == 'view'){
										if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
											Redirect::to(URL::build('/admin/buycraft/', 'view=giftcards'));
											die();
										}

										$giftcard = $queries->getWhere('buycraft_gift_cards', array('id', '=', $_GET['id']));
										if(!count($giftcard)){
											Redirect::to(URL::build('/admin/buycraft/', 'view=giftcards'));
											die();
										}
										$giftcard = $giftcard[0];

										if(Input::exists() && $user->hasPermission('admincp.buycraft.giftcards.update')){
											if(Token::check(Input::get('token'))){
												if(isset($_POST['action'])){
													if($_POST['action'] == 'void'){
														// DELETE request
														// Get server key
														$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

														if(count($server_key))
															$server_key = $server_key[0]->value;
														else
															$server_key = null;

														$ch = curl_init();
														curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/gift-cards/' . $giftcard->id);
														curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-Buycraft-Secret: ' . $server_key));
														curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
														curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
														curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

														$ch_result = curl_exec($ch);

														$result = json_decode($ch_result);

														curl_close($ch);

														if(isset($result->error_code)){
															$error = Output::getClean($result->error_code . ': ' . $result->error_message);
														} else {
															$queries->delete('buycraft_gift_cards', array('id', '=', $giftcard->id));
															Session::flash('new_gift_card_success', $buycraft_language->get('language', 'gift_card_voided_successfully'));
															Redirect::to(URL::build('/admin/buycraft/', 'view=giftcards'));
															die();
														}

													} else if($_POST['action'] == 'update'){
														$validate = new Validate();
														$validation = $validate->check($_POST, array(
															'credit' => array(
																'required' => true
															)
														));

														if($validation->passed()){
															$post_object = new stdClass();
															$post_object->amount = $_POST['credit'] + 0;

															$json = json_encode($post_object);

															// POST to Buycraft
															// Get server key
															$server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

															if(count($server_key))
																$server_key = $server_key[0]->value;
															else
																$server_key = null;

															$ch = curl_init();
															curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/gift-cards/' . $giftcard->id);
															curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-Buycraft-Secret: ' . $server_key));
															curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
															curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
															curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
															curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

															$ch_result = curl_exec($ch);

															$result = json_decode($ch_result);

															curl_close($ch);

															if(isset($result->error_code)){
																$error = Output::getClean($result->error_code . ': ' . $result->error_message);
															} else {
																Buycraft::updateGiftCards($server_key, null, DB::getInstance());
																Session::flash('new_gift_card_success', $buycraft_language->get('language', 'gift_card_updated_successfully'));
																Redirect::to(URL::build('/admin/buycraft/', 'view=giftcards'));
																die();
															}

														} else
															$error = $buycraft_language->get('language', 'credit_required');
													}
												}

											} else
												$error = $language->get('general', 'invalid_token');
										}

										echo '<h5 style="display:inline">' . str_replace('{x}', Output::getClean($giftcard->code), $buycraft_language->get('language', 'viewing_gift_card_x')) . '</h5>';
										echo '<a class="btn btn-primary float-lg-right" href="' . URL::build('/admin/buycraft/', 'view=giftcards') . '">' . $language->get('general', 'back') . '</a>';
										echo '<hr />';

										if(isset($error))
											echo '<div class="alert alert-danger">' . $error . '</div>';

										?>
										<div class="table-responsive">
											<table class="table table-striped">
												<col width="50%"></col>
												<col width="50%"></col>
												<tbody>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'gift_card_code'); ?></strong></td>
													<td><?php echo Output::getClean($giftcard->code); ?></td>
												</tr>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'gift_card_start_balance'); ?></strong></td>
													<td><?php echo Output::getClean($giftcard->balance_starting . $giftcard->balance_currency); ?></td>
												</tr>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'gift_card_balance_remaining'); ?></strong></td>
													<td><?php echo Output::getClean($giftcard->balance_remaining . $giftcard->balance_currency); ?></td>
												</tr>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'gift_card_active'); ?></strong></td>
													<td><?php echo ($giftcard->void ? '<i class="fa fa-times-circle fa-2x text-danger"></i>' : '<i class="fa fa-check-circle fa-2x text-success"></i>'); ?></td>
												</tr>
												<tr>
													<td><strong><?php echo $buycraft_language->get('language', 'gift_card_note'); ?></strong></td>
													<td><?php echo Output::getPurified($giftcard->note); ?></td>
												</tr>
												</tbody>
											</table>
										</div>
					
										<?php if($user->hasPermission('admincp.buycraft.giftcards.update')){ ?>
											<hr />

											<h5><?php echo $buycraft_language->get('language', 'add_credit'); ?></h5>
											<form action="" method="post">
												<div class="form-group">
													<label for="inputCredit"><?php echo $buycraft_language->get('language', 'credit'); ?></label>
													<input type="number" class="form-control" id="inputCredit" name="credit" placeholder="<?php echo $buycraft_language->get('language', 'credit'); ?>">
												</div>
												<div class="form-group">
													<input type="hidden" name="action" value="update">
													<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
													<input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
												</div>
											</form>

											<h5><?php echo $buycraft_language->get('language', 'void_gift_card'); ?></h5>
											<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal"><?php echo $buycraft_language->get('language', 'void_gift_card'); ?></button>

											<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
												<div class="modal-dialog" role="document">
													<div class="modal-content">
														<div class="modal-header">
															<h5 class="modal-title" id="deleteModalLabel"><?php echo $buycraft_language->get('language', 'void_gift_card'); ?></h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<?php echo $buycraft_language->get('language', 'confirm_void_gift_card'); ?>
														</div>
														<form action="" method="post">
															<input type="hidden" name="action" value="void">
															<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
															<div class="modal-footer">
																<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $language->get('general', 'cancel'); ?></button>
																<button type="submit" class="btn btn-danger"><?php echo $buycraft_language->get('language', 'void_gift_card'); ?></button>
															</div>
														</form>
													</div>
												</div>
											</div>

										<?php
										}

									} else {
										Redirect::to(URL::build('/admin/buycraft/', 'view=giftcards'));
										die();
									}
								}

								break;


							default:
								Redirect::to(URL::build('/admin/buycraft'));
								die();

								break;
                        }
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require(ROOT_PATH . '/modules/Core/pages/admin/footer.php'); ?>

<?php require(ROOT_PATH . '/modules/Core/pages/admin/scripts.php'); ?>

<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.js"></script>

<script type="text/javascript">
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function(html) {
        var switchery = new Switchery(html);
    });

    $("[rel=popover]").each(function(i, obj) {
        $(this)
            .popover({
                html: true,
                trigger: "manual",
                content: function() {
                    var id = $(this).attr("id");
                    return $("#popover-content-" + id).html();
                }
            })
            .on("mouseenter", function() {
                var _this = this;
                $(this).popover("show");
                $(".popover").on("mouseleave", function() {
                    $(_this).popover("hide");
                });
            })
            .on("mouseleave", function() {
                var _this = this;
                setTimeout(function() {
                    if (!$(".popover:hover").length) {
                        $(_this).popover("hide");
                    }
                }, 300);
            });
    });
</script>

<?php
if(isset($_GET['view'])){
	if($_GET['view'] == 'coupons' && isset($_GET['action']) && $_GET['action'] == 'new'){
		?>
		<script type="text/javascript">
			$(document).ready(function(){
                $('#effectiveOnPackages').hide();
                $('#effectiveOnCategories').hide();
                $('#discountTypePercentage').hide();
			});

			$('#inputEffectiveOn').change(function(){
				let selected = $(this).val();
				if(selected == '1'){
					$('#effectiveOnPackages').hide();
					$('#effectiveOnCategories').hide();
				} else if(selected == '2'){
					$('#effectiveOnPackages').show();
					$('#effectiveOnCategories').hide();
				} else if(selected == '3'){
					$('#effectiveOnPackages').hide();
					$('#effectiveOnCategories').show();
				}
			});

			$('#inputDiscountType').change(function(){
			    let selected = $(this).val();
			    if(selected == 'value'){
			        $('#discountTypeValue').show();
			        $('#discountTypePercentage').hide();
				} else {
			        $('#discountTypeValue').hide();
			        $('#discountTypePercentage').show();
				}
			});

			$('#inputRedeemUnlimited').change(function(){
			    if($('#inputRedeemUnlimited').prop("checked")){
			        $('#redeemLimit').hide();
				} else {
			        $('#redeemLimit').show();
				}
			});

            $('#inputExpireNever').change(function(){
                if($('#inputExpireNever').prop("checked")){
                    $('#expireDate').hide();
                } else {
                    $('#expireDate').show();
                }
            });
		</script>
		<?php
	} else if(($_GET['view'] == 'categories' || $_GET['view'] == 'packages') && isset($_GET['edit'])){
        ?>
<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/emoji/js/emojione.min.js"></script>
<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js"></script>
<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/plugins/emojione/dialogs/emojione.json"></script>

        <?php
		echo '<script type="text/javascript">' . Input::createEditor('inputDescription') . '</script>';
    } else if(($_GET['view'] == 'payments' && !isset($_GET['payment']) || ($_GET['view'] == 'bans' && !isset($_GET['action'])))){
        ?>
        <script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/moment/moment.min.js"></script>
        <script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/dataTables/DataTables-1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/dataTables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
        <script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/dataTables/DataTables-1.10.16/js/dataTables.moment.js"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $.fn.dataTable.moment( 'D MMM YYYY, HH:mm' );

                $('.dataTables-<?php echo Output::getClean($_GET['view']); ?>').dataTable({
                    responsive: true,
                    language: {
                        "lengthMenu": "<?php echo $language->get('table', 'display_records_per_page'); ?>",
                        "zeroRecords": "<?php echo $language->get('table', 'nothing_found'); ?>",
                        "info": "<?php echo $language->get('table', 'page_x_of_y'); ?>",
                        "infoEmpty": "<?php echo $language->get('table', 'no_records'); ?>",
                        "infoFiltered": "<?php echo $language->get('table', 'filtered'); ?>",
                        "search": "<?php echo $language->get('general', 'search'); ?> ",
                        "paginate": {
                            "next": "<?php echo $language->get('general', 'next'); ?>",
                            "previous": "<?php echo $language->get('general', 'previous'); ?>"
                        }
                    },
                    "order": []
                });
            });
        </script>
        <?php
    }
} else {
	if($user->hasPermission('admincp.buycraft.settings')){
        ?>
	<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/emoji/js/emojione.min.js"></script>
	<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js"></script>
	<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/ckeditor.js"></script>
	<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/plugins/emojione/dialogs/emojione.json"></script>

	<?php
	echo '<script type="text/javascript">' . Input::createEditor('inputStoreContent', true) . '</script>';
	}
}
?>
</body>
</html>