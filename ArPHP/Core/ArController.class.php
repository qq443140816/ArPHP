<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */

/**
 * class ArController
 *
 * default hash comment :
 *
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.net
 */
class ArController
{
    // assign container
    protected $assign = array();

    /**
     * init function.
     *
     * @return void
     */
    public function init()
    {

    }

    /**
     * magic function.
     *
     * @param string $name   funcName.
     * @param mixed  $params funcParames.
     *
     * @return mixed
     */
    public function __call($name, $params)
    {
        if ($name == 'module') :
            if (!isset($this->assign['module'])) :
                $module = empty($params[0]) ? arCfg('requestRoute.c') : $params[0];
                $m =  $module . 'Module';
                $this->assign['module'] = new $m;
            endif;
            return $this->assign['module'];
        elseif ($name == 'model') :
            if (!$this->assign['model']) :
                $model = empty($params[0]) ? arCfg('requestRoute.c') : $params[0];
                $m = $model . 'Model';
                $this->assign['model'] = ArModel::model($m);
            endif;
            return $this->assign['model'];
        else :
            throw new ArException("class do not have a method $name");
        endif;

    }

    /**
     * default function.
     *
     * @param array $vals value.
     *
     * @return void
     */
    public function assign(array $vals)
    {
        foreach ($vals as $key => $val) :
            $this->assign[$key] = $val;
        endforeach;

    }

    /**
     * show string function.
     *
     * @param string $ckey          key.
     * @param string $defaultReturn return.
     * @param string $show          display string.
     *
     * @return mixed
     */
    public function show($ckey = '', $defaultReturn = '', $show = true)
    {
        $rt = array();
        if (empty($ckey)) :
            $rt = $this->assign;
        else :
            if (strpos($ckey, '.') === false) :
                if (isset($this->assign[$ckey])) :
                    $rt = $this->assign[$ckey];
                endif;
            else :
                $cE = explode('.', $ckey);
                $rt = $this->assign;
                while ($k = array_shift($cE)) :
                    if (!isset($rt[$k])) :
                        $rt = $defaultReturn;
                        break;
                    else :
                        $rt = $rt[$k];
                    endif;
                endwhile;
            endif;
        endif;
        if ($show) :
            echo $rt;
        else :
            return $rt;
        endif;

    }

    /**
     * display function.
     *
     * @param string $view  view template.
     * @param string $class fromClass.
     *
     * @return mixed
     */
    public function display($view = '', $class = __CLASS__)
    {
        $viewPath = '';
        $viewBasePath = arCfg('PATH.VIEW');
        $overRide = false;
        $absolute = false;

        if (strpos($view, '@') === 0) :
            $overRide = true;
            $view = ltrim($view, '@');
        endif;

        $r = Ar::a('ArWebApplication')->route;


        if (empty($view)) :
            $viewPath .= $r['c'] . DS . $r['a'];
        elseif(strpos($view, '/') !== false) :
            if (substr($view, 0, 1) == '/') :
                $absolute = true;
                $viewPath .= str_replace('/', DS, ltrim($view, '/'));
            else :
                $viewPath .= $r['c'] . DS  . str_replace('/', DS, ltrim($view, '/'));
            endif;
            if (substr($view, -1) == '/') :
                $viewPath .= $r['a'];
            endif;
        else :
            $viewPath .= $r['c'] . DS . $view;
        endif;

        $currentC = $tempC = $r['c'] . 'Controller';

        $preFix = '';

        if (!$absolute) :
            while ($cP = get_parent_class($tempC)) :
                if (!in_array(substr($cP, 0, -10), array('Ar', 'Base'))) :
                    $preFix = substr($cP, 0, -10) . DS . $preFix;
                    if (!$overRide && method_exists($cP, $r['a'] . 'Action')) :
                        $viewPath = str_replace(substr($tempC, 0, -10) . DS, '', $viewPath);
                    endif;
                    $tempC = $cP;
                else :
                    break;
                endif;
            endwhile;
        endif;

        $viewFile = $viewBasePath . $preFix . $viewPath . '.php';

        if (is_file($viewFile)) :
            extract($this->assign);
            include $viewFile;
        else :
            throw new ArException('view : ' . $viewFile . ' not found');
        endif;

        exit;

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
    public function redirect($r = array(), $show = '', $time = '0')
    {
        $route = empty($r[0]) ? '' : $r[0];

        $param = empty($r[1]) ? array() : $r[1];

        $url = Ar::createUrl($route, $param);

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

    /**
     * redirect function.
     *
     * @param mixed  $r    route.
     * @param string $show show string.
     * @param string $time time display.
     *
     * @return mixed
     */
    public function redirectSuccess($r = array(), $show = '', $time = '1')
    {
        $this->redirect($r, '成功:' . $show, $time);

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
    public function redirectError($r = array(), $show = '' , $time = '4')
    {
        $this->redirect($r, '失败:' . $show, $time);

    }

    /**
     * redirect function.
     *
     * @param string $msg message.
     *
     * @return void
     */
    public function showJsonSuccess($msg = ' ')
    {
        $this->showJson(array('ret_msg' => $msg, 'success' => "1"));

    }

    /**
     * redirect function.
     *
     * @param string $msg message.
     *
     * @return void
     */
    public function showJsonError($msg = ' ')
    {
        $this->showJson(array('ret_msg' => 'faild', 'error_msg' => $msg, 'success' => "0"));

    }

    /**
     * json display.
     *
     * @param mixed $data    jsondata.
     * @param array $options option.
     *
     * @return mixed
     */
    public function showJson($data = array(), array $options = array())
    {
        if (empty($options['showJson'])) :
            header('charset:utf-8');
            header('Content-type:text/javascript');
            if (empty($options['data'])) :
                $retArr = array(
                        'ret_code' => '1000',
                        'ret_msg' => '',
                    );

                if (is_array($data)) :
                    if (isset($data['ret_code']) && isset($data['ret_msg'])) :
                        $retArr['data'] = $data;
                        $retArr['total_lines'] = Ar::c('validator.validator')->checkMutiArray($data) ? (string)count($data) : 1;

                        $retArr = array_merge($retArr, $options);
                    else :
                        if (!empty($data['error_msg'])) :
                            $retArr['ret_code'] = "1001";
                        endif;
                        $retArr = array_merge($retArr, $data);
                    endif;
                else :
                    $retArr['ret_msg'] = $data;
                endif;
            else :
                $retArr = $data;
            endif;

            echo json_encode($retArr);
            exit;
        else :
            return $data;
        endif;

    }

    /**
     * check login.
     *
     * @return boolean
     */
    public function ifLogin()
    {
        return !!arComp('list.session')->get('uid');

    }

    /**
     * logout.
     *
     * @return void
     */
    public function logOut()
    {
        arComp('list.session')->set('uid', null);

    }

    /**
     * auth function.
     *
     * @return void
     */
    public function auth()
    {

    }

    /**
     * start controller.
     *
     * @param string $module module.
     *
     * @return void
     */
    public function runController($module)
    {
        $route = explode('/', $module);

        $requestRoute = array(
                'm' => arCfg('requestRoute.m'),
                'c' => $route[0],
                'a' => $route[1],
            );

        Ar::a('ArWebApplication')->runController($requestRoute);

    }

}
