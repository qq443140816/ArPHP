<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */



/**
 * Default Controller of webapp.
 */
class IndexController extends ArController {

    /**
     * just the example of get contents.
     *
     * @return void
     */
    public function indexAction()
    {
        echo 'wap index';
        
    }

    public function testAction()
    {

        $apiR = Ar::c('rpc.json')->callApi('categories');
        
        var_dump($apiR);
        exit;


    }


}
