<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Component
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.org
 */

/**
 * Http Text
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
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.org
 */
class ArApi extends ArComponent
{
    // get url method
    public $method = 'get';

    //curl options
    public $curlOptions = array();

    // 设置cookie文件
    public function handleCookie($file = '')
    {
        if (!$file) :
            $file = AR_ROOT_PATH . 'cookiefile';
        endif;

        if (!file_exists($file)) :
            file_put_contents($file, '');
        endif;

        // curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/cookieFileName");
        // curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookieFileName");
        $this->curlOptions[CURLOPT_COOKIEJAR] = $file;
        $this->curlOptions[CURLOPT_COOKIEFILE] = $file;

    }

    /**
     * remote call.
     *
     * @param string $url resource url.
     *
     * @return string
     */
    public function remoteCall($url, $params = array(), $method = '')
    {
        if ($method) :
            $this->method = $method;
        else :
            $this->method = empty($this->config['method']) ? 'get' : $this->config['method'];
        endif;

        $options = array(CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => 1);

        if ($this->method == 'post') :
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $params;
        else :
            if (is_array($params)) :
                $queryString = http_build_query($params);
                if (strpos($url, '?') === false) :
                    $url .= '?' . $queryString;
                else :
                    $url .= '&' . $queryString;
                endif;
            endif;
        endif;

        $init = curl_init($url);

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

        if ($curlInfo = curl_getinfo($init)) :
            switch ($curlInfo['http_code']) {
                case '404':
                    throw new ArException('Curl error: ' . 'url ' . '"' . $curlInfo['url'] . '" not found');
                    break;
                default:
                    break;
            }
        endif;

        curl_close($init);

        return $rtStr;

    }

    /**
     * call api.
     *
     * @param string $api api.
     *
     * @return mixed
     */
    public function callApi($api)
    {

    }

    /**
     * parse.
     *
     * @param string $parseStr string.
     *
     * @return mixed
     */
    protected function parse($parseStr)
    {

    }

    /**
     * encrypt cache data.
     *
     * @param mixed $data cache date.
     *
     * @return string
     */
    public function encrypt($data)
    {
        return arComp('hash.mcrypt')->encrypt(serialize($data));

    }

    /**
     * decrypt cache data.
     *
     * @param mixed $data description.
     *
     * @return mixed
     */
    public function decrypt($data)
    {
        return unserialize(arComp('hash.mcrypt')->decrypt($data));

    }

}
