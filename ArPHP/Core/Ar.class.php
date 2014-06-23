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
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */

/**
 * class class
 *
 * default hash comment :
 *
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.net
 */
class Ar
{
    // applications collections
    static private $_a = array();
    // components collections
    static private $_c = array();
    // config
    static private $_config = array();
    // autoload path
    static public $autoLoadPath;

    /**
     * init application.
     *
     * @return mixed
     */
    static public function init()
    {
        Ar::import(AR_CORE_PATH . 'alias.func.php');

        self::$autoLoadPath = array(
            AR_CORE_PATH,
            AR_FRAME_PATH,
            AR_COMP_PATH,
            AR_COMP_PATH . 'Db' . DS,
            AR_COMP_PATH . 'Url' . DS,
            AR_COMP_PATH . 'Format' . DS,
            AR_COMP_PATH . 'Validator' . DS,
            AR_COMP_PATH . 'Hash' . DS,
            AR_COMP_PATH . 'Rpc' . DS,
            AR_COMP_PATH . 'List' . DS,
            AR_COMP_PATH . 'Cache' . DS,
            AR_COMP_PATH . 'Ext' . DS
        );

        if (!AR_OUTER_START) :
            Ar::c('url.skeleton')->generate();
            self::setConfig('', Ar::import(AR_ROOT_PATH . 'Conf' . DS . 'public.config.php'));
            Ar::c('url.route')->parse();
        else :
            Ar::c('url.skeleton')->generateIntoOther();
            self::setConfig('', Ar::import(AR_MAN_PATH . 'Conf' . DS . 'public.config.php'));
        endif;

        self::$_config = array_merge(
            self::$_config,
            Ar::import(AR_CONFIG_PATH . 'default.config.php', true)
        );

        ArApp::run();

    }

    /**
     * set application.
     *
     * @param string $key key.
     * @param string $val key value.
     *
     * @return void
     */
    static public function setA($key, $val)
    {
        $classkey = strtolower($key);
        self::$_a[$classkey] = $val;

    }

    /**
     * get global config.
     *
     * @param string $ckey          key.
     * @param mixed  $defaultReturn default return value.
     *
     * @return mixed
     */
    static public function getConfig($ckey = '', $defaultReturn = array())
    {
        $rt = array();
        if (empty($ckey)) :
            $rt = self::$_config;
        else :
            if (strpos($ckey, '.') === false) :
                if (isset(self::$_config[$ckey])) :
                    $rt = self::$_config[$ckey];
                endif;
            else :
                $cE = explode('.', $ckey);
                $rt = self::$_config;

                while ($k = array_shift($cE)) :
                    if (!isset($rt[$k])) :
                        if (func_num_args() > 1) :
                            $rt = $defaultReturn;
                        else :
                            $rt = null;

                        endif;
                        break;
                    else :
                        $rt = $rt[$k];
                    endif;
                endwhile;
            endif;

        endif;

        return $rt;

    }

    /**
     * set config.
     *
     * @param string $ckey  key.
     * @param mixed  $value value.
     *
     * @return void
     */
    static public function setConfig($ckey = '', $value = array())
    {
        if (!empty($ckey)) :
            self::$_config[$ckey] = $value;
        else :
            self::$_config = $value;
        endif;

    }

    /**
     * get application.
     *
     * @param string $akey key.
     *
     * @return mixed
     */
    static public function a($akey)
    {
        $akey = strtolower($akey);
        return isset(self::$_a[$akey]) ? self::$_a[$akey] : null;

    }

    /**
     * get component.
     *
     * @param string $cname component.
     *
     * @return mixed
     */
    static public function c($cname)
    {
        $cKey = strtolower($cname);

        if (!isset(self::$_c[$cKey])) :
            $config = self::getConfig('components.' . $cKey . '.config', array());
            self::setC($cKey, $config);
        endif;

        return self::$_c[$cKey];

    }

    /**
     * set component.
     *
     * @param string $component component name.
     * @param array  $config    component config.
     *
     * @return void
     */
    static public function setC($component, array $config = array())
    {
        $cKey = strtolower($component);

        if (isset(self::$_c[$cKey])) :
            return false;
        endif;

        $cArr = explode('.', $component);

        array_unshift($cArr, 'components');

        $cArr = array_map('ucfirst', $cArr);

        $className = 'Ar' . array_pop($cArr);

        $cArr[] = $className;

        $classFile = implode($cArr, '\\');

        self::$_c[$cKey] = call_user_func_array("$className::init", array($config, $className));

    }

    /**
     * autoload register.
     *
     * @param string $class class.
     *
     * @return mixed
     */
    static public function autoLoader($class)
    {
        $class = str_replace('\\', DS, $class);

        if (AR_OUTER_START) :
            $appModule = AR_MAN_PATH;
        else :
            $appModule = arCfg('requestRoute.m') . DS;
        endif;

        array_push(self::$autoLoadPath, $appModule);

        $appConfigFile = $appModule . 'Conf' . DS . 'app.config.php';
        $appConfig = self::import($appConfigFile, true);

        if (is_array($appConfig)) :
            self::setConfig('', array_merge(self::getConfig(), $appConfig));
        endif;

        if (preg_match("#[A-Z]{1}[a-z0-9]+$#", $class, $match)) :
            $appEnginePath = $appModule . $match[0] . DS;

            $extPath = $appModule . 'Ext' . DS;

            array_push(self::$autoLoadPath, $appEnginePath, $extPath);
        endif;


        foreach (self::$autoLoadPath as $path) :
            $classFile = $path . $class . '.class.php';
            if (is_file($classFile)) :
                include_once $classFile;
                $rt = true;
                break;
            endif;
        endforeach;

        if (empty($rt)) :
            trigger_error('class : ' . $class . ' does not exist !', E_USER_ERROR);
            exit;
        endif;

    }

    /**
     * set autoLoad path.
     *
     * @param string $path path.
     *
     * @return void
     */
    static public function importPath($path)
    {
        array_push(self::$autoLoadPath, rtrim($path, DS) . DS);

    }

    /**
     * import file or path.
     *
     * @param string  $path     import path.
     * @param boolean $allowTry allow test exist.
     *
     * @return mixed
     */
    static public function import($path, $allowTry = false)
    {
        static $holdFile = array();
        if (strpos($path, DS) === false) :
            $fileName = str_replace(array('c.', 'ext.', 'app.', '.'), array('Controller.', 'Extensions.', rtrim(AR_ROOT_PATH, DS) . '.', DS), $path) . '.class.php';
        else :
            $fileName = $path;
        endif;

        if (is_file($fileName)) :
            $file = include_once $fileName;
            if ($file === true) :
                return $holdFile[$fileName];
            else :
                $holdFile[$fileName] = $file;
                return $file;
            endif;
        else :
            if ($allowTry) :
                return array();
            else :
                throw new ArException('import not found file :' . $fileName);
            endif;
        endif;

    }

    /**
     * url manage.
     *
     * @param string  $url    url.
     * @param boolean $params url get param.
     *
     * @return string
     */
    static public function createUrl($url = '', $params = array())
    {
        $defaultModule = arCfg('requestRoute.m') == AR_DEFAULT_APP_NAME ? '' : arCfg('requestRoute.m');

        $urlMode = arCfg('URL_MODE', 'PATH');

        $prefix = rtrim(AR_SERVER_PATH . $defaultModule, '/');

        $urlParam = arCfg('requestRoute');
        $urlParam['m'] = $defaultModule;

        if (empty($url)) :
            if ($urlMode == 'PATH') :
                $url = $prefix;
                $controller = arCfg('requestRoute.c');
                $action = arCfg('requestRoute.a');
                $url .= '/' . $controller . '/' . $action;
            endif;
        else :
            if (strpos($url, '/') === false) :
                if ($urlMode != 'PATH') :
                    $urlParam['a'] = $url;
                else :
                    $url = $prefix . '/' . arCfg('requestRoute.c') . '/' . $url;
                endif;
            elseif (strpos($url, '/') === 0) :
                if ($urlMode != 'PATH') :
                    $eP = explode('/', ltrim($url, '/'));
                    $urlParam['m'] = $eP[0];
                    $urlParam['c'] = $eP[1];
                    $urlParam['a'] = $eP[2];
                else :
                    $url = ltrim($url, '/');
                    $url = AR_SERVER_PATH . $url;
                endif;
            else :
                if ($urlMode != 'PATH') :
                    $eP = explode('/', $url);
                    $urlParam['c'] = $eP[0];
                    $urlParam['a'] = $eP[1];
                else :
                    $url = $prefix . '/' . $url;
                endif;
            endif;

        endif;

        if (!empty($params['greedyUrl']) && $params['greedyUrl']) :
            unset($params['greedyUrl']);
            unset($_GET['m']);
            unset($_GET['c']);
            unset($_GET['a']);
            $params = array_merge($_GET, $params);
        endif;
        if ($urlMode != 'PATH') :
            $urlParam = array_filter(array_merge($urlParam, $params));
        endif;

        switch ($urlMode) {
        case 'PATH' :
            foreach ($params as $pkey => $pvalue) :
                if (!$pvalue && !is_numeric($pvalue)) :
                    continue;
                endif;
                $url .= '/' . $pkey . '/' . $pvalue;
            endforeach;
            break;
        case 'QUERY' :
            $url = arComp('url.route')->host() . '?' . http_build_query($urlParam);
            break;
        case 'FULL' :
            $url = arComp('url.route')->host(true) . '?' . http_build_query($urlParam);
            break;
        }

        return $url;

    }

    /**
     * exception handler.
     *
     * @param object $e Exception.
     *
     * @return void
     */
    static public function exceptionHandler($e)
    {
        echo get_class($e) . ' : ' . $e->getMessage();

    }

    /**
     * error handler.
     *
     * @param string $errno   errno.
     * @param string $errstr  error msg.
     * @param string $errfile error file.
     * @param string $errline error line.
     *
     * @return mixed
     */
    static public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) :
            // This error code is not included in error_reporting
            return;
        endif;
        switch ($errno) {
        case E_USER_ERROR:
            echo "<b>ERROR</b> [$errno] $errstr<br />\n";
            echo "  Fatal error on line $errline in file $errfile";
            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            exit(1);
            break;

        case E_USER_WARNING:
            echo "<b>WARNING</b> [$errno] $errstr<br />\n";
            echo " on line $errline in file $errfile <br />\n";
            break;

        case E_USER_NOTICE:
            echo "<b>NOTICE</b> [$errno] $errstr<br />\n";
            echo " on line $errline in file $errfile <br />\n";
            break;

        default:
            echo "Unknown error type: [$errno] $errstr";
            echo " on line $errline in file $errfile <br />\n";
            break;
        }

        /* Don't execute PHP internal error handler */
        return true;

    }

}
