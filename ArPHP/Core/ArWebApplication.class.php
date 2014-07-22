<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */

/**
 * application
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
class ArWebApplication extends ArApplication
{
    // route container
    public $route = array();

    /**
     * start function.
     *
     * @return void
     */
    public function start()
    {
        if (ini_get('session.auto_start') == 0) :
            session_start();
        endif;

        register_shutdown_function(array($this, 'shutDown'));

        $this->processRequest();

    }

    /**
     * process.
     *
     * @return void
     */
    public function processRequest()
    {
        $this->runController(Ar::getConfig('requestRoute'));

    }

    /**
     * default controller.
     *
     * @param string $route route.
     *
     * @return mixed
     */
    public function runController($route)
    {
        Ar::setConfig('requestRoute', $route);

        if (empty($route['c'])) :
            $c = 'Index';
        else :
            $c = ucfirst($route['c']);
        endif;

        $this->route['c'] = $c;
        $class = $c . 'Controller';

        if (class_exists($class)) :
            $this->_c = new $class;
            $this->_c->init();
            $action = ($a = empty($route['a']) ? 'index' : $route['a']) . 'Action';

            $this->route['a'] = $a;
            if (is_callable(array($this->_c, $action))) :
                try {
                    $this->_c->$action();
                    exit;
                } catch (ArException $e) {
                    if (!AR_AS_OUTER_FRAME) :
                        throw new ArException($e->getMessage());
                    endif;
                }
            else :
                if (!AR_AS_OUTER_FRAME) :
                    throw new ArException('Action ' . $action . ' not found');
                endif;
            endif;
        endif;

    }

}
