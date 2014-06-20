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
        'PATH' => array(
            'PUBLIC' => AR_SERVER_PATH . arCfg('requestRoute.m') . '/Public/',
            'GPUBLIC' => AR_SERVER_PATH . 'Public/',
            'CACHE' => AR_ROOT_PATH . arCfg('requestRoute.m') . DS . 'Cache' . DS,
            'VIEW' => AR_ROOT_PATH . arCfg('requestRoute.m') . DS . 'View' . DS,
            'UPLOAD' => AR_ROOT_PATH . arCfg('requestRoute.m') . DS . 'Upload' . DS,
            'EXT' => AR_ROOT_PATH . arCfg('requestRoute.m') . DS . 'Ext' . DS,
        ),
        'URL_MODE' => 'PATH',
   );