<?php
/**
 * class Db default class\PDO 
 *
 * @author assnr <ycassnr@gmail.com>
 */

namespace Components\Rpc;

/**
 * abstract Db class.
 */
class ArJson extends ArText {
    public function remoteCall($url)
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
    
    public function parse($parseStr)
    {
        return $this->parseJson($parseStr);

    }

    public function parseJson($parseStr)
    {
        return json_decode($parseStr, 1);

    }

    public function getApi($api, $params)
    {
        if (empty(self::$config['remotePrefix']))
            throw new \Core\ArException('config not found field remotePrefix');
            
        $prefix = rtrim(self::$config['remotePrefix'], '/') . '/' . trim($api, '/');

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