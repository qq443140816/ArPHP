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
    public function categoriesAction($type = 'index')
    {
        $showJson = true;
        if (func_num_args() > 0) :
            $showJson = false;
        else :
            if (empty($_GET['type'])) :
                $type = 'index';
            else :
                $type = $_GET['type'];
            endif;
        endif;

        $categories = array(
                'index' => array(5000, 5001, 5002, 5003, 5004, 5484, 5482, 5483, 6156),
                'banshi' => array(5006, 5005, 5007, 5011, 5998, 6000),
                'open' => array(5713, 5891, 5892, 5893, 5482, 5895, 5896, 5897, 5898),
            );

        
        $result = Ar::c('db.dbmysql')->select('id,displayname,href')
            ->where(array('id' => $categories[$type]))
            ->table('config_categories')
            ->queryAll();

        if ($showJson)
            $this->showJson($result);
        else
            return $result;

    }

    /**
     * get articles by categoried id
     *
     * @return void
     */
    public function articlesByCategoryIdAction($id = '0')
    {
        $showJson = true;
        if (func_num_args() > 0) :
            $showJson = false;
        else :
            if (empty($_GET['id'])) :
                $id = 0;
            else :
                $id = (int)$_GET['id'];
            endif;
        endif;

        $limit = 8;
        $condition = array('title !=' => '');
        $cate = Ar::c('db.dbmysql')->read()->table('config_categories')->select('not,types,children')->where(array('id' => $id))->queryRow();
        $table = $cate['types'] . '_content as A';
        $order = 'dex desc, top desc, hot desc , rec desc';

        $columns = 'id,title,href,etime, ctime, href as contentKey';
        if ($id === 6000)
            $columns = 'id,title,content,href, ctime, etime, href as contentKey';

        $condition['categories'] = $id;
        $childStr = (string)trim($cate['children'], ',');

        if ((string)$id === $childStr)
            $isCateGory = '0';
        else
            $isCateGory = '1';

        if (!empty($_GET['type']) && $_GET['type'] == 'content') :
            $isCateGory = '0';
            $condition['categories'] = explode(',', trim($cate['children'], ','));
        endif;

        if ($isCateGory == 1) :
            switch ($id) {
                // 办事服务
                case '5005' :
                // 信息公开
                case '5713' :
                case '5893' :
                case '5482' :
                case '5895' :
                case '5896' :
                case '5897' :
                case '5898' :
                    $table = 'config_categories';
                    $condition = array();
                    $condition['not'] = $id;
                    $limit = 16;
                    $order = 'od desc,id asc';
                    $columns = 'id,displayname,name,href';
                    break;
                // 教育厅文件
                case '5482' :
                case '5484' :
                    $condition = array();
                    $condition['categories'] = explode(',', trim($cate['children'], ','));
                    $order = 'A.dex DESC,A.top DESC,A.hot DESC,A.rec DESC,A.etime desc';
                    break;
                default:
                    $table = 'config_categories';
                    $condition = array();
                    $condition['not'] = $id;
                    $limit = 16;
                    $order = 'od desc,id asc';
                    $columns = 'id,displayname,name,href';
                    break;
                }
        endif;

        $result = Ar::c('db.dbmysql')->read()->select($columns)

            ->where($condition)

            ->table($table)

            ->limit($limit)
            
            ->order($order)

            ->queryAll();

        $result = Ar::c('format.format')->timeToDate($result, 'etime');
        $result = Ar::c('format.format')->timeToDate($result, 'ctime');
        $result = MyModel::model()->addClumns($result, 'isCateGory', $isCateGory);
        

        if ($isCateGory == 0)
            $result = Ar::c('format.format')->encrypt($result, 'contentKey');
        if ($id == 6000)
            $result = MyModel::model()->doUserResultForPhone($result);

        return $this->showJson($result, $showJson);

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
        $result = Ar::c('format.format')->timeToDate($result, 'ctime');
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
        if (!empty($_GET['contentKey'])) :
            $result = $this->secondContentByKeyAction($_GET['contentKey']);
        else :

            $id = !empty($_GET['id']) ? (int)$_GET['id'] : '0';

            $type = empty($_GET['type']) ? 'article' : $_GET['type'];

            $tableMap = array(
                    'article' => 'article',
                    'banshi' => 'second',
                    'open' => 'open'
                );

            $table = $tableMap[$type] . '_content';
            $result = Ar::c('db.dbmysql')->read()->select('')

                ->where(array('id' => $id))

                ->table($table)

                ->queryRow();

            $result = Ar::c('format.format')->timeToDate($result, 'ctime');
            $result = Ar::c('format.format')->stripslashes($result);
            $result = Ar::c('format.format')->replace(array('{:$system.img:}', '{:$system.upload:}'), array('http://www.scedu.net/front/', 'upfileload'), $result);
        endif;

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
        // var_dump($result);
        // exit;

        $this->showJson($result);

    }

    /**
     * get content.
     *
     * @return string.
     */
    public function secondContentByKeyAction($str = '')
    {
        $showJson = true;
        if (func_num_args() > 0) :
            $showJson = false;
        else :
            if (empty($_GET['key'])) :
                $str = '';
            else :
                $id = (int)$_GET['key'];
                $str = $_GET['key'];
            endif;
        endif;

        $str = Ar::c('hash.mcrypt')->decrypt($str);

        $url = trim(str_replace('http://www.scedu.net/', '', $str), '/');

        $pathArr = explode('/', $url);

        $firstChild = array_shift($pathArr);

        if (Ar::c('validator.validator')->checkUrl($url)) :
            $result['link'] = '1';
            $result['url'] = $url;
        else :
            switch ($firstChild) {
                case 'banshi':
                    $tablePrefix = 'second';
                    break;
                case 'open':
                    $tablePrefix = 'open';
                    break;
                default:
                    $tablePrefix = 'article';
                    break;
            }

            $table = $tablePrefix . '_content';

            $result = Ar::c('db.dbmysql')->read()->select('')

                ->where(array('url' => $url))

                ->table($table)

                ->queryRow();

            $result = Ar::c('format.format')->timeToDate($result, 'ctime');
            $result = Ar::c('format.format')->replace(array('{:$system.img:}', '{:$system.upload:}'), array('http://www.scedu.net/front/', 'upfileload'), $result);
            $result['link'] = '0';
        endif;
        return $this->showJson($result, $showJson);

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
        error_log(var_export($_GET, 1));
        $fid = (int)$_GET['fid'];
        $table = 'config_categories';
        $condition = array();
        $condition['not'] = $fid;
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
        $result = Ar::c('format.format')->timeToDate($result, 'ctime');

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
        $columns = 'A.`href`,A.`title`,A.`etime`,A.`id`';
        $result = Ar::c('db.dbmysql')->read()->select($columns)

            ->where($condition)

            ->table($table)

            ->limit($limit)
            
            ->order($order)

            ->queryAll();

        $result = Ar::c('format.format')->timeToDate($result, 'etime');

        $this->showJson($result);

    }

    // 信息公开
    public function getAllOpenInfoAction()
    {
        $cates = $this->categoriesAction('open');
        foreach ($cates as &$cate) :
            $cate['children'] = $this->articlesByCategoryIdAction($cate['id']);
        endforeach;
        $result = $cates;
        $this->showJson($result);

    }

    // leader info
    public function leaderWorkInfoAction()
    {
        $id = (int)$_GET['id'];
        $table = 'open_content As A';
        $condition = array();
        $cate = Ar::c('db.dbmysql')
            ->read()
            ->table('config_categories')
            ->select('types,children')
            ->where(array('id' => $id))
            ->queryRow();

        $categories  = explode(',', trim($cate['children'], ','));
        $limit = 1;

        $order = 'A.tag asc,A.`dex` desc,A.`top` desc,A.`hot` desc, A.`rec` desc,A.`etime` desc';
        $columns = 'A.thumbnails2 ,A.id,A.`href`,A.`title`,A.`ctime`,A.content,A.photo';
        $result = Ar::c('db.dbmysql')->read()->select($columns)

            ->where("categories in (" . implode($categories, ',') . ") AND (A.tag LIKE '%loader%' OR A.tag LIKE '%work%')")

            ->table($table)

            ->limit($limit)
            
            ->order($order)

            ->queryRow();

        $result = Ar::c('format.format')->timeToDate($result, 'ctime');
        $result = Ar::c('format.format')->replace(array('{:$system.img:}', '{:$system.upload:}'), array('http://www.scedu.net/front/', 'upfileload'), $result);

        $this->showJson($result);

    }

    // leader info
    public function leaderSpeekInfoAction()
    {
        $id = (int)$_GET['id'];
        $table = 'open_content As A';
        $condition = array();
        $cate = Ar::c('db.dbmysql')
            ->read()
            ->table('config_categories')
            ->select('types,children')
            ->where(array('id' => $id))
            ->queryRow();

        $categories  = explode(',', trim($cate['children'], ','));

        $condition['categories'] = $categories;

        $condition['tag like'] = '%speek%';

        $limit = 7;

        $order = 'LENGTH(thumbnails2) DESC,A.`dex` desc,A.`top` desc,A.`hot` desc, A.`rec` desc,A.`etime` desc';
        $columns = 'A.thumbnails2 ,A.`href`,A.`title`,A.`ctime` ';

        $result = Ar::c('db.dbmysql')->read()->select($columns)

            ->where($condition)

            ->table($table)

            ->limit($limit)
            
            ->order($order)

            ->queryAll();
        $result = Ar::c('format.format')->timeToDate($result, 'ctime');
        $result = Ar::c('format.format')->replace(array('{:$system.img:}', '{:$system.upload:}'), array('http://www.scedu.net/front/', 'upfileload'), $result);

        $this->showJson($result);

    }

    public function testAction()
    {
        $m = Model::model();
        $m->index();

    }

}
