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
     * @param string  $dir            dir.
     * @param boolean $showServerName showServerName.
     *
     * @return string
     */
    public function serverPath($dir, $showServerName = false)
    {
        return ($showServerName ? $this->serverName() : '') . str_replace(array(realpath($_SERVER['DOCUMENT_ROOT']), DS), array('', '/'), $dir);

    }

    /**
     * pathToDir.
     *
     * @param string $path path.
     *
     * @return string
     */
    public function pathToDir($path)
    {
        if (strpos($path, '/') === 0) :
            $dir = rtrim(realpath($_SERVER['DOCUMENT_ROOT']), DS) . DS;
            $path = trim($path, '/');
            $path = str_replace('/', DS, $path);
            $dir = $dir . $path;
        else :
            $path = str_replace('/', DS, $path);
            $dir = AR_ROOT_PATH . $path;
        endif;

        return $dir;

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
     * parse url rules.
     *
     * @param string $url url.
     *
     * @return string
     */
    public function parseUrlForRules($url)
    {
        $urlRouteRules = arCfg('URL_ROUTE_RULES');
        foreach ($urlRouteRules as $key => &$rules) :
            preg_match_all('|:(.*):|U', $rules['mode'], $match);
            if (!empty($match[1])) :
                $rules['mode'] = preg_replace('|(:.*:)|U', '([a-zA-z0-9]+)', $rules['mode']);
                $urlRegRules = '|' . $rules['mode'] . '|';
                if (preg_match_all($urlRegRules, $url, $matchRules)) :
                    $lengthOfVariable = count($match[1]);
                    for ($i = 0; $i < $lengthOfVariable; $i++) :
                        $rulesKey = $i + 1;
                        $_GET[$match[1][$i]] = $matchRules[$rulesKey][0];
                    endfor;
                    $url = preg_replace('|(.*)' . $rules['mode'] . '(.*)|', "$1" . $key . "$" . ($lengthOfVariable + 2), $url);
                    break;
                else :
                    continue;
                endif;
            endif;
        endforeach;
        return $url;

    }

    /**
     * parse string.
     *
     * @return mixed
     */
    public function parse()
    {
        $requestUrl = $this->parseUrlForRules($_SERVER['REQUEST_URI']);

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

        $requestRoute = array('a_m' => $m, 'a_c' => empty($c) ? AR_DEFAULT_CONTROLLER : $c, 'a_a' => empty($a) ? AR_DEFAULT_ACTION : $a);
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
            $staticMark['getUrlParamArray'] = array_merge($_GET, $staticMark['getUrlParamArray']);
            $staticMark['firstParse'] = false;
        endif;
        return $staticMark['getUrlParamArray'];

    }

    /**
     * url manage.
     *
     * @param string  $urlKey      route key.
     * @param boolean $params  url get param.
     * @param string  $urlMode url mode.
     *
     * @return string
     */
    public function createUrl($urlKey = '', $params = array(), $urlMode = 'NOT_INIT')
    {
        // 路由url
        $url = $urlKey;

        // 路由规则
        $urlRouteRules = arCfg('URL_ROUTE_RULES');

        $defaultModule = arCfg('requestRoute.a_m') == AR_DEFAULT_APP_NAME ? '' : arCfg('requestRoute.a_m');
        if ($urlMode === 'NOT_INIT') :
            $urlMode = arCfg('URL_MODE', 'PATH');
        endif;

        $prefix = rtrim(AR_SERVER_PATH . $defaultModule, '/');

        $urlParam = arCfg('requestRoute');
        $urlParam['a_m'] = $defaultModule;

        if (isset($params['greedyUrl']) && $params['greedyUrl'] === false) :
            // do nothing
        else :
            if ((isset($params['greedyUrl']) && $params['greedyUrl'] === true) || arCfg('URL_GREEDY') === true) :
                unset($params['greedyUrl']);
                unset($_GET['a_m']);
                unset($_GET['a_c']);
                unset($_GET['a_a']);
                // 合并参数
                if (is_array(arGet())) :
                    $getArr = arGet();
                    unset($getArr['a_m']);
                    unset($getArr['a_c']);
                    unset($getArr['a_a']);
                    $params = array_merge($getArr, $params);
                endif;
            endif;
        endif;

        if (empty($url)) :
            if ($urlMode == 'PATH') :
                $url = $prefix;
                $controller = arCfg('requestRoute.a_c');
                $action = arCfg('requestRoute.a_a');
                $url .= '/' . $controller . '/' . $action;
            endif;
        else :
            // url
            if (strpos($url, 'http') === 0) :
                $urlArr = parse_url($url);
                $reBuildUrlArr = $params;
                if (!empty($urlArr['query'])) :
                    parse_str($urlArr['query'], $urlStrArr);
                    $reBuildUrlArr = array_filter(array_merge($params, $urlStrArr));
                    $baseUrl = substr($url, 0, strpos($url, '?'));
                else :
                    $baseUrl = rtrim($url, '?');
                endif;
                $reBuildUrl = $baseUrl . '?' . http_build_query($reBuildUrlArr);
                return $reBuildUrl;
            elseif (strpos($url, '/') === false) :
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

        if ($urlMode != 'PATH') :
            $urlParam = array_filter(array_merge($urlParam, $params));
        endif;

        // 初始化config时
        if (empty($urlMode)) :
            $urlMode = 'PATH';
        endif;
        switch ($urlMode) {

        case 'PATH' :
            // 路由解析
            if (array_key_exists($urlKey, $urlRouteRules)) :
                $url = str_replace($urlKey, $urlRouteRules[$urlKey]['mode'], $url);
                preg_match_all('|:(.*):|U', $url, $match);
                if (!empty($match[1])) :
                    foreach ($match[1] as $variable) :
                        if (array_key_exists($variable, $params)) :
                            $url = str_replace(':' . $variable . ':', $params[$variable], $url);
                            unset($params[$variable]);
                        endif;
                    endforeach;
                endif;
            endif;
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
     * @param mixed  $r         route.
     * @param string $show show string.
     * @param string $time time display.
     * @param string $seg  seg  seg redirect.
     *
     * @return mixed
     */
    public function redirect($r = '', $show = '', $time = '0', $seg = '')
    {
        $show = trim($show);
        $show = preg_replace("/\n/", ' ', $show);
        if (is_string($r)) :
            $url = '';
            if (empty($r)) :
                $urlTemp = arComp('list.session')->get('ar_back_url');
                if ($urlTemp) :
                    $url = $urlTemp;
                    arComp('list.session')->set('ar_back_url', null);
                endif;
            else :
                if ($r == 'up') :
                    if (!empty($_SERVER['HTTP_REFERER'])) :
                        $url = $_SERVER['HTTP_REFERER'];
                    endif;
                endif;
            endif;
        else :
            $route = empty($r[0]) ? '' : $r[0];
            $param = empty($r[1]) ? array() : $r[1];
            // 跳转回来
            if (isset($param['ar_back']) && $param['ar_back'] === true) :
                unset($param['ar_back']);
                arComp('list.session')->set('ar_back_url', $_SERVER['REQUEST_URI']);
            endif;
            $url = arComp('url.route')->createUrl($route, $param);
        endif;
        // search seg if found then render
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
        if ($seg) :
            // filename
            $seg = 'Redirect/' . $seg;
            try {
                arSeg(array('segKey' => $seg, 'url' => $url, 'show' => $show, 'time' => $time));
                exit;
            } catch (ArException $e) {

            }
        endif;
        echo $redirectUrl;
        exit;

    }

}
