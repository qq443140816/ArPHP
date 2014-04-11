<?php 
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

namespace Core;

/**
 * class Ar.
 */
class ArModel {

    private static $_models = array(

        );


    static public function model()
    {
        $key = strtolower(get_called_class());
        
        if (!isset(self::$_models[$key]))
            self::$_models[$key] = new static;

        return self::$_models[$key];

    }

}
