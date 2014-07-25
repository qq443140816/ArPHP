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
 * display std out msg
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
class ArOut extends ArComponent
{
    /**
     * json display.
     *
     * @param mixed $data    jsondata.
     * @param array $options option.
     *
     * @return mixed
     */
    public function json($data = array(), array $options = array())
    {
        if (empty($options['showJson']) || $options['showJson'] == true) :
            header('charset:utf-8');
            header('Content-type:text/html');
            if (empty($options['data'])) :
                $retArr = array(
                        'ret_code' => '1000',
                        'ret_msg' => '',
                    );

                if (is_array($data)) :
                    if (!isset($data['ret_code']) || !isset($data['ret_msg'])) :
                        $retArr['data'] = $data;
                        $retArr['total_lines'] = Ar::c('validator.validator')->checkMutiArray($data) ? (string)count($data) : 1;

                        $retArr = array_merge($retArr, $options);
                    else :
                        if (!empty($data['error_msg']) && empty($data['ret_code'])) :
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
     * show debug info.
     *
     * @param string  $msg  out msg.
     * @param string  $tag  debug stage.
     * @param boolean $show if display.
     *
     * @return void
     */
    public function deBug($msg = '', $tag = 'RUNTIME', $show = false)
    {
        static $debugMsg = '';
        if (preg_match("#\[[A-Z_]+\]$#", $msg)) :
            $msg = "<b>" . $msg . "</b>";
        else :
            $msg = "&nbsp;&nbsp;" . $msg;
        endif;
        $debugMsg .= $msg . "<br>";

        if ($show) :

            $showContentBox = array(
                    'header' => '<div style="width:98%;bottom:30px"><div style="border-top:1px #666 dashed;background:#f1f1f1;text-align:center;font-size:20px;margin:10px 0px 10px;">[DEBUG ' . $tag . ' INFO] </div>',
                    'showMsg' => '<div style="padding:5px;background:#f3f3f1;line-height:30px">' . $debugMsg . '</div>',
                    'trance' => '<div style="background:#f8f8f8">RUN TIME : ' . (microtime(1) - AR_START_TIME) . 's</div>',
                    'footer' => '</div>',
                );

            $deBugMsg = '';

            echo join($showContentBox, '');

        endif;


    }

}
