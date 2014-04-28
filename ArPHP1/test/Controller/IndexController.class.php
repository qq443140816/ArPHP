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
        $url = $_GET['url'];
        arComp('rpc.proxy')->callApi($url);

    }

}
