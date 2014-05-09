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


    public function getDb()
    {
        return ArComp('db.dbmysql')->table($this->tableName)->setSource($this->nowModel);

    }

    public function rules()
    {
        return array();

    }

    public function insertCheck($data)
    {
        $rules = $this->rules();

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
