<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */

/**
 * class ArApplication
 *
 * default hash comment :
 *
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.net
 */
class ArApplication
{
    /**
     * shutDown function.
     *
     * @return void
     */
    public function shutDown()
    {
        if (AR_DEBUG) :
            if (arCfg('DEBUG_SHOW_EXCEPTION')) :
                arComp('ext.out')->deBug('', 'EXCEPTION', true);
            endif;

            if (arCfg('DEBUG_SHOW_ERROR')) :
                arComp('ext.out')->deBug('', 'ERROR', true);
            endif;

            if (arCfg('DEBUG_SHOW_TRACE'))  :
                arComp('ext.out')->deBug('[SHUTDOWN]', 'TRACE', true);
            endif;

        endif;

        if (AR_RUN_AS_SERVICE_HTTP) :
            arComp('rpc.service')->response('', true);
        endif;

    }

    /**
     * abstract func.
     *
     * @return void
     */
    public function start()
    {
        register_shutdown_function(array($this, 'shutDown'));

    }

}
