<?php
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

/**
 * class Ar.
 */
class Ar {

    static private $_a = array();

    static private $_c = array();

    static private $_config = array();

    static public function init()
    {
        self::$_config = array_merge(
                Ar::import(CONFIG_PATH . 'default.config.php'),
                Ar::import(ROOT_PATH . 'Conf' . DS . 'public.config.php', true)
            );

        Ar::import(CORE_PATH . 'alias.func.php');

        ArApp::run();

    }

    static public function setA($key, $val)
    {
        self::$_a[$key] = $val;

    }

    static public function getConfig($ckey = '', $rt = array())
    {
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
                    $rt = $rt[$k];                   
                endwhile;
            endif;

        endif;

        return $rt;

    }

    static public function setConfig($ckey = '', $value = array())
    {
        if (!empty($ckey))
            self::$_config[$ckey] = $value;
        else
            self::$_config = $value;

    }

    static public function a($akey)
    {
        return isset(self::$_a[$akey]) ? self::$_a[$akey] : null;

    }

    static public function c($cname)
    {
        if (!isset(self::$_c[$cname])) :

            $cKey = strtolower($cname);

            $confC = self::getConfig('components');

            $cArr = explode('.', $cKey);

            $conf = self::getConfig(strtolower($cArr[0]));

            if (!empty($confC[$cArr[0]]) && !empty($confC[$cArr[0]]['config']))
                $config = $confC[$cArr[0]]['config'];
            else
                $config = array();
            self::setC($cname, $config);
        endif;

        return self::$_c[$cname];

    }

    static public function setC($component, $config = array())
    {
        $cKey = strtolower($component);

        if (isset(self::$_c[$cKey]))
            return false;

        $cArr = explode('.', $component);
        

        array_unshift($cArr, 'components');

        $cArr = array_map('ucfirst', $cArr);

        $className = 'Ar' . array_pop($cArr);

        $cArr[] = $className;

        $classFile = implode($cArr, '\\');

        self::$_c[$cKey] = call_user_func_array("$className::init", array($config, $className));

    }
    
    static public function autoLoader($class)
    {
        $class = str_replace('\\', DS, $class);
        $autoLoadPaths = array(
            CORE_PATH, 
            FRAME_PATH, 
            COMP_PATH, 
            COMP_PATH . 'Db' . DS,
            COMP_PATH . 'Url' . DS,
            COMP_PATH . 'Format' . DS,
            COMP_PATH . 'Validator' . DS,
            COMP_PATH . 'Hash' . DS,
            COMP_PATH . 'Rpc' . DS,
        );

        $m = self::getConfig('requestRoute');

        if (!empty($m['m'])) :

            $appMoudle = ROOT_PATH . $m['m'] . DS;
            array_push($autoLoadPaths, $appMoudle);

            $appConfigFile = $appMoudle . 'Conf' . DS . 'app.config.php';
            $appConfig = self::import($appConfigFile, true);

            if (is_array($appConfig))
                self::setConfig('', array_merge(self::getConfig(), $appConfig));

            if (preg_match("#[A-Z]{1}[a-z0-9]+$#", $class, $match)) :
                $appEnginePath = $appMoudle . $match[0] . DS;

                $extPath = $appMoudle . 'Ext' . DS;

                array_push($autoLoadPaths, $appEnginePath, $extPath);
            endif;

        endif;

        foreach ($autoLoadPaths as $path) :
            $classFile = $path . $class . '.class.php';
            if (is_file($classFile)) :
                require_once $classFile;
                $rt = true;
                break;
            endif;
        endforeach;

        if (empty($rt))
            throw new ArException('class : ' . $class . ' does not exist !');

    }

    static public function import($path, $allowTry = false)
    {
        if (strpos($path, DS) === false)
            $fileName = str_replace(array('c.', 'ext.', 'app.', '.'), array('Controller.', 'Extensions.', rtrim(ROOT_PATH, DS) . '.', DS), $path) . '.class.php';
        else
            $fileName = $path;

        if (is_file($fileName)) :
            $file = require_once($fileName);
            if ($file === true) :
                return array();
            else :
                return $file;
            endif;
        else :
            if ($allowTry)
                return array();
            else
                throw new ArException('import not found file :' . $fileName);
        endif;

    }

    static public function exceptionHandler($e)
    {
        echo get_class($e) . ' : ' . $e->getMessage();

    }

    static public function errorHandler($errno, $errstr)
    {
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";

    }
    
}
