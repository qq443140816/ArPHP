<?php
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

/**
 * class ArController.
 */
class ArController {

    public function init()
    {

    }

    public function display($view = '')
    {
        $viewPath = arCfg('PATH.VIEW');

        $r = Ar::a('ArWebApplication')->route;

        if (empty($view)) :
            $viewPath .= $r['c'] . DS . $r['a'];
        elseif(strpos($view, '/') !== false) :
            $viewPath .= str_replace('/', DS, $view);
        else :
            $viewPath .= $r['c'] . DS . $view;
        endif;

        $viewFile = $viewPath . '.php';
        if (is_file($viewFile))
            include $viewFile;
        else
            throw new ArException('view : ' . $viewFile . ' not found');
    }

    public function redirect($route = '', $param = array(), $show = '')
    {
        $url = Ar::createUrl($route, $param);
        header('Location: ' . $url);

    }

    public function showJson($data, array $options = array())
    {
        if (empty($options['showJson']) && arComp('validator.validator')->checkAjax()) :
            header('charset:utf-8');
            header('Content-type:text/plain');
            if (empty($options['data'])) :
                $retArr = array(
                        'ret_code' => '1000',
                        'ret_msg' => '',
                    );

                if (is_array($data)) :
                    if (empty($data['ret_code']) && empty($data['ret_msg'])) :

                        $retArr['data'] = $data;
                        $retArr['total_lines'] = Ar::c('validator.validator')->checkMutiArray($data) ? (string)count($data) : 1;

                        $retArr = array_merge($retArr, $options);
                    else :
                        if (!empty($data['error_msg']))
                            $retArr['ret_code'] = "1001";
                        $retArr = array_merge($retArr, $data);
                    endif;
                else :
                    $retArr['ret_msg'] = $data;
                endif;
            else :
                $retArr = $data;
            endif;

            echo json_encode($retArr);
        else :
            return $data;
        endif;

    }

    public function ifLogin()
    {
        return !!arComp('list.session')->get('uid');

    }

    public function logOut()
    {
        arComp('list.session')->set('uid', null);

    }

    public function auth()
    {

    }

}
