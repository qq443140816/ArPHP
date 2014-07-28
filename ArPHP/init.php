<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: : coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */
defined('AR_START_TIME') or define('AR_START_TIME', microtime(true));

defined('AR_DEBUG') or define('AR_DEBUG', true);

defined('AR_OUTER_START') or define('AR_OUTER_START', false);

defined('AR_AS_OUTER_FRAME') or define('AR_AS_OUTER_FRAME', false);

defined('AR_DEFAULT_APP_NAME') or define('AR_DEFAULT_APP_NAME', 'main');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('AR_FRAME_PATH') or define('AR_FRAME_PATH', dirname(__FILE__) . DS);

defined('AR_ROOT_PATH') or define('AR_ROOT_PATH', realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . DS);

defined('AR_APP_PATH') or define('AR_APP_PATH', AR_ROOT_PATH . (AR_DEFAULT_APP_NAME ? AR_DEFAULT_APP_NAME . DS : ''));

defined('AR_APP_VIEW_PATH') or define('AR_APP_VIEW_PATH', AR_APP_PATH . 'View' . DS);

defined('AR_CORE_PATH') or define('AR_CORE_PATH', AR_FRAME_PATH . 'Core' . DS);

defined('AR_CONFIG_PATH') or define('AR_CONFIG_PATH', AR_FRAME_PATH . 'Conf' . DS);

defined('AR_APP_CONFIG_PATH') or define('AR_APP_CONFIG_PATH', AR_APP_PATH . 'Conf' . DS);

defined('AR_APP_CONTROLLER_PATH') or define('AR_APP_CONTROLLER_PATH', AR_APP_PATH . 'Controller' . DS);

defined('AR_EXT_PATH') or define('AR_EXT_PATH', AR_FRAME_PATH . 'Extensions' . DS);

defined('AR_COMP_PATH') or define('AR_COMP_PATH', AR_FRAME_PATH . 'Components' . DS);

defined('AR_SERVER_PATH') or define('AR_SERVER_PATH', ($dir = dirname($_SERVER['SCRIPT_NAME'])) == DS ? '/' : str_replace(DS, '/', $dir) . '/');

require_once AR_CORE_PATH . 'Ar.class.php';

spl_autoload_register('Ar::autoLoader');

if (!AR_OUTER_START) :
    set_exception_handler('Ar::exceptionHandler');
    set_error_handler('Ar::errorHandler');
else :
    defined('AR_MAN_PATH') or define('AR_MAN_PATH', AR_ROOT_PATH . 'Arman' . DS);
endif;

Ar::init();
