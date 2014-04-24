<?php
class ArFormat extends ArComponent {

    public function timeToDate($obj, $key = '')
    {
        if (Ar::c('validator.validator')->checkMutiArray($obj)) :
            foreach ($obj as &$time) :
                $time = $this->timeToDate($time, $key);
            endforeach;
        elseif (is_array($obj)) :
            if (isset($obj[$key])) :
                $obj[$key] = $this->timeToDate($obj[$key]);
            endif;
        else :
            $obj = date('Y-m-d', Ar::c('validator.validator')->checkNumber($obj) ? $obj : strtotime($obj));
        endif;

        return $obj;

    }

    public function replace($key, $value, $obj)
    {
        if (is_array($obj)) :
            foreach($obj as &$o) :
                $o = $this->replace($key, $value, $o);
            endforeach;
        else :
            $obj = str_replace($key, $value, $obj);
        endif;
        return $obj;

    }

    public function stripslashes($obj)
    {
        if (is_array($obj)) :
            foreach($obj as &$o) :
                $o = $this->stripslashes($o);
            endforeach;
        else :
            $obj = stripslashes($obj); 
        endif;
        return $obj;

    }

    public function encrypt($obj, $key = '')
    {
        if (is_array($obj)) :
            if (empty($obj[$key])) :
                foreach ($obj as &$eObj) :
                    $eObj = $this->encrypt($eObj, $key);
                endforeach;
            else :
                $obj[$key] = $this->encrypt($obj[$key]);
            endif;
        else :
            $obj = Ar::c('hash.mcrypt')->encrypt($obj);
        endif;

        return $obj;

    }

    public function urldecode($obj, $key = '')
    {
        if (is_array($obj)) :
            if (empty($obj[$key])) :
                foreach ($obj as &$eObj) :
                    $eObj = $this->urldecode($eObj, $key);
                endforeach;
            else :
                $obj[$key] = $this->urldecode($obj[$key]);
            endif;
        else :
            $obj = urldecode($obj);
        endif;
        
        return $obj;

    }

    public function urlencode($obj, $key = '')
    {
        if (is_array($obj)) :
            if (empty($obj[$key])) :
                foreach ($obj as &$eObj) :
                    $eObj = $this->urlencode($eObj, $key);
                endforeach;
            else :
                $obj[$key] = $this->urlencode($obj[$key]);
            endif;
        else :
            $obj = urlencode($obj);
        endif;

        return $obj;

    }
    
}
