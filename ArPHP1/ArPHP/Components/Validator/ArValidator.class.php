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

    /**
     * check if url.
     *
     * @return boolean
     */
    public function checkUrl($url)
    {
        return preg_match("#^(http)#", $url);

    }

    /**
     * check key equal.
     *
     * @return boolean
     */
    public function checkArrayKeyEqual(array $arri, array $arro)
    {
        $lengthi = count($arri);
        $lengtho = count($arro);

        $rt = true;

        if ($lengthi !== $lengtho) :

            $rt = false;

        else :
            foreach ($arri as $ikey => $ivalue) :

                if (!array_key_exists($ikey, $arro)) :
                    $rt = false;
                    break;
                endif;

            endforeach;
        endif;

        return $rt;

    }

    public function checkAjax()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            return true;
        else
            return false;

    }

    public function checkEmpty($obj)
    {
        return empty($obj);

    }

    public function checkDataByRules(array $data, array $rules)
    {
        foreach ($rules as $k => $rule) :

            if (array_key_exists($k, $data)) :
                switch ($rule[0]) :
                    case 'number' :
                        if ($this->checkNumber($data[$k]))
                            unset($rules[$k]);
                        break;
                    case 'required' :
                        if (!$this->checkEmpty($data[$k]))
                            unset($rules[$k]);
                        break;
                    default :
                        if (!$this->checkEmpty($data[$k]))
                            unset($rules[$k]);
                        break;
                endswitch;
            endif;

        endforeach;

        return $rules;

    }

}
