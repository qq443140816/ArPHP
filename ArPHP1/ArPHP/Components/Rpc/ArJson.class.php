<?php
class ArJson extends ArText {

    protected function remoteCall($url)
    {
        $init = curl_init($url);

        curl_setopt_array($init, array(
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => 1,
                )
            );
        $rtStr = curl_exec($init);

        // if($rtStr === false)
            // echo 'Curl error: ' . curl_error($init);

        curl_close($init);

        return $rtStr;

    }

    protected function parse($parseStr)
    {
        return $this->parseJson($parseStr);

    }

    protected function parseJson($parseStr)
    {
        return json_decode($parseStr, 1);

    }

    protected function getApi($api, $params)
    {
        $prefix = rtrim(empty(self::$config['remotePrefix']) ? arComp('url.route')->host() : self::$config['remotePrefix'], '/') . '/' . trim($api, '/');

        foreach ($params as $pkey => $param) :
            $prefix .= '/' . $pkey . '/' . $param;
        endforeach;
        return $prefix;

    }

    public function callApi($api, $params = array())
    {
        $url = $this->getApi($api, $params);

        $result = $this->remoteCall($url);

        return $this->parse($result);

    }

}
