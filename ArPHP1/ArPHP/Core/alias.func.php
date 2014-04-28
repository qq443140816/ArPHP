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
