<?php 
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

namespace Core;
use \Core\Ar as Ar;

/**
 * class ArController.
 */
class ArController {

    public function init()
    {

    }

    public function display($view = '')
    {
        $viewPath = APP_VIEW_PATH;
        if (empty($view)) :
            $r = Ar::a('\Core\ArWebApplication')->route;
            $viewPath .= $r['c'] . DS . $r['a'];
        elseif(strpos($view, '/') !== false) :
            $viewPath .= str_replace('/', DS, $view);
        else :
            $viewPath .= $r['c'] . DS . $view;
        endif;

        $viewFile = $viewPath . '.php';

        if (is_file($viewFile))
            Ar::import($viewFile);
        else
            throw new ArException('view : ' . $viewFile . ' not found');
    }

    public function showJson($data)
    {
        header('charset:utf-8');
        $retArr = array(
                'ret_code' => '1000',
                'ret_msg' => '',
            );
        if (is_array($data)) :
            if (empty($data['retcode']) || empty($data['errormsg'])) :
                $retArr['data'] = $data;
                $retArr['total_lines'] = (string)count($data);
            else :
                $retArr = array_merge($retArr, $data);
            endif;
        endif;

        echo urldecode(json_encode($retArr));

    }

}
