<?php 
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

/**
 * class Ar.
 */
class ArModel {

    private static $_models = array(

        );


    static public function model($class = __CLASS__)
    {
        $key = strtolower($class);

        if (!isset(self::$_models[$key]))
            self::$_models[$key] = new $class;

        return self::$_models[$key];

    }

}
