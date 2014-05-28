<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Components.Cache
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */

/**
 * class redis cache
 *
 * default hash comment :
 *
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.net
 */
class ArRedis extends ArCache
{
    // object redis
    private $_redis = null;

    /**
     * initialization function.
     *
     * @param mixed  $config config.
     * @param string $class  hold class.
     *
     * @return Object
     */
    static public function init($config = array(), $class = __CLASS__)
    {
        $obj = parent::init($config, $class);
        $obj->connect();
        return $obj;

    }

    /**
     * redis connect.
     *
     * @return mixed
     */
    public function connect()
    {
        if (!$this->_redis) :
            $this->_redis = stream_socket_client(
                self::$config['host'] . ':' . self::$config['port'],
                $errorNumber,
                $errorDescription,
                empty(self::$config['timeout']) ? ini_get('default_socket_timeout') : self::$config['timeout']
            );

            if (!$this->_redis) :
                throw new ArException('Failed to connect to redis: ' . $errorDescription . '[code]:' . $errorNumber, (int)$errorNumber);
            endif;

            if(!empty(self::$config['password'])) :
                $this->executeCommand('AUTH', array(self::$config['password']));
            endif;

            if (!empty(self::$config['db'])) :
                $this->executeCommand('SELECT', array(self::$config['db']));
            endif;

        endif;

        return $this->_redis;

    }

    /**
     * cache get
     *
     * @param string $key cache key.
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->executeCommand('GET', array($this->generateUniqueKey($key)));

    }

    /**
     * cache set.
     *
     * @param string $key   cache key.
     * @param mixed  $value value.
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        return (bool)$this->executeCommand('SET', array($this->generateUniqueKey($key), $value));

    }

    /**
     * cache del.
     *
     * @param string $key cache key.
     *
     * @return mixed
     */
    public function del($key)
    {
        return (bool)$this->executeCommand('DEL', array($this->generateUniqueKey($key)));

    }

    /**
     * cache flush.
     *
     * @return mixed
     */
    public function flush()
    {
        return $this->executeCommand('FLUSHDB');

    }

    /**
     * default function.
     *
     * @param string $name   command.
     * @param mixed  $params select params.
     *
     * @return mixed
     */
    public function executeCommand($name, $params = array())
    {
        if($this->_redis === null) :
            $this->connect();
        endif;

        array_unshift($params, $name);
        $command= '*' . count($params) . "\r\n";
        foreach($params as $arg) :
            $command .= '$' . strlen($arg) . "\r\n" . $arg . "\r\n";
        endforeach;

        fwrite($this->_redis, $command);

        return $this->parseResponse();
    }

    /**
     * parse res.
     *
     * @return mixed
     */
    protected function parseResponse()
    {
        if(($line = fgets($this->_redis)) === false) :
            throw new ArException('Failed reading data from redis connection socket.');
        endif;

        $type = $line[0];
        $line = substr($line, 1, -2);

        switch ($type) {
            // Status reply
        case '+' :
            return true;
            // Error reply
        case '-' :
            throw new ArException('Redis error: ' . $line);
        // Integer reply
        case ':' :
            // no cast to int as it is in the range of a signed 64 bit integer
            return $line;
        // Bulk replies
        case '$' :
            if ($line == '-1') :
                return null;
            endif;
            $length = $line + 2;
            $data = '';
            while ($length > 0) :

                if(($block = fread($this->_redis, $length)) === false) :
                    throw new ArException('Failed reading data from redis connection socket.');
                endif;

                $data .= $block;

                $length -= (function_exists('mb_strlen') ? mb_strlen($block, '8bit') : strlen($block));
            endwhile;
            return substr($data, 0, -2);
        case '*' :
            $count = (int)$line;
            $data = array();
            for ( $i = 0; $i < $count; $i++ ) :
                $data[] = $this->parseResponse();
            endfor;

            return $data;
        default:
            throw new ArException('Unable to parse data received from redis.');
        }

    }

}
