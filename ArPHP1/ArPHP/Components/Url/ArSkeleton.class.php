<?php
/**
 * class Db default classPDO
 *
 * @author assnr <ycassnr@gmail.com>
 */

/**
 * abstract Db class.
 */
class ArSkeleton extends ArComponent {

    public $appName = '';
    protected $basePath = '';

    /**
     * url skeleton .
     *
     * import muti url format.
     */
    public function generateFolders()
    {
        $folderLists = array(
                $this->basePath,
                ROOT_PATH . 'Conf',
                $this->basePath . 'Controller',
                $this->basePath . 'View',
                $this->basePath . 'View' . DS . 'Index',
                $this->basePath . 'Ext',
                $this->basePath . 'Model',
                $this->basePath . 'Conf',
                $this->basePath . 'Public',
            );

        foreach($folderLists as $folder) :
            if (!$this->check($folder))
                if (!mkdir($folder)) :
                    throw new ArException("folder $folder create failed !");
                endif;
        endforeach;

    }

    public function generateFiles()
    {
        $fileLists = array(
            $this->basePath . 'Controller' . DS . 'IndexController.class.php' =>
'<?php
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
class IndexController extends ArController {

    /**
     * just the example of get contents.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->display();

    }

}',
        $this->basePath . 'Model' . DS . 'MyModel.class.php' =>
'<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Model of webapp.
 */
class MyModel extends ArModel {

    static public function model($class = __CLASS__) {
        return parent::model($class);

    }

    public function yourFunction()
    {
        echo "this is your funciton";
    }

}',
        $this->basePath . 'View' . DS . 'Index' . DS . 'index.php' =>
'<html>
    this is your view file !
    <h1>Hello, ArPHP ! </h1>
</html>
',
        $this->basePath . 'Conf' . DS . 'app.config.php' =>
'<?php
/**
 * Ar default app config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
    );',

        ROOT_PATH . 'Conf' . DS . 'public.config.php' =>
'<?php
/**
 * Ar default public config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
    \'moduleLists\' => array(
                    \'' . $this->appName . '\'
                ),
    );',

            );

        foreach($fileLists as $file => $content) :
            if (!$this->check($file))
                file_put_contents($file, $content);
        endforeach;


    }

    public function check($file)
    {
        return is_file($file) || is_dir($file);

    }

    public function generate($appName)
    {
        $this->appName = $appName;
        $this->basePath = ROOT_PATH . $this->appName . DS;

        if (!$this->check($this->basePath)) :

            $this->generateFolders();
            $this->generateFiles();

        endif;

    }

}
