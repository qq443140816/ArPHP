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
     * @return string
     */
    public function host()
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/' . trim(str_replace(array('/', '\\', DS), '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

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

        if (($root = dirname($phpSelf)) != '/') :
            $requestUrl = preg_replace("#^$root#", '', $requestUrl);
        endif;

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
