<?php
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

/**
 * class webApplication.
*/
class ArWebApplication extends ArApplication {

    public $route = array();

    public function start()
    {
        if (ini_get('session.auto_start') == 0) :
            session_start();
        endif;

        register_shutdown_function(array($this, 'shutDown'));

        $this->processRequest();

    }

    public function processRequest()
    {
        $this->runController(Ar::getConfig('requestRoute'));

    }

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
        $this->_c = new $class;
        $this->_c->init();
        $action = ($a = empty($route['a']) ? 'index' : $route['a']) . 'Action';

        $this->route['a'] = $a;

        if (is_callable(array($this->_c, $action)))
            $this->_c->$action();
        else
            throw new ArException('Action ' . $action . ' not found');

    }

}
