<?php
/**
 * class Db default classPDO 
 *
 * @author assnr <ycassnr@gmail.com>
 */

/**
 * abstract Db class.
 */
abstract class ArCache extends ArComponent {
    
    static protected $config = array();

    abstract function getValue($key);

    abstract function setValue($key, $value);

    abstract function addValue($key, $value);

    abstract function flush();

}
