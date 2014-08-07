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
 * class ArApplicationServiceHttp
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
class ArApplicationServiceHttp extends ArApplicationService
{
    /**
     * extend abstract func.
     *
     * @return void
     */
    public function start()
    {
        parent::start();
        $data = $this->parseHttpServiceHanlder();
        return $this->runService($data);

    }

    public function parseHttpServiceHanlder()
    {
        if ($ws = arPost('ws')) :
            if (!$ws = arComp('rpc.api')->decrypt($ws)) :
                throw new ArServiceException('ws query format incorrect error');
            endif;

            if (empty($ws['class']) || empty($ws['method']) || !isset($ws['param'])) :
                throw new ArServiceException('ws query param missing error');
            endif;

            return array(
                    'class' => $ws['class'],
                    'method' => $ws['method'],
                    'param' => $ws['param'],
                );

        else :
            throw new ArServiceException('ws query ws info missing error');
        endif;

    }

    public function runService($ws)
    {
        $service = $ws['class'] . 'Service';
        $method = $ws['method'] . 'Worker';
        $param = $ws['param'];

        try {
            $serviceHolder = new $service;
            $serviceHolder->init();
        } catch(Exeception $e) {
            throw new ArServiceException('ws service "' . $service . '" does not exist ');
        }

        if (!is_callable(array($serviceHolder, $method))) :
            throw new ArServiceException('ws service do not hava a method ' . $method);
        endif;
        call_user_func_array(array($serviceHolder, $method), $param);
        $serviceHolder->notResponseToClientHanlder();

    }

}
