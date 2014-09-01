<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Conf
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */
return array(
        // path
        'PATH' => array(
            'PUBLIC' => AR_SERVER_PATH . arCfg('requestRoute.a_m') . '/Public/',
            'GPUBLIC' => AR_SERVER_PATH . 'Public/',
            'CACHE' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Cache' . DS,
            'LOG' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Log' . DS,
            'VIEW' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'View' . DS,
            'UPLOAD' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Upload' . DS,
            'EXT' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Ext' . DS,
        ),

        // url
        'URL_MODE' => 'PATH',

        // debug
        'DEBUG_SHOW_TRACE' => true,
        'DEBUG_SHOW_ERROR' => true,
        'DEBUG_SHOW_EXCEPTION' => true,

   );
