<?php
/**
 * init script
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

define('START_TIME', microtime(true));

defined('APP_NAME') or define('APP_NAME', '');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('FRAME_PATH') or define('FRAME_PATH', dirname(__FILE__) . DS);

defined('APP_PATH') or define('APP_PATH', dirname(FRAME_PATH) . DS . (APP_NAME ? APP_NAME . DS : ''));

defined('APP_VIEW_PATH') or define('APP_VIEW_PATH', APP_PATH . 'View' . DS);

defined('CORE_PATH') or define('CORE_PATH', FRAME_PATH . DS . 'Core' . DS);

defined('CONFIG_PATH') or define('CONFIG_PATH', FRAME_PATH . DS . 'Conf' . DS);

defined('APP_CONFIG_PATH') or define('APP_CONFIG_PATH', APP_PATH . 'Conf' . DS);

defined('EXT_PATH') or define('EXT_PATH', FRAME_PATH . DS . 'Extensions' . DS);

defined('COMP_PATH') or define('COMP_PATH', FRAME_PATH . DS . 'Components' . DS);

require_once (CORE_PATH . 'Ar.class.php');

spl_autoload_register('\Core\Ar::autoLoader');

set_exception_handler('\Core\Ar::exceptionHandler');

set_error_handler('\Core\Ar::errorHandler');

\Core\Ar::init();
