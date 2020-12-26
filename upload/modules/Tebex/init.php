<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr8
 *
 *  License: MIT
 *
 *  Tebex initialisation file
 */

// Language
$buycraft_language = new Language(ROOT_PATH . '/modules/Tebex/language', LANGUAGE);

require_once(ROOT_PATH . '/modules/Tebex/module.php');
$module = new Tebex_Module($language, $buycraft_language, $pages, $cache);