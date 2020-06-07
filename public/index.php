<?php

define('ROOT_PATH', __DIR__ . '/..');
define('APP_PATH', ROOT_PATH . '/app');
define('VENDOR_PATH', ROOT_PATH . '/vendor');

require_once VENDOR_PATH . '/autoload.php';
require_once APP_PATH . '/Bootstrap.php';

new \App\Bootstrap();
