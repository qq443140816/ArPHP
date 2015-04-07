<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Component.Db
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */

/**
 * ArDb
 *
 * default hash comment :
 *
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.Component.Db
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.net
 */
class ArDb extends ArComponent
{
    // read
    static public $readConnections = array();
    // write
    static public $writeConnections = array();
    // connectionMark
    public $connectionMark = 'read.default';
    // pdoStatement
    protected $pdoStatement = null;

    protected $driverName = 'PDO';

    /**
     * read connection.
     *
     * @param string  $name                connection name.
     * @param boolean $returnPdoConnection return db type.
     *
     * @return void
     */
    public function read($name = 'default', $returnPdoConnection = false)
    {
        $this->connectionMark = 'read.' . $name;

        // 默认取第一个
        if (empty($this->config['read']['default'])) :
            $tempCk = array_keys($this->config['read']);
            $fistrKey = array_shift($tempCk);
            $this->config['read']['default'] = $this->config['read'][$fistrKey];
        endif;

        if (!isset(self::$readConnections[$name]) && isset($this->config['read'][$name])) :
            $this->addReadConnection($name);
        endif;
        if (!isset(self::$readConnections[$name])) :
            throw new ArDbException('dbReadConfig not hava a param ' . $name, 1);
        endif;
        if ($returnPdoConnection) :
            return self::$readConnections[$name];
        else :
            return $this;
        endif;

    }

    /**
     * read connection.
     *
     * @param string  $name                connection name.
     * @param boolean $returnPdoConnection return db type.
     *
     * @return mixed
     */
    public function write($name = 'default', $returnPdoConnection = false)
    {
        $this->connectionMark = 'write.' . $name;
        if (!isset(self::$writeConnections[$name]) && isset($this->config['write'][$name])) :
            $this->addWriteConnection($name);
        endif;
        if (!isset(self::$writeConnections[$name])) :
            throw new ArDbException('dbWriteConfig not hava a param ' . $name, 1);
        endif;
        if ($returnPdoConnection) :
            return self::$writeConnections[$name];
        else :
            return $this;
        endif;


    }


    /**
     * read connection.
     *
     * @param string $name connection name.
     *
     * @return void
     */
    protected function addReadConnection($name = '')
    {
        if (!isset(self::$readConnections[$name])) :
            self::$readConnections[$name] = $this->createConnection('read.' . $name);
        endif;

    }

    /**
     * read connection.
     *
     * @param string $name connection name.
     *
     * @return void
     */
    protected function addWriteConnection($name = '')
    {
        if (!isset(self::$writeConnections[$name])) :
            self::$writeConnections[$name] = $this->createConnection('write.' . $name);
        endif;

    }

    /**
     * read connection.
     *
     * @param string $name connection name.
     *
     * @return PDO
     */
    protected function createConnection($name = '')
    {
        list($dataBaseType, $mark) = explode('.', $name);
        $dsn = $this->config[$dataBaseType][$mark]['dsn'];
        $user = $this->config[$dataBaseType][$mark]['user'];
        $pass = $this->config[$dataBaseType][$mark]['pass'];
        $option = $this->config[$dataBaseType][$mark]['option'];
        try {
            return new $this->driverName($dsn, $user, $pass, $option);
        } catch (PDOException $e) {
            throw $e;
        }

    }

    protected function getCurrentConfig($configKey = '')
    {
        list($dataBaseType, $mark) = explode('.', $this->connectionMark);

        if (empty($this->config[$dataBaseType][$mark])) :
            throw new ArDbException("Db Config Mark Error : " . $this->connectionMark . ' Required');
        endif;
        if (empty($configKey)) :
            return $this->config[$dataBaseType][$mark];
        else :
            if (array_key_exists($configKey, $this->config[$dataBaseType][$mark])) :
                return $this->config[$dataBaseType][$mark][$configKey];
            else :
                throw new ArDbException("Db Config Lost Key Error : " . $configKey . ' Required');
            endif;
        endif;


    }

    /**
     * pdo connection.
     *
     * @return PDO
     */
    protected function getDbConnection()
    {
        if (empty($this->connectionMark) || !strpos($this->connectionMark, '.')) :
            throw new ArDbException("Connection Mark Error : " . $this->connectionMark);
        endif;
        list($dataBaseType, $mark) = explode('.', $this->connectionMark);

        switch ($dataBaseType) {
            case 'read':
                return $this->read($mark, true);
                break;
            case 'write':
                return $this->write($mark, true);
                break;

            default:
                throw new ArDbException("Connection Mark DataBase Type Error : " . $this->connectionMark);
                break;

        }

    }

    /**
     * read connection.
     *
     * @param string $attribute attr.
     * @param string $value     value.
     *
     * @return Object
     */
    public function setPdoAttributes($attribute , $value = '')
    {
        $this->getDbConnection()->setAttribute($attribute, $value);
        return $this;

    }

    /**
     * trans.
     *
     * @return boolean
     */
    public function transBegin()
    {
        return $this->getDbConnection()->beginTransaction();

    }

    /**
     * trans commit.
     *
     * @return boolean
     */
    public function transCommit()
    {
        return $this->getDbConnection()->commit();

    }

    /**
     * trans roolbank.
     *
     * @return boolean
     */
    public function transRollBack()
    {
        return $this->getDbConnection()->rollBack();

    }

    /**
     * if in trans.
     *
     * @return boolean
     */
    public function inTransaction()
    {
        // This method actually seems to work fine on PHP5.3.5 (and probably a few older versions).
        return $this->getDbConnection()->inTransaction();

    }

}
