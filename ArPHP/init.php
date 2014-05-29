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
define('START_TIME', microtime(true));

defined('DEFAULT_APP_NAME') or define('DEFAULT_APP_NAME', 'main');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('FRAME_PATH') or define('FRAME_PATH', dirname(__FILE__) . DS);

defined('ROOT_PATH') or define('ROOT_PATH', realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . DS);

defined('APP_PATH') or define('APP_PATH', ROOT_PATH . (DEFAULT_APP_NAME ? DEFAULT_APP_NAME . DS : ''));

defined('APP_VIEW_PATH') or define('APP_VIEW_PATH', APP_PATH . 'View' . DS);

defined('CORE_PATH') or define('CORE_PATH', FRAME_PATH . 'Core' . DS);

defined('CONFIG_PATH') or define('CONFIG_PATH', FRAME_PATH . 'Conf' . DS);

defined('APP_CONFIG_PATH') or define('APP_CONFIG_PATH', APP_PATH . 'Conf' . DS);

defined('APP_CONTROLLER_PATH') or define('APP_CONTROLLER_PATH', APP_PATH . 'Controller' . DS);

defined('EXT_PATH') or define('EXT_PATH', FRAME_PATH . 'Extensions' . DS);

defined('COMP_PATH') or define('COMP_PATH', FRAME_PATH . 'Components' . DS);

defined('SERVER_PATH') or define('SERVER_PATH', ($dir = dirname($_SERVER['SCRIPT_NAME'])) == DS ? '/' : str_replace(DS, '/', $dir) . '/');

require_once CORE_PATH . 'Ar.class.php';

spl_autoload_register('Ar::autoLoader');

set_exception_handler('Ar::exceptionHandler');

set_error_handler('Ar::errorHandler');

Ar::init();
