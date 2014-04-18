<?php
/**
 * class Db default class\PDO 
 *
 * @author assnr <ycassnr@gmail.com>
 */

namespace Components\Rpc;

/**
 * abstract Db class.
 */
abstract class ArText extends \Components\ArComponent {
    static public $config = array();
    
    abstract public function callApi($api);

    abstract public function parse($parseStr);

}
    