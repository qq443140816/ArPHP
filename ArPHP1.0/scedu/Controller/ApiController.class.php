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
        $result = Ar::c('db.dbmysql')->select('id,displayname,href')
            ->where(array('id' => array(5000, 5001, 5002, 5003, 5004, 5484)))
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
        $result = Ar::c('db.dbmysql')->read()->select('id,title,href,etime')

            ->where(array('categories' => $id))

            ->table('article_content')

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
        $result = Ar::c('db.dbmysql')->read()->select('')

            ->where(array('id' => $id))

            ->table('article_content')

            ->queryRow();

        $this->showJson($result);

    }

}
