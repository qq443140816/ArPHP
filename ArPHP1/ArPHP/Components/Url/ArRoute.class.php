<?php
class ArRoute extends ArComponent {
    
       
    public function parse()
    {
        $requestUrl = $_SERVER['REQUEST_URI'];

        $phpSelf = $_SERVER['SCRIPT_NAME'];

        if (strpos($requestUrl, $phpSelf) !== false)
            $requestUrl = str_replace($phpSelf, '', $requestUrl);

        if (($pos = strpos($requestUrl, '?')) !== false)
            $requestUrl = substr($requestUrl, 0, $pos);

        if (($root = dirname($phpSelf)) != '/')
            $requestUrl = preg_replace("#^$root#", '', $requestUrl);

        $requestUrl = trim($requestUrl, '/');
        $pathArr = explode('/', $requestUrl);
        $temp = array_shift($pathArr);

        $m = in_array($temp, Ar::getConfig('moduleLists', array())) ? $temp : APP_NAME;

        $c = in_array($temp, Ar::getConfig('moduleLists', array())) ? array_shift($pathArr) : $temp;

        $a = array_shift($pathArr);

        while ($gkey = array_shift($pathArr)) :
            $_GET[$gkey] = array_shift($pathArr);
        endwhile;

        $requestRoute = array('m' => $m, 'c' => empty($c) ? 'Index' : $c, 'a' => empty($a) ? 'index' : $a);

        Ar::setConfig('requestRoute', $requestRoute);

        return $requestRoute;

    }

    

}
