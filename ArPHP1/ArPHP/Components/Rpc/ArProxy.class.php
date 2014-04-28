<?php
class ArProxy extends ArText {

    $domainInfo = array();

    public function remoteCall($url)
    {
        $init = curl_init($url);

        curl_setopt_array($init, array(
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => 1,
                )
            );
        $rtStr = curl_exec($init);
        curl_close($init);

        return $rtStr;

    }

    public function callApi($url)
    {
        $this->parse($url);

        $source = $this->remoteCall($url);

    }

    public function parse($url)
    {
        //$pInfo = pathinfo($url);
        //$ext = $pInfo['extension'];
        $uInfo = parse_url($url);

        $this->domainInfo['host'] = $uInfo['host'];
        //$this->domainInfo['refer'] = $uInfo['host'];

    }
