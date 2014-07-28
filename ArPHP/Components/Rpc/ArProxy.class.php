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
 * ArProxy
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
class ArProxy extends ArText
{
    // domain
    protected $domainInfo = array();
    // default mime type
    protected $mimeType = 'text/html';

    /**
     * remote call.
     *
     * @param string $url call url.
     *
     * @return mixed
     */
    protected function remoteCall($url)
    {
        $init = curl_init($url);

        curl_setopt_array($init, array(
                CURLOPT_RETURNTRANSFER => 1,

                CURLOPT_AUTOREFERER => 1,

                CURLOPT_RETURNTRANSFER => 1,

                CURLOPT_HTTPHEADER => array(
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 ',
                        'Host' => $this->domainInfo['host'],
                        'Referer' => $this->domainInfo['referer'],
                    )
            )
        );

        $rtStr = curl_exec($init);

        $info = curl_getinfo($init);

        if (!empty($info['content_type'])) :
            $this->mimeType = $info['content_type'];
        endif;

        curl_close($init);

        return $rtStr;

    }

    /**
     * call api
     *
     * @param string $url url.
     *
     * @return void
     */
    public function callApi($url)
    {

        $this->parse($url);

        $source = $this->remoteCall($url);

        header('Content-Type:' . $this->mimeType);

        echo $source;

    }

    /**
     * parse return data.
     *
     * @param string $url url resource.
     *
     * @return void
     */
    protected function parse($url)
    {
        $uInfo = parse_url($url);

        if (empty($uInfo['host']) || empty($uInfo['scheme'])) :
            throw new ArException('url ' . $url . ' may have a valid host');
        endif;

        $this->domainInfo['host'] = $uInfo['host'];

        $this->domainInfo['referer'] = $uInfo['scheme'] . '://'. $uInfo['host'];

    }

}
