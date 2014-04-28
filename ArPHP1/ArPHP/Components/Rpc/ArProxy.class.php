<?php
class ArProxy extends ArText {

    public $domainInfo = array();

    public $mimeType = 'text/html';

    public function remoteCall($url)
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

        if (!empty($info['content_type']))
            $this->mimeType = $info['content_type'];

        curl_close($init);

        return $rtStr;

    }

    public function callApi($url)
    {

        $this->parse($url);

        $source = $this->remoteCall($url);

        header('Content-Type:' . $this->mimeType);

        echo $source;


    }

    public function parse($url)
    {
        $uInfo = parse_url($url);
        if (empty($uInfo['host']) || empty($uInfo['scheme']))
            throw new ArException('url ' . $url . ' may have a valid host');
            
        $this->domainInfo['host'] = $uInfo['host'];

        $this->domainInfo['referer'] = $uInfo['scheme'] . '://'. $uInfo['host'];

    }

}
