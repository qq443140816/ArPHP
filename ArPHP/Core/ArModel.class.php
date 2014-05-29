<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.net
 */

/**
 * model
 *
 * default hash comment :
 *
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.net/licence BSD Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.net
 */
class ArModel
{
    // current Model handle
    public $nowModel = '';

    // table of database
    public $tableName = '';

    // container of model
    private static $_models = array(

        );


    /**
     * model prototype.
     *
     * @param string $class which model handle.
     *
     * @return Object
     */
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

    /**
     * upload for detail model.
     *
     * @param string $field description.
     * @param string $type  upload type.
     *
     * @return mixed
     */
    public function upload($field, $type = 'img')
    {
        $upFile = arComp('ext.upload')->upload($field, '', $type);

        if (!$upFile) :
            arComp('list.log')->set($this->nowModel, arComp('ext.upload')->errorMsg());
            return false;
        else :
            return $upFile;
        endif;

    }

    /**
     * db connection.
     *
     * @return Object
     */
    public function getDb()
    {
        return ArComp('db.mysql')->table($this->tableName)->setSource($this->nowModel);

    }

    /**
     * filter rules.
     *
     * @return array
     */
    public function rules()
    {
        return array();

    }

    /**
     * check value when update.
     *
     * @param array $data data.
     *
     * @return boolean
     */
    public function updateCheck(array $data = array())
    {
        $rules = $this->rules();

        foreach ($rules as $key => $rule) :
            if (empty($rules[2]) || $rules[2] != 'update' ) :
                unset($rules[$key]);
            endif;
        endforeach;

        return $this->insertCheck($data, $rules);

    }

    /**
     * insert check.
     *
     * @param array $data  insert data.
     * @param array $rules check rules.
     *
     * @return boolean
     */
    public function insertCheck(array $data = array(), array $rules = array())
    {
        $rules = empty($rules) ? $this->rules() : $rules;

        $r = arComp('validator.validator')->checkDataByRules($data, $rules);

        if (empty($r)) :
            return true;
        endif;

        $errorMsg = '';

        foreach ($r as $errorR) :
            $errorMsg .= $errorR[1] . "\n";
        endforeach;

        arComp('list.log')->set($this->nowModel, $errorMsg);

        return false;

    }

    /**
     * generate insert data.
     *
     * @param array $data data after format.
     *
     * @return array
     */
    public function formatData(array $data = array())
    {
        return $data;

    }

}

