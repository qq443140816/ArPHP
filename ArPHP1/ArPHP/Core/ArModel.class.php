<?php
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

/**
 * class Ar.
 */
class ArModel {

    public $nowModel = '';

    public $tableName = '';

    private static $_models = array(

        );


    static public function model($class = __CLASS__)
    {
        $key = strtolower($class);

        if (!isset(self::$_models[$key])) :
            $obj = new $class;
            $obj->nowModel = $class;
            self::$_models[$key] = $obj;
        endif;

        return self::$_models[$key];

    }

    public function upload($filed, $type = 'img')
    {
        $upFile = arComp('ext.upload')->upload($filed, '', $type);

        if (!$upFile) :
            arComp('list.log')->set($this->nowModel, arComp('ext.upload')->errorMsg());
            return false;
        else :
            return $upFile;
        endif;

    }

    public function getDb()
    {
        return ArComp('db.mysql')->table($this->tableName)->setSource($this->nowModel);

    }

    public function rules()
    {
        return array();

    }

    public function updateCheck($data)
    {
        $rules = $this->rules();

        foreach ($rules as $key => $rule) :
            if (empty($rules[2]) || $rules[2] != 'update' ) :
                unset($rules[$key]);
            endif;
        endforeach;

        return $this->insertCheck($data, $rules);

    }

    public function insertCheck($data, $rules = array())
    {
        $rules = empty($rules) ? $this->rules() : $rules;

        $r = arComp('validator.validator')->checkDataByRules($data, $rules);

        if (empty($r))
            return true;

        $errorMsg = '';

        foreach ($r as $errorR) :
            $errorMsg .= $errorR[1] . "\n";
        endforeach;

        arComp('list.log')->set($this->nowModel, $errorMsg);

        return false;

    }

    public function formatData($data)
    {
        return $data;

    }



}
