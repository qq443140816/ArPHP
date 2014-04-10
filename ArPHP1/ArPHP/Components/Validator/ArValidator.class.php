<?php
class ArValidator extends ArComponent {

   /**
     * check if number.
     *
     * @return boolean
     */
    public function checkNumber($obj)
    {
        return is_numeric($obj);
        
    }

    /**
     * check if muti array.
     *
     * @return boolean
     */
    public function checkMutiArray($obj)
    {
        $rt = true;        
        if (is_array($obj)) :
            foreach ($obj as $arr) :
                if (!is_array($arr)) :
                    $rt = false;
                    break;
                endif;
            endforeach;
        else :
            $rt = false;
        endif;

        return $rt;

    }
    
}
