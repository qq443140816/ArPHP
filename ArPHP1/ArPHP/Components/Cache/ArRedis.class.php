<?php
/**
 * class Db default classPDO
 *
 * @author assnr <ycassnr@gmail.com>
 */

/**
 * cache class.
 */
class ArRedis extends ArCache {

    private $_redis = null;

    static public function init($config = array(), $class = __CLASS__)
    {
        // var_dump($config);
        // exit;
        $obj = parent::init($config, $class);
        $obj->connect();
        return $obj;


    }

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

            if(!empty(self::$config['password']))
                $this->executeCommand('AUTH', array(self::$config['password']));

            if (!empty(self::$config['db']))
                $this->executeCommand('SELECT', array(self::$config['db']));

        endif;

        return $this->_redis;

    }

    public function get($key)
    {
        return $this->executeCommand('GET', array($this->generateUniqueKey($key)));

    }

    public function set($key, $value)
    {
        return (bool)$this->executeCommand('SET',array($this->generateUniqueKey($key), $value));

    }

    public function del($key)
    {
        return (bool)$this->executeCommand('DEL',array($this->generateUniqueKey($key)));

    }

    public function flush()
    {
        return $this->executeCommand('FLUSHDB');

    }

    public function executeCommand($name,$params=array())
    {
        if($this->_redis === null)
            $this->connect();

        array_unshift($params,$name);
        $command='*'.count($params)."\r\n";
        foreach($params as $arg)
            $command.='$'.strlen($arg)."\r\n".$arg."\r\n";

        fwrite($this->_redis, $command);

        return $this->parseResponse();
    }

    private function parseResponse()
    {
        if(($line=fgets($this->_redis)) === false)
            throw new ArException('Failed reading data from redis connection socket.');
        $type=$line[0];
        $line=substr($line,1,-2);
        switch($type)
        {
            case '+': // Status reply
                return true;
            case '-': // Error reply
                throw new ArException('Redis error: '.$line);
            case ':': // Integer reply
                // no cast to int as it is in the range of a signed 64 bit integer
                return $line;
            case '$': // Bulk replies
                if($line=='-1')
                    return null;
                $length=$line+2;
                $data='';
                while($length>0)
                {
                    if(($block = fread($this->_redis,$length))===false)
                        throw new ArException('Failed reading data from redis connection socket.');
                    $data .= $block;
                    $length -= (function_exists('mb_strlen') ? mb_strlen($block,'8bit') : strlen($block));
                }
                return substr($data,0,-2);
            case '*': // Multi-bulk replies
                $count=(int)$line;
                $data=array();
                for($i=0;$i<$count;$i++)
                    $data[]=$this->parseResponse();
                return $data;
            default:
                throw new ArException('Unable to parse data received from redis.');
        }
    }

}
