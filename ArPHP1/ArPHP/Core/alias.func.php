<?php
/**
 * alias of Core function.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

function arComp($name = '')
{
    return Ar::c($name);

}

function arCfg($name = '')
{
    return Ar::getConfig($name);

}

function arU($name = '', $params = array())
{
    return Ar::createUrl($name, $params);

}

function arGet($key = '', $default = null)
{
    $ret = array();

    if (empty($key)) :
        $ret = $_GET;
    else :
        if (!isset($_GET[$key]))
            $ret = null;
        else
            $ret = $_GET[$key];
    endif;

    $ret = arComp('format.format')->addslashes($ret);
    if (empty($ret) && !is_numeric($ret)) :
        $ret = $default;
    endif;
    return $ret;

}

function arPost($key = '')
{
    $ret = array();

    if (empty($key)) :
        $ret = $_POST;
    else :
        if (empty($_POST[$key]))
            $ret = null;
        else
            $ret = $_POST[$key];
    endif;

    return arComp('format.format')->addslashes($ret);

}

function arLm($module)
{
    return Ar::importPath(ROOT_PATH . str_replace('.', DS, $module));

}