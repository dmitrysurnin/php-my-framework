<?php

define('START_TIME', microtime(true));

define('F_ROOT', dirname(__FILE__));

require_once F_ROOT . '/globals.php';

require_once F_ROOT . '/base/CAutoLoader.php';

spl_autoload_register(['myframework\CAutoLoader', 'autoload']);
