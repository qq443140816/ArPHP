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
    return Ar::createUrl($name, $params = array());

}

function arGet($key = '')
{
    $ret = array();

    if (empty($key)) :
        $ret = $_GET;
    else :
        if (empty($_GET[$key]))
            $ret = null;
        else
            $ret = $_GET[$key];
    endif;

    return arComp('format.format')->addslashes($ret);

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

