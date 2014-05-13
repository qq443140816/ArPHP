<?php
class ArRoute extends ArComponent {

    public function serverPath($dir)
    {
        return str_replace(array(realpath($_SERVER['DOCUMENT_ROOT']), DS), array('', '/'), $dir);

    }

    public function host()
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/' . trim(str_replace(array('/', '\\', DS), '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

    }

    /**
     * url parse .
     *
     * import muti url format.
     */
    public function parse()
    {
        $requestUrl = $_SERVER['REQUEST_URI'];

        $phpSelf = $_SERVER['SCRIPT_NAME'];

        if (strpos($requestUrl, $phpSelf) !== false)
            $requestUrl = str_replace($phpSelf, '', $requestUrl);

        if (($pos = strpos($requestUrl, '?')) !== false) :
            $queryStr = substr($requestUrl, $pos + 1);
            $requestUrl = substr($requestUrl, 0, $pos);
        endif;

        if (($root = dirname($phpSelf)) != '/')
            $requestUrl = preg_replace("#^$root#", '', $requestUrl);

        $requestUrl = trim($requestUrl, '/');
        $pathArr = explode('/', $requestUrl);
        $temp = array_shift($pathArr);

        $m = in_array($temp, Ar::getConfig('moduleLists', array())) ? $temp : DEFAULT_APP_NAME;

        $c = in_array($temp, Ar::getConfig('moduleLists', array())) ? array_shift($pathArr) : $temp;

        $a = array_shift($pathArr);

        while ($gkey = array_shift($pathArr)) :
            $_GET[$gkey] = array_shift($pathArr);
        endwhile;

        if (!empty($queryStr)) :
            parse_str($queryStr, $query);
            $_GET = array_merge($_GET, $query);
        endif;

        $requestRoute = array('m' => $m, 'c' => empty($c) ? 'Index' : $c, 'a' => empty($a) ? 'index' : $a);

        Ar::setConfig('requestRoute', $requestRoute);

        return $requestRoute;

    }

}
