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
class BaseController extends \Core\ArController {

    public function indexAction()
    {
        echo 'index';
        $result = Ar::c('db.dbmysql')->table('t1')->queryAll();
        var_dump($result);

    }

}