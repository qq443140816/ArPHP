<?php 
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

namespace Core;

/**
 * class webApplication.
*/
class ArWebApplication extends ArApplication {

    public $route = array();

    public function start()
    {
        register_shutdown_function(array($this, 'shutDown'));

        $this->processRequest();

    }

    public function processRequest()
    {
        $routeArr = Ar::c('url.route')->parse();

        $this->runController($routeArr);

    }

    public function runController($route)
    {
        $path = 'app.c.';

        if (empty($route['c'])) :
            $path .= $c = 'Index';
        else :
            $path .= $c = ucfirst($route['c']);
        endif;

        $this->route['c'] = $c;

        $path .= 'Controller';

        Ar::import($path);

        if (class_exists(($class = $c . 'Controller'))) :
            $this->_c = new $class;
            $this->_c->init();
            $action = ($a = empty($route['a']) ? 'Index' : $route['a']) . 'Action';

            $this->route['a'] = $a;

            if (is_callable(array($this->_c, $action)))
                $this->_c->$action();
            else
                throw new ArException('Action ' . $action . ' not found');
        else :
            throw new ArException('Controller ' . $class . ' not found');
        endif;

    }
    
}
