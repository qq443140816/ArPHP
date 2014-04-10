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
    public function indexAction()
    {
        var_dump(Ar::c('validator.validator')->checkMutiArray(array(array(2),array(231), 4)));

    }

}
