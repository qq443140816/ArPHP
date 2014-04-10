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
class ApiController extends ArController {

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

        $cate = Ar::c('db.dbmysql')->read()->table('config_categories')->select('types')->where(array('id' => $id))->queryRow();

        $table = $cate['types'] . '_content';

        $result = Ar::c('db.dbmysql')->read()->select('id,title,href,etime')

            ->where(array('categories' => $id, 'title !=' => ''))

            ->table($table)

            ->limit(8)
            
            ->order('dex desc, top desc, hot desc , rec desc')

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

        $this->showJson($result);

    }
}
