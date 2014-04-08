<?php 
/**
 * ArPHP for PHP .
 *
 * @author ycassnr <ycassnr@gmail.com>
 */



/**
 * class webApplication.
*/
abstract class ArApplication {

    public function shutDown()
    {
        // maybe log;
        // echo microtime(true) - START_TIME;
        
    }

    abstract public function start();

}
