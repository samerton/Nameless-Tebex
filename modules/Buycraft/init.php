<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Buycraft initialisation file
 */

// Ensure module has been installed
$module_installed = $cache->retrieve('module_buycraft');
if(!$module_installed){
    // Hasn't been installed
    // Create tables
    try {
        $engine = Config::get('mysql/engine');
        $charset = Config::get('mysql/charset');
    } catch(Exception $e){
        $engine = 'InnoDB';
        $charset = 'utf8mb4';
    }

    if(!$queries->tableExists('buycraft_categories')){
        try {
            //$queries->createTable('buycraft_categories', '', "ENGINE=$engine DEFAULT CHARSET=$charset");

        } catch(Exception $e){
            // Error

        }
    }
    //echo '<pre>' . $queries->tableExists('users') . '</pre>';
    //die();

    // Permissions
    $admin_permissions = $queries->getWhere('groups', array('id', '=', 2));
    $admin_permissions = $admin_permissions[0]->permissions;

    $admin_permissions = json_decode($admin_permissions, true);
    $admin_permissions['admincp.buycraft'] = 1;
    $admin_permissions['admincp.buycraft.settings'] = 1;
    $admin_permissions['admincp.buycraft.categories'] = 1;
    $admin_permissions['admincp.buycraft.categories.update'] = 1;
    $admin_permissions['admincp.buycraft.packages'] = 1;
    $admin_permissions['admincp.buycraft.packages.update'] = 1;
    $admin_permissions['admincp.buycraft.payments'] = 1;
    $admin_permissions['admincp.buycraft.payments.new'] = 1;
    $admin_permissions['admincp.buycraft.giftcards'] = 1;
    $admin_permissions['admincp.buycraft.giftcards.new'] = 1;
    $admin_permissions['admincp.buycraft.giftcards.update'] = 1;
    $admin_permissions['admincp.buycraft.coupons'] = 1;
    $admin_permissions['admincp.buycraft.coupons.new'] = 1;
    $admin_permissions['admincp.buycraft.coupons.delete'] = 1;
    $admin_permissions['admincp.buycraft.bans'] = 1;
    $admin_permissions['admincp.buycraft.bans.new'] = 1;

    $admin_permissions_updated = json_encode($admin_permissions);

    $queries->update('groups', 2, array(
        'permissions' => $admin_permissions_updated
    ));

    $cache->store('module_buycraft', true);

    if($user->isLoggedIn() && $user->isAdmLoggedIn() && $user->data()->group_id == 2) {
        Redirect::to(URL::build('/admin/modules'));
        die();
    }

} else {
    // Installed
}

// Language
$buycraft_language = new Language(ROOT_PATH . '/modules/Buycraft/language', LANGUAGE);

// Temp admin sidebar method
if (!isset($admin_sidebar)) $admin_sidebar = array();
$admin_sidebar['buycraft'] = array(
	'title' => $buycraft_language->get('language', 'buycraft'),
	'url' => URL::build('/admin/buycraft')
);

require_once(ROOT_PATH . '/modules/Buycraft/module.php');
$module = new Buycraft_Module($language, $buycraft_language, $pages, $cache);