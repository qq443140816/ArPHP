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
    // config
    public $currentConfig = array();
    // pdo
    protected $pdo = null;
    // pdoStatement
    protected $pdoStatement = null;

    /**
     * construct.
     *
     * @param array $config config.
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->currentConfig = $config;
        try {
            $this->pdo = new PDO($config['dsn'], $config['user'], $config['pass'], $config['option']);
        } catch (PDOException $e) {
            throw $e;
        }

    }

    /**
     * read connection.
     *
     * @param string $name connection name.
     *
     * @return void
     */
    public function read($name = 'default')
    {
        if (!isset(self::$readConnections[$name]) && isset($this->config['read'][$name])) :
            $this->addReadConnection($name);
        endif;

        if (isset(self::$readConnections[$name])) :
            return self::$readConnections[$name];
        else :
            throw new ArDbException('dbReadConfig not hava a param ' . $name, 1);
        endif;

    }

    /**
     * read connection.
     *
     * @param string $name connection name.
     *
     * @return mixed
     */
    public function write($name = 'default')
    {
        if (!isset(self::$writeConnections[$name]) && isset($this->config['write'][$name])) :
            $this->addWriteConnection($name);
        endif;

        if (isset(self::$writeConnections[$name])) :
            return self::$writeConnections[$name];
        else :
            throw new ArDbException('dbWriteConfig not hava a param ' . $name, 1);
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
        if (!isset(self::$writeConnections[$name])) :

            $dsn = $this->config['read'][$name]['dsn'];
            $user = $this->config['read'][$name]['user'];
            $pass = $this->config['read'][$name]['pass'];
            $option = $this->config['read'][$name]['option'];
            self::$writeConnections[$name] = new $this->driverName($dsn, $user, $pass, $option);

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

            $dsn = $this->config['write'][$name]['dsn'];
            $user = $this->config['write'][$name]['user'];
            $pass = $this->config['write'][$name]['pass'];
            $option = $this->config['write'][$name]['option'];
            self::$writeConnections[$name] = new $this->driverName($dsn, $user, $pass, $option);

        endif;

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
        $this->pdo->setAttribute($attribute, $value);
        return $this;

    }

    /**
     * trans.
     *
     * @return boolean
     */
    public function transBegin()
    {
        return $this->pdo->beginTransaction();

    }

    /**
     * trans commit.
     *
     * @return boolean
     */
    public function transCommit()
    {
        return $this->pdo->commit();

    }

    /**
     * trans roolbank.
     *
     * @return boolean
     */
    public function transRoolBack()
    {
        return $this->pdo->rollBack();

    }

    /**
     * if in trans.
     *
     * @return boolean
     */
    public function inTransaction()
    {
        // This method actually seems to work fine on PHP5.3.5 (and probably a few older versions).
        return $this->pdo->inTransaction();

    }

}
