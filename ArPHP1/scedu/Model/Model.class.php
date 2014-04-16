<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Controller of webapp.
 */
class MyModel extends ArModel {

    static public function model($class = __CLASS__) {
        return parent::model($class);

    }

    /**
     * add columns.
     *
     * @return array
     */
    public function addClumns($data, $key, $value = '')
    {
        if (Ar::c('validator.validator')->checkMutiArray($data)) :
            foreach ($data as &$d) :
                $d[$key] = $value;
            endforeach;
        elseif (is_array($data)) :
            $data[$key] = $value;
        endif;

        return $data;

    }

}