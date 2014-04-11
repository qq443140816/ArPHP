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
class ApiController extends \Core\ArController {

    /**
     * just the example of get contents.
     *
     * @return void
     */
    public function categoriesAction()
    {
        $categories = array(
                'index' => array(5000, 5001, 5002, 5003, 5004, 5484, 5482, 5483, 6156),
                'banshi' => array(5006, 5005, 5007, 5011, 5998, 6000),
            );

        if (empty($_GET['type'])) :
            $type = 'index';
        else :
            $type = $_GET['type'];
        endif;

        $result = Ar::c('db.dbmysql')->select('id,displayname,href')
            ->where(array('id' => $categories[$type]))
            ->table('config_categories')
            ->queryAll();
        $this->showJson($result);

    }

    /**
     * get articles by categoried id
     *
     * @return void
     */
    public function articlesByCategoryIdAction()
    {
        $id = (int)$_GET['id'];

        $limit = 8;
        $condition = array('title !=' => '');
        $cate = Ar::c('db.dbmysql')->read()->table('config_categories')->select('types,children')->where(array('id' => $id))->queryRow();
        $table = $cate['types'] . '_content as A';
        $order = 'dex desc, top desc, hot desc , rec desc';
        $columns = 'id,title,href,etime';

        switch ($id) {
            // 办事服务
            case '5005' :
                $table = 'config_categories';
                $condition = array();
                $condition['not'] = 5005;
                $limit = 16;
                $order = 'od desc,id asc';
                $columns = 'id,displayname,name,href';
                break;
            case '5482' :
            case '5484' :
                $condition = array();
                $condition['categories'] = explode(',', trim($cate['children'], ','));
                $order = 'A.dex DESC,A.top DESC,A.hot DESC,A.rec DESC,A.etime desc';
                break;
            default:
                $condition['categories'] = $id;
                break;
        }


        $result = Ar::c('db.dbmysql')->read()->select($columns)

            ->where($condition)

            ->table($table)

            ->limit($limit)
            
            ->order($order)

            ->queryAll();

        $result = Ar::c('format.format')->timeToDate($result, 'etime');

        $this->showJson($result);

    }

    /**
     * get articles by categoried id
     *
     * @return void
     */
    public function articlesIndexSlideAction()
    {
        $result = Ar::c('db.dbmysql')->read()->select('id,title,href,etime,thumbnails2')

            ->where(array('categories' => 5000, 'crontabitem' => 0, 'thumbnails2 != ' => ''))

            ->table('article_content')

            ->limit(5)
            
            ->order('dex desc, top desc, hot desc , rec desc')

            ->queryAll();

        $result = Ar::c('format.format')->timeToDate($result, 'etime');
        $result = Ar::c('format.format')->replace(array('{:$system.img:}', '{:$system.upload:}'), array('front/', 'upfileload'), $result);
        $result = Ar::c('format.format')->stripslashes($result);

        $this->showJson($result);

    }

    /**
     * get article info.
     *
     * @return string
     */
    public function articleByIdAction()
    {
        $id = (int)$_GET['id'];

        $type = empty($_GET['type']) ? 'article' : $_GET['type'];

        $tableMap = array(
                'article' => 'article',
                'banshi' => 'second',
            );

        $table = $tableMap[$type] . '_content';
        $result = Ar::c('db.dbmysql')->read()->select('')

            ->where(array('id' => $id))

            ->table($table)

            ->queryRow();

        $result = Ar::c('format.format')->timeToDate($result, 'ctime');
        $result = Ar::c('format.format')->replace(array('{:$system.img:}', '{:$system.upload:}'), array('http://www.scedu.net/front/', 'upfileload'), $result);

        $this->showJson($result);

    }

    /**
     * 来信选登.
     *
     * @return string
     */
    public function leaderBoxAction()
    {
        $condition = array('gongai' => 0, 'recontent !=' => '');
        $columns = 'id,title,ctime';
        
        if (!empty($_GET['id'])) :
            $condition['id'] = (int)$_GET['id'];
            $columns .= ',content';
        endif;

        $result = Ar::c('db.dbmysql')->read()->table('comm_leaderbox')->select($columns)
            ->where($condition)
            ->order('ctime desc')
            ->limit(8)
            ->queryAll();
        $result = Ar::c('format.format')->timeToDate($result, 'ctime');
        
        $this->showJson($result);

    }

    /**
     * head news.
     *
     * @return string
     */
    public function headTopArticlesAction()
    {
        $result = Ar::c('db.dbmysql')->read()->select('id,name,create_times,href as key')

            ->where(array('types' => 17, 'status' => 1))

            ->table('center_content')

            ->order('ctypes desc,od desc,id asc')

            ->queryAll();
            
        $result = Ar::c('format.format')->timeToDate($result, 'create_times');

        $result = Ar::c('format.format')->encrypt($result, 'key');

        $result = Ar::c('format.format')->replace('/', '---', $result);

        $this->showJson($result);

    }

    /**
     * get content.
     *
     * @return string.
     */
    public function secondContentByKeyAction()
    {

        $str = $_GET['key'];

        $str = Ar::c('format.format')->replace('---', '/', $str);

        $str = Ar::c('hash.mcrypt')->decrypt($str);

        $url = str_replace('http://www.scedu.net/', '', $str);

        if (strpos($url, 'banshi') !== false)
            $tablePrefix = 'second';
        else
            $tablePrefix = 'article';

        $table = $tablePrefix . '_content';

        $result = Ar::c('db.dbmysql')->read()->select('')

            ->where(array('url' => $url))

            ->table($table)

            ->queryRow();

        $result = Ar::c('format.format')->timeToDate($result, 'ctime');
        $result = Ar::c('format.format')->replace(array('{:$system.img:}', '{:$system.upload:}'), array('http://www.scedu.net/front/', 'upfileload'), $result);

        $this->showJson($result);

    }

    // 热点信息分类
    public function redianCatesAction()
    {
        $table = 'config_categories';
        $condition = array();
        $condition['not'] = 5005;
        $limit = 16;
        $order = 'od desc,id asc';
        $columns = 'id,displayname,name,href,types';
        $result = Ar::c('db.dbmysql')->read()->select($columns)

            ->where($condition)

            ->table($table)

            ->limit($limit)
            
            ->order($order)

            ->queryAll();

        $result = Ar::c('format.format')->timeToDate($result, 'etime');

        $this->showJson($result);

    }

    // 热点子分类
    public function redianSubCatesByFidAction()
    {
        $fid = (int)$_GET['fid'];
        $table = 'config_categories';
        $condition = array();
        $condition['not'] = $fid;
        $limit = 16;
        $order = 'od desc,id asc';
        $columns = 'id,displayname,name,href,types,children';
        $result = Ar::c('db.dbmysql')->read()->select($columns)

            ->where($condition)

            ->table($table)

            ->limit($limit)
            
            ->order($order)

            ->queryAll();

        $result = Ar::c('format.format')->timeToDate($result, 'etime');

        $this->showJson($result);

    }

    // 热点文章
    public function redianArticlesAction()
    {
        $table = 'second_content As A';
        $condition = array();
        $condition['categories'] = $_GET['id'];
        $condition['crontabitem'] = 0;
        $limit = 16;
        $order = 'A.`dex` desc,A.`top` desc,A.`hot` desc, A.`rec` desc,A.`etime` desc';
        $columns = 'A.`href`,A.`title`,A.`etime`';
        $result = Ar::c('db.dbmysql')->read()->select($columns)

            ->where($condition)

            ->table($table)

            ->limit($limit)
            
            ->order($order)

            ->queryAll();

        $result = Ar::c('format.format')->timeToDate($result, 'etime');

        $this->showJson($result);

    }




    public function testAction()
    {
        $m = Model::model();
        $m->index();

    }

}
