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

        if (arGet('m')) :
            $m = arGet('m');
        endif;
        if (arGet('c')) :
            $c = arGet('c');
        endif;
        if (arGet('a')) :
            $a = arGet('a');
        endif;

        $requestRoute = array('m' => $m, 'c' => empty($c) ? 'Index' : $c, 'a' => empty($a) ? 'index' : $a);
        Ar::setConfig('requestRoute', $requestRoute);

        return $requestRoute;

    }

    /**
     * url manage.
     *
     * @param string  $url    url.
     * @param boolean $params url get param.
     *
     * @return string
     */
    public function createUrl($url = '', $params = array())
    {
        $defaultModule = arCfg('requestRoute.m') == AR_DEFAULT_APP_NAME ? '' : arCfg('requestRoute.m');

        $urlMode = arCfg('URL_MODE', 'PATH');

        $prefix = rtrim(AR_SERVER_PATH . $defaultModule, '/');

        $urlParam = arCfg('requestRoute');
        $urlParam['m'] = $defaultModule;

        if (empty($url)) :
            if ($urlMode == 'PATH') :
                $url = $prefix;
                $controller = arCfg('requestRoute.c');
                $action = arCfg('requestRoute.a');
                $url .= '/' . $controller . '/' . $action;
            endif;
        else :
            if (strpos($url, '/') === false) :
                if ($urlMode != 'PATH') :
                    $urlParam['a'] = $url;
                else :
                    $url = $prefix . '/' . arCfg('requestRoute.c') . '/' . $url;
                endif;
            elseif (strpos($url, '/') === 0) :
                if ($urlMode != 'PATH') :
                    $eP = explode('/', ltrim($url, '/'));
                    $urlParam['m'] = $eP[0];
                    $urlParam['c'] = $eP[1];
                    $urlParam['a'] = $eP[2];
                else :
                    $url = ltrim($url, '/');
                    $url = AR_SERVER_PATH . $url;
                endif;
            else :
                if ($urlMode != 'PATH') :
                    $eP = explode('/', $url);
                    $urlParam['c'] = $eP[0];
                    $urlParam['a'] = $eP[1];
                else :
                    $url = $prefix . '/' . $url;
                endif;
            endif;

        endif;

        if (!empty($params['greedyUrl']) && $params['greedyUrl']) :
            unset($params['greedyUrl']);
            unset($_GET['m']);
            unset($_GET['c']);
            unset($_GET['a']);
            $params = array_merge($_GET, $params);
        endif;
        if ($urlMode != 'PATH') :
            $urlParam = array_filter(array_merge($urlParam, $params));
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



}
