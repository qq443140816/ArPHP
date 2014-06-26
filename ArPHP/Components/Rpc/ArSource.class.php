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
 * ArJson
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
class ArSource extends ArText
{
    // get url method
    public $method = 'get';

    /**
     * remote call.
     *
     * @param string $url resource url.
     *
     * @return string
     */
    protected function remoteCall($url, $params = array())
    {
        $init = curl_init($url);
        $options = array(CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => 1);
        if ($this->method == 'post') :
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $params;
        endif;
        curl_setopt_array($init, $options);
        $rtStr = curl_exec($init);

        if ($rtStr === false) :
            throw new ArException('Curl error: ' . curl_error($init));
        endif;

        curl_close($init);

        return $rtStr;

    }

    /**
     * parse return data.
     *
     * @param string $parseStr not parsed string.
     *
     * @return string
     */
    protected function parse($parseStr)
    {
        return $parseStr;

    }

    /**
     * getApi.
     *
     * @param string $api    api.
     * @param mixed  $params param.
     *
     * @return string
     */
    protected function getApi($api, $params)
    {
        $prefix = rtrim(empty(self::$config['remotePrefix']) ? arComp('url.route')->ServerName() : self::$config['remotePrefix'], '/');
        $this->method = empty(self::$config['method']) ? 'get' : self::$config['method'];
        switch ($this->method) {
        case 'get' :
            if (empty(self::$config['remotePrefix'])) :
                $prefix .= arU($api, $params);
            else :
                $prefix .= $api;
            endif;
            break;
        case 'post' :
            $prefix .= empty(self::$config['remotePrefix']) ? arU($api) : $api;
            break;
        }
        return trim($prefix, '/');

    }

    /**
     * call api.
     *
     * @param string $api    api.
     * @param mixed  $params parames.
     *
     * @return mixed
     */
    public function callApi($api, $params = array())
    {
        $url = $this->getApi($api, $params);

        $result = $this->remoteCall($url, $params);

        return $this->parse($result);

    }

}
