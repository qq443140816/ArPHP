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
        $viewPath = APP_VIEW_PATH;
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
            Ar::import($viewFile);
        else
            throw new ArException('view : ' . $viewFile . ' not found');
    }

   

    public function showJson($data, $showJson = true, $options = array())
    {
        if ($showJson) :
            header('charset:utf-8');
            header('Content-type:text/plain');
            $retArr = array(
                    'ret_code' => '1000',
                    'ret_msg' => '',
                );

            if (empty($options))
                $options = array();
            
            if (is_array($data)) :
                if (empty($data['retcode']) || empty($data['errormsg'])) :
                    $retArr['data'] = $data;
                    $retArr['total_lines'] = Ar::c('validator.validator')->checkMutiArray($data) ? (string)count($data) : 1;
                    $retArr = array_merge($retArr, $options);
                else :
                    $retArr = array_merge($retArr, $data);
                endif;
            endif;
            echo json_encode($retArr);
        else :
            return $data;
        endif;

    }

}
