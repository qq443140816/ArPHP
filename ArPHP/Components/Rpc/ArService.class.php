<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Component
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */

/**
 * Service
 *
 * default hash comment :
 *
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.Component
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.net
 */
class ArService extends ArApi
{
    protected $remoteWsFile = '';

    /**
     * initialization for component.
     *
     * @param mixed  $config config.
     * @param string $class  instanse class.
     *
     * @return Object
     */
    static public function init($config = array(), $class = __CLASS__)
    {
        $obj = parent::init($config, $class);
        $obj->setRemoteWsFile();
        return $obj;

    }

    public function setRemoteWsFile($wsFile = '')
    {
        if (empty($wsFile)) :
            $this->remoteWsFile = empty($this->config['wsFile']) ? arComp('url.route')->host() . '/arws.php' : $this->config['wsFile'];
        else :
            $this->remoteWsFile = $wsFile;
        endif;

    }

    public function setAuthUserSignature($sign = array())
    {
        $this->remoteQueryUrlSign = $sign;

    }

    public function gRemoteWsUrl()
    {
        return $this->remoteWsFile . '?' . http_build_query($this->remoteQueryUrlSign);

    }

    public function __call($name, $args = array())
    {
        $remoteQueryUrlSign = array();
        $remoteQueryData = array();
        if (substr($name, 0, 2) === 'Ws') :
            $remoteQueryData['class'] = ltrim($name, 'Ws');
            $remoteQueryData['method'] = $args[0];
            $remoteQueryData['param'] = empty($args[1]) ? array() : $args[1];
        else :
            throw new ArException("Service do not have a method " . $name);
        endif;

        $this->setAuthUserSignature($remoteQueryUrlSign);

        $postServiceData = array('ws' => $this->encrypt($remoteQueryData));

        return $this->callApi($this->gRemoteWsUrl(), $postServiceData);

    }

    public function callApi($url, $args = array())
    {
        $this->method = 'post';
        $response = $this->remoteCall($url, $args);
        return $this->processResponse($response);

    }

   /**
     * response to client.
     *
     * @param mixed $data response data.
     *
     * @return void
     */
    public function response($data = '')
    {
        $remoteStdOutMsg = ob_get_contents();
        ob_end_clean();
        if (AR_DEBUG && $remoteStdOutMsg) :
            $backInfo = array(
                'data' => $data,
                'stdOutMsg' => $remoteStdOutMsg,
            );
            $data = $backInfo;
        endif;
        echo $this->encrypt($data);

    }

    protected function processResponse($response = '')
    {
        if (empty($response)) :
            throw new ArException('Remote Service Error (  Service Response Empty )', '1012');
        elseif (preg_match('#.*error.*on line.*#', $response)):
            throw new ArException('Remote Service Error ( ' . $response . ' )', '1101');
        endif;
        $remoteBackResult = $this->decrypt($response);
        if (is_array($remoteBackResult) &&!empty($remoteBackResult['error_msg'])) :
            throw new ArException('Remote Service Error ( ' . $remoteBackResult['error_msg'] . ' )', $remoteBackResult['error_code']);
        endif;
        return $remoteBackResult;

    }

}
