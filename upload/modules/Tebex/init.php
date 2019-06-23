<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr5
 *
 *  License: MIT
 *
 *  Tebex initialisation file
 */

// Language
$buycraft_language = new Language(ROOT_PATH . '/modules/Tebex/language', LANGUAGE);

// Temp admin sidebar method
if (!isset($admin_sidebar)) $admin_sidebar = array();
$admin_sidebar['buycraft'] = array(
	'title' => $buycraft_language->get('language', 'buycraft'),
	'url' => URL::build('/admin/buycraft')
);

require_once(ROOT_PATH . '/modules/Tebex/module.php');
$module = new Tebex_Module($language, $buycraft_language, $pages, $cache);