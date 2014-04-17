<?php
// phpinfo();


define('APP_NAME', 'scedu');

define('APP_PATH', dirname(__FILE__) . '/' . APP_NAME . '/');

if (version_compare(phpversion(), '5.3.0', '<')) :
    require_once 'ArPHP1.0/' . 'init.php';
else :
    require_once 'ArPHP2.0/' . 'init.php';
endif;

