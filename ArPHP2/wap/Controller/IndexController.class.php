<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

use \Core\Ar;

/**
 * Default Controller of webapp.
 */
class IndexController extends \Core\ArController {

    /**
     * just the example of get contents.
     *
     * @return void
     */
    public function testAction()
    {
       echo 'wap c'; 

       myModel::model()->hh();

    }

}
