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
 * component holder.
 *
 * @param string $name cname.
 *
 * @return Object
 */
function arComp($name = '')
{
    return Ar::c($name);

}

/**
 * alas to cfg.
 *
 * @param string $name key of config.
 *
 * @return mixed
 */
function arCfg($name = '')
{
    return Ar::getConfig($name);

}

/**
 * route holder.
 *
 * @param string $name   route path.
 * @param mixed  $params route param.
 *
 * @return string
 */
function arU($name = '', $params = array())
{
    return Ar::createUrl($name, $params);

}

/**
 * filter $_GET.
 *
 * @param string $key     get key.
 * @param mixed  $default return value.
 *
 * @return mixed
 */
function arGet($key = '', $default = null)
{
    $ret = array();

    if (empty($key)) :
        $ret = $_GET;
    else :
        if (!isset($_GET[$key])) :
            $ret = null;
        else :
            $ret = $_GET[$key];
        endif;
    endif;

    $ret = arComp('format.format')->addslashes($ret);
    if (empty($ret) && !is_numeric($ret)) :
        $ret = $default;
    endif;
    return $ret;

}

/**
 * filter $_POST.
 *
 * @param string $key post key.
 *
 * @return mixed
 */
function arPost($key = '')
{
    $ret = array();

    if (empty($key)) :
        $ret = $_POST;
    else :
        if (empty($_POST[$key])) :
            $ret = null;
        else :
            $ret = $_POST[$key];
        endif;
    endif;

    return arComp('format.format')->addslashes($ret);

}

/**
 * load other module.
 *
 * @param string $module name.
 *
 * @return mixed
 */
function arLm($module)
{
    return Ar::importPath(ROOT_PATH . str_replace('.', DS, $module));

}

/**
 * echo for default
 *
 * @param string $echo    echo.
 * @param string $default default out.
 *
 * @return void
 */
function arEcho($echo = '', $default = '')
{
    if (is_array($default)) :
        $echo = $default[(int)$echo];
    else :
        if (empty($echo)) :
            $echo = $default;
        endif;
    endif;

    echo $echo;

}
