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

    abstract function get($key);

    abstract function set($key, $value);

    abstract function del($key);

    abstract function flush();

    protected function generateUniqueKey($keyName)
    {
        return md5($keyName);

    }

    protected function encrypt($data)
    {
        return serialize($data);

    }

    protected function decrypt($data)
    {
        return unserialize($data);

    }

}
