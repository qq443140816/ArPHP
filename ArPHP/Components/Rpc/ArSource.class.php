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

    //curl options
    public $curlOptions = array();

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

        if ($this->curlOptions) :
            foreach ($this->curlOptions as $ckey => $opt) :
                $options[$ckey] = $opt;
            endforeach;
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
        $prefix = rtrim(empty($this->config['remotePrefix']) ? arComp('url.route')->ServerName() : $this->config['remotePrefix'], '/');
        $this->method = empty($this->config['method']) ? 'get' : $this->config['method'];

        if (!empty($params['curlOptions'])) :
            $this->curlOptions = $params['curlOptions'];
            unset($params['curlOptions']);
        endif;

        switch ($this->method) {
        case 'get' :
            if (empty($this->config['remotePrefix'])) :
                $prefix .= arU($api, $params);
            else :
                $prefix .= '/' . ltrim($api, '/');
            endif;
            break;
        case 'post' :
            $prefix .= empty($this->config['remotePrefix']) ? arU($api) : ('/' . ltrim($api, '/'));
            break;
        }
        $url = trim($prefix, '/');

        return $this->remoteCall($url, $params);

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
        $result = $this->getApi($api, $params);

        return $this->parse($result);

    }

}
