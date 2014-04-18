<?php
/**
 * class Db default class\PDO 
 *
 * @author assnr <ycassnr@gmail.com>
 */

namespace Components;

/**
 * Db Component class.
 */
class ArComponent {

    static public $config = array();

    static public function init($config = array())
    {
        static::$config = $config;
        return new static;

    }

    public function setConfig($config = array())
    {
        self::$config = $config;

    }

}
