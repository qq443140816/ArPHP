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
        self::$autoLoadPath = array(
            CORE_PATH,
            FRAME_PATH,
            COMP_PATH,
            COMP_PATH . 'Db' . DS,
            COMP_PATH . 'Url' . DS,
            COMP_PATH . 'Format' . DS,
            COMP_PATH . 'Validator' . DS,
            COMP_PATH . 'Hash' . DS,
            COMP_PATH . 'Rpc' . DS,
            COMP_PATH . 'List' . DS,
            COMP_PATH . 'Cache' . DS,
            COMP_PATH . 'Ext' . DS
        );

        Ar::c('url.skeleton')->generate(DEFAULT_APP_NAME);

        self::setConfig('', Ar::import(ROOT_PATH . 'Conf' . DS . 'public.config.php'));

        Ar::c('url.route')->parse();

        Ar::import(CORE_PATH . 'alias.func.php');

        self::$_config = array_merge(
            self::$_config,
            Ar::import(CONFIG_PATH . 'default.config.php', true)
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

        $m = self::getConfig('requestRoute');

        if (!empty($m['m'])) :
            $appModule = ROOT_PATH . $m['m'] . DS;
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
            throw new ArException('class : ' . $class . ' does not exist !');
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
        if (strpos($path, DS) === false) :
            $fileName = str_replace(array('c.', 'ext.', 'app.', '.'), array('Controller.', 'Extensions.', rtrim(ROOT_PATH, DS) . '.', DS), $path) . '.class.php';
        else :
            $fileName = $path;
        endif;

        if (is_file($fileName)) :
            $file = include_once $fileName;
            if ($file === true) :
                return array();
            else :
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
        $prefix = rtrim(SERVER_PATH . (arCfg('requestRoute.m') == DEFAULT_APP_NAME ? '' : arCfg('requestRoute.m')), '/');

        $url = ltrim($url, '/');

        if (empty($url)) :
            $url = $prefix;

            $url .= '/' . arCfg('requestRoute.c') . '/' . arCfg('requestRoute.a');

        else :
            if (strpos($url, '/') === false) :
                $url = $prefix . '/' . arCfg('requestRoute.c') . '/' . $url;
            else :
                $url = $prefix . '/' . $url;
            endif;

        endif;

        foreach ($params as $pkey => $pvalue) :
            $url .= '/' . $pkey . '/' . $pvalue;
        endforeach;

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
     * @param string $errno  errno.
     * @param string $errstr error msg.
     *
     * @return void
     */
    static public function errorHandler($errno, $errstr)
    {
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";

    }

}
