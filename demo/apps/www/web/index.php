<?php
namespace www;

$GLOBALS['start'] = microtime(true);

mb_internal_encoding('UTF-8');

date_default_timezone_set('Europe/Moscow');

header('Content-Type: text/html; charset=utf-8');

define('DEBUG', 1);

define('CONSOLE', false);

define('PROJECT', __NAMESPACE__);

define('ROOT', realpath('../../..')); /// папка "root"

define('APP', realpath('..')); /// папка "root/apps/www"

define('WEB', realpath('.')); /// папка "root/apps/www/web"

if (DEBUG)
{
	error_reporting(-1);

	ini_set('display_errors', 1);
}

require_once ROOT . '/../../myframework/framework.php';

require_once APP . '/extends/WebApplication.php';

F::$app = new WebApplication(require_once  '../config/main.php');

F::$app->run();
