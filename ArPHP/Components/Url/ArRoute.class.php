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
 * ArRoute
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
class ArRoute extends ArComponent
{
    /**
     * serverPath.
     *
     * @param string $dir dir.
     *
     * @return string
     */
    public function serverPath($dir)
    {
        return str_replace(array(realpath($_SERVER['DOCUMENT_ROOT']), DS), array('', '/'), $dir);

    }

    /**
     * host.
     *
     * @param boolean $scriptName return scriptname.
     *
     * @return string
     */
    public function host($scriptName = false)
    {
        $host = $this->serverName() . '/' . trim(str_replace(array('/', '\\', DS), '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        $host = rtrim($host, '/');
        if ($scriptName) :
            $host .= '/' . basename($_SERVER['SCRIPT_NAME']);
        endif;
        return $host;

    }

    /**
     * return server name
     *
     * @return string
     */
    public function serverName()
    {
        return 'http://' . $_SERVER['HTTP_HOST'];

    }

    /**
     * parse string.
     *
     * @return mixed
     */
    public function parse()
    {
        $requestUrl = $_SERVER['REQUEST_URI'];

        $phpSelf = $_SERVER['SCRIPT_NAME'];

        if (strpos($requestUrl, $phpSelf) !== false) :
            $requestUrl = str_replace($phpSelf, '', $requestUrl);
        endif;

        if (($pos = strpos($requestUrl, '?')) !== false) :
            $queryStr = substr($requestUrl, $pos + 1);
            $requestUrl = substr($requestUrl, 0, $pos);
        endif;

        if (($root = dirname($phpSelf)) != '/' && $root != '\\') :
            $requestUrl = preg_replace("#^{$root}#", '', $requestUrl);
        endif;

        $requestUrl = trim($requestUrl, '/');
        $pathArr = explode('/', $requestUrl);
        $temp = array_shift($pathArr);

        $m = in_array($temp, Ar::getConfig('moduleLists', array())) ? $temp : AR_DEFAULT_APP_NAME;

        $c = in_array($temp, Ar::getConfig('moduleLists', array())) ? array_shift($pathArr) : $temp;

        $a = array_shift($pathArr);

        while ($gkey = array_shift($pathArr)) :
            $_GET[$gkey] = array_shift($pathArr);
        endwhile;

        if (!empty($queryStr)) :
            parse_str($queryStr, $query);
            $_GET = array_merge($_GET, $query);
        endif;

        if (arGet('a_m')) :
            $m = arGet('a_m');
        endif;
        if (arGet('a_c')) :
            $c = arGet('a_c');
        endif;
        if (arGet('a_a')) :
            $a = arGet('a_a');
        endif;

        $requestRoute = array('a_m' => $m, 'a_c' => empty($c) ? 'Index' : $c, 'a_a' => empty($a) ? 'index' : $a);
        Ar::setConfig('requestRoute', $requestRoute);

        return $requestRoute;

    }

    /**
     * generate url get parame.
     *
     * @return array
     */
    public function parseGetUrlIntoArray()
    {
        static $staticMark = array(
            'firstParse' => true,
            'getUrlParamArray' => array(),
        );
        if ($staticMark['firstParse']) :
            $parseUrl = parse_url($_SERVER['REQUEST_URI']);

            if (empty($parseUrl['query'])) :

            else :
                parse_str($parseUrl['query'], $params);
                $staticMark['getUrlParamArray'] = $params;
            endif;
            $staticMark['firstParse'] = false;
        endif;
        return $staticMark['getUrlParamArray'];

    }

    /**
     * url manage.
     *
     * @param string  $url     url.
     * @param boolean $params  url get param.
     * @param string  $urlMode url mode.
     *
     * @return string
     */
    public function createUrl($url = '', $params = array(), $urlMode = 'NOT_INIT')
    {
        $defaultModule = arCfg('requestRoute.a_m') == AR_DEFAULT_APP_NAME ? '' : arCfg('requestRoute.a_m');
        if ($urlMode === 'NOT_INIT') :
            $urlMode = arCfg('URL_MODE', 'PATH');
        endif;

        $prefix = rtrim(AR_SERVER_PATH . $defaultModule, '/');

        $urlParam = arCfg('requestRoute');
        $urlParam['a_m'] = $defaultModule;

        if (empty($url)) :
            if ($urlMode == 'PATH') :
                $url = $prefix;
                $controller = arCfg('requestRoute.a_c');
                $action = arCfg('requestRoute.a_a');
                $url .= '/' . $controller . '/' . $action;
            endif;
        else :
            if (strpos($url, '/') === false) :
                if ($urlMode != 'PATH') :
                    $urlParam['a_a'] = $url;
                else :
                    $url = $prefix . '/' . arCfg('requestRoute.a_c') . '/' . $url;
                endif;
            elseif (strpos($url, '/') === 0) :
                if ($urlMode != 'PATH') :
                    $eP = explode('/', ltrim($url, '/'));
                    $urlParam['a_m'] = $eP[0];
                    $urlParam['a_c'] = isset($eP[1]) ? $eP[1] : null;
                    $urlParam['a_a'] = isset($eP[2]) ? $eP[2] : null;
                else :
                    $url = ltrim($url, '/');
                    $url = AR_SERVER_PATH . $url;
                endif;
            else :
                if ($urlMode != 'PATH') :
                    $eP = explode('/', $url);
                    $urlParam['a_c'] = $eP[0];
                    $urlParam['a_a'] = $eP[1];
                else :
                    $url = $prefix . '/' . $url;
                endif;
            endif;

        endif;

        if (!empty($params['greedyUrl']) && $params['greedyUrl']) :
            unset($params['greedyUrl']);
            unset($_GET['a_m']);
            unset($_GET['a_c']);
            unset($_GET['a_a']);
            if (is_array(arGet())) :
                $params = array_merge(arGet(), $params);
            endif;
        endif;
        if ($urlMode != 'PATH') :
            $urlParam = array_filter(array_merge($urlParam, $params));
        endif;

        // 初始化config时
        if (empty($urlMode)) :
            $urlMode = 'PATH';
        endif;

        switch ($urlMode) {

        case 'PATH' :
            foreach ($params as $pkey => $pvalue) :
                if (!$pvalue && !is_numeric($pvalue)) :
                    continue;
                endif;
                $url .= '/' . $pkey . '/' . $pvalue;
            endforeach;
            break;
        case 'QUERY' :
            $url = arComp('url.route')->host() . '?' . http_build_query($urlParam);
            break;
        case 'FULL' :
            $url = arComp('url.route')->host(true) . '?' . http_build_query($urlParam);
            break;
        }

        return $url;

    }

    /**
     * redirect function.
     *
     * @param mixed  $r    route.
     * @param string $show show string.
     * @param string $time time display.
     *
     * @return mixed
     */
    public function redirect($r = '', $show = '', $time = '0')
    {
        if (is_string($r)) :
            $url = $r;
        else :
            $route = empty($r[0]) ? '' : $r[0];
            $param = empty($r[1]) ? array() : $r[1];
            $url = arComp('url.route')->createUrl($route, $param);
        endif;

        $redirectUrl = <<<str
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Refresh" content="$time;URL=$url" />
</head>
<body>
$show<a href="$url">立即跳转</a>
</body>
</html>
str;
        echo $redirectUrl;
        exit;

    }

}
