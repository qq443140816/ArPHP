<?php
/**
 * class Db default classPDO 
 *
 * @author assnr <ycassnr@gmail.com>
 */

/**
 * abstract Db class.
 */
class ArDb extends ArComponent {

    static public $readConnections = array();
    static public $writeConnections = array();

    public $currentConfig = array();

    protected $_pdo = null;
    protected $_pdoStatement = null;

    public function __construct(array $config)
    {
        $this->currentConfig = $config;

        set_exception_handler(array($this, 'exceptionHandler'));
        try {

            $this->_pdo = new PDO($config['dsn'], $config['user'], $config['pass'], $config['option']);

        } catch (PDOException $e) {
            throw $e;

        }

    }

    public function read($name = 'default')
    {
        if (!isset(self::$readConnections[$name]) && isset(self::$config['read'][$name]))
            $this->addReadConnection($name);

        if (isset(self::$readConnections[$name]))
            return self::$readConnections[$name];
        else
            throw new ArDbException('dbReadConfig not hava a param ' . $name, 1);

    }

    public function write($name = 'default')
    {
        if (!isset(self::$writeConnections[$name]) && isset(self::$config['write'][$name]))
            $this->addWriteConnection($name);

        if (isset(self::$writeConnections[$name]))
            return self::$writeConnections[$name];
        else
            throw new ArDbException('dbWriteConfig not hava a param ' . $name, 1);

    }

    protected function addReadConnection($name = '')
    {
        if (!isset(self::$writeConnections[$name])) :

            $dsn = self::$config['read'][$name]['dsn'];
            $user = self::$config['read'][$name]['user'];
            $pass = self::$config['read'][$name]['pass'];
            $option = self::$config['read'][$name]['option'];
            self::$writeConnections[$name] = new $this->driverName($dsn, $user, $pass, $option);

        endif;

    }

    protected function addWriteConnection($name = '')
    {
        if (!isset(self::$writeConnections[$name])) :

            $dsn = self::$config['write'][$name]['dsn'];
            $user = self::$config['write'][$name]['user'];
            $pass = self::$config['write'][$name]['pass'];
            $option = self::$config['write'][$name]['option'];
            self::$writeConnections[$name] = new $this->driverName($dsn, $user, $pass, $option);

        endif;

    }

    public function setPdoAttributes($attribute , $value = '')
    {
        $this->_pdo->setAttribute($attribute, $value);
        return $this;

    }

    public function transBegin()
    {
        return $this->_pdo->beginTransaction();

    }

    public function transCommit()
    {
        return $this->_pdo->commit();

    }

    public function transRoolBack()
    {
        return $this->_pdo->rollBack();

    }

    public function inTransaction()
    {
        // This method actually seems to work fine on PHP5.3.5 (and probably a few older versions).
        return $this->_pdo->inTransaction();

    }

    public function exceptionHandler($e)
    {
        echo get_class($e) . ' Msg : ' . $e->getMessage() . "\n" .' Code : '. $e->getCode() . "\n" . 'LastSql : ' . $this->lastSql;

    }

}
