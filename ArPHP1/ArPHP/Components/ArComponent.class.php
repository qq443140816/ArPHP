<?php
/**
 * class Db default classPDO 
 *
 * @author assnr <ycassnr@gmail.com>
 */

/**
 * Db Component class.
 */
class ArComponent {

    static protected $config = array();

    static public function init($config = array(), $class = __CLASS__)
    {
        self::$config = $config;
        return new $class;

    }

    public function setConfig($config = array())
    {
        self::$config = $config;

    }

}
