<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Component.List
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */

/**
 * ArLog
 *
 * default hash comment :
 *
 * <code>
 * #1. arComp('list.log')->record(array('132', 'ff'));
 * #2. arComp('list.log')->record('test log');
 * </code>
 *
 * @category ArPHP
 * @package  Core.Component.List
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.net
 */
class ArLog extends ArList
{
    // cache path
    protected $logPath;

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

        $obj->logPath = empty($obj->config['logPath']) ? (AR_OUTER_START ? AR_ROOT_PATH . 'Log' . DS : arCfg('PATH.LOG')) : $obj->config['logPath'];

        if(!is_dir($obj->logPath)) :
            mkdir($obj->logPath, 0777, true);
        endif;

        return $obj;

    }

    /**
     * 记录日志.
     *
     * @param mixed $data 记录信息.
     *
     * @return mixed
     */
    public function record($data = '')
    {
        if (is_array($data)) :
            $data = var_export($data, true);
        endif;

        $data = '------' . date('Y-m-d H:i:s', time()) . ' ' . time() . "------\n" . $data . "\n";

        return file_put_contents($this->generateLogFileName(), $data, LOCK_EX|FILE_APPEND);

    }

    /**
     * 生成日志文件名称.
     *
     * @return void
     */
    protected function generateLogFileName()
    {
        return $this->logPath . date('Ymd') . '.log.txt';

    }

}
