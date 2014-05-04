<?php
// phpinfo();
define('DEFAULT_APP_NAME', 'test');
$edition = 'ArPHP1';
define('APP_PATH', dirname(__FILE__) . '/' . $edition . '/' . DEFAULT_APP_NAME . '/');

require_once $edition . '/ArPHP/' . 'init.php';
