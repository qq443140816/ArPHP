<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

use \Core\Ar;

/**
 * Default Controller of webapp.
 */
class MyModel extends \Core\ArModel {

    public function index() {
        echo 'model';
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

    /**
     * user result.
     *
     * @return array
     */
    public function doUserResultForPhone($result)
    {
        foreach ($result as &$val) :
            $val['title'] = $val['title'] . '-' . $val['content'];
        endforeach;
        return $result;

    }

}
