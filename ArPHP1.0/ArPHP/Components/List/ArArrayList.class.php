<?php
namespace Components\Format;

class ArArrayList extends \Components\Component {
    static public $config = array();

    public function timeToDate($obj, $key = '')
    {
        if (is_array($obj)) :
            if (empty($obj[$key])) :
                foreach ($obj as &$time) :
                    $time = $this->timeToDate($time, $key);
                endforeach;
            else :
                $obj[$key] = $this->timeToDate($obj[$key]);
            endif;
        else :
            $obj = date('Y-m-d', $obj);
        endif;
        return $obj;

    }
    
}