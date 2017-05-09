<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.org
 */

/**
 * class ArView
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
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.org
 */
class ArView
{
    // 开启seg
    public static function start()
    {
        // arSeg(array('segKey' => 'Sys/css', 'include_once' => 1));
        // 批量 css 插入
        $cssInsertBundles = arCfg('BUNDLE_VIEW_ASSIGN.cssInsertBundles', array());
        $cssInsertBundlesStart = arCfg('BUNDLE_VIEW_ASSIGN.cssInsertBundles_start', array());
        $cssInsertBundles = array_merge($cssInsertBundlesStart, $cssInsertBundles);

        if ($cssInsertBundles) :
            foreach ($cssInsertBundles as $bundle) :
                $cssDir = 'css/';
                if (strpos($bundle, '/') === 0) :
                    $cssDir = '';
                endif;
                // APP 项目目录
                $cssFileApp = AR_APP_PATH . 'Public' . DS . $cssDir . $bundle . '.css';
                $cssFileApp = realpath($cssFileApp);
                $cssServerFile = $cssServerFileApp = arCfg('PATH.PUBLIC') . $cssDir . $bundle . '.css';
                if (!is_file($cssFileApp)) :
                    // 公共目录
                    $cssFilePublic = AR_ROOT_PATH . 'Public' . DS . $cssDir . $bundle . '.css';
                    $cssFilePublic = realpath($cssFilePublic);
                    $cssServerFile = $cssServerFilePublic = arCfg('PATH.GPUBLIC') . $cssDir . $bundle . '.css';
                    if (!is_file($cssFilePublic)) :
                        $cssServerFile = $cssServerFileApp;
                    endif;
                endif;
                $cssServerFile = str_replace('//', '/', $cssServerFile);
                echo '<link href="' . $cssServerFile . '" rel="stylesheet" type="text/css"/>';
            endforeach;
        endif;

 echo '<script type="text/javascript">
// 全局JS变量集合
var JSV = {
    // 公用路径
    PATH_PUBLIC: "' . arCfg('PATH.PUBLIC') . '",
    // 服务地址
    PATH_SERVER: "' . AR_SERVER_PATH . '",
    // 应用服务地址
    PATH_APP_SERVER: "' . arCfg('PATH.APP_SERVER_PATH') . '",
    // 当前请求path
    PATH_CURRENT: "' . arU('') . '",
};
</script>';

        $jsInsertBundles = arCfg('BUNDLE_VIEW_ASSIGN.jsInsertBundles_start', array());
        // 批量 js 插入
        if ($jsInsertBundles) :
            foreach ($jsInsertBundles as $bundle) :
                $jsDir = 'js/';
                if (strpos($bundle, '/') === 0) :
                    $jsDir = '';
                endif;
                // APP 项目目录
                $jsServerFile = $jsServerFileApp = arCfg('PATH.PUBLIC') . $jsDir . $bundle . '.js';
                $jsFileApp = AR_APP_PATH . 'Public' . DS . $jsDir . $bundle . '.js';
                if (!is_file($jsFileApp)) :
                    // 公共目录
                    $jsServerFile = $jsServerFilePublic = arCfg('PATH.GPUBLIC') . $jsDir . $bundle . '.js';
                    $jsFilePublic = AR_ROOT_PATH . 'Public' . DS . $jsDir . $bundle . '.js';
                    if (!is_file($jsFilePublic)) :
                        $jsServerFile = $jsServerFileApp;
                    endif;
                endif;
                $jsServerFile = str_replace('//', '/', $jsServerFile);
                echo '<script src="' . $jsServerFile . '" type="text/javascript"></script>';
            endforeach;
        endif;

    }

    // 结束seg
    public static function end()
    {
        // arSeg(array('segKey' => 'Sys/js', 'include_once' => 1));
        $jsInsertBundles = arCfg('BUNDLE_VIEW_ASSIGN.jsInsertBundles', array());
        // 批量 js 插入
        if ($jsInsertBundles) :
            foreach ($jsInsertBundles as $bundle) :
                $jsDir = 'js/';
                if (strpos($bundle, '/') === 0) :
                    $jsDir = '';
                endif;
                // APP 项目目录
                $jsServerFile = $jsServerFileApp = arCfg('PATH.PUBLIC') . $jsDir . $bundle . '.js';
                $jsFileApp = AR_APP_PATH . 'Public' . DS . $jsDir . $bundle . '.js';
                if (!is_file($jsFileApp)) :
                    // 公共目录
                    $jsServerFile = $jsServerFilePublic = arCfg('PATH.GPUBLIC') . $jsDir . $bundle . '.js';
                    $jsFilePublic = AR_ROOT_PATH . 'Public' . DS . $jsDir . $bundle . '.js';
                    if (!is_file($jsFilePublic)) :
                        $jsServerFile = $jsServerFileApp;
                    endif;
                endif;
                $jsServerFile = str_replace('//', '/', $jsServerFile);
                echo '<script src="' . $jsServerFile . '" type="text/javascript"></script>';
            endforeach;
        endif;

    }

    // 布局
    public static function layout($layout)
    {
        Ar::setConfig('LAYOUT_NAME', $layout);

    }

    // 加载文件
    public static function load($file, $tostart = false)
    {
        if (strpos($file, ',') !== false) :
            $fileList = explode(',', $file);
            foreach ($fileList as $file) :
                self::load($file, $tostart);
            endforeach;
            return;
        endif;

        $posDot = strrpos($file, '.');

        if ($posDot !== false) :
            $extension = substr($file, $posDot + 1);
            $fileMainName = substr($file, 0, $posDot);
            switch ($extension) {
                case 'css':
                        if ($tostart) :
                            $css = arCfg('BUNDLE_VIEW_ASSIGN.cssInsertBundles_start', array());
                            $css[] = $fileMainName;
                            Ar::setConfig('BUNDLE_VIEW_ASSIGN.cssInsertBundles_start', $css);
                        else :
                            $css = arCfg('BUNDLE_VIEW_ASSIGN.cssInsertBundles', array());
                            $css[] = $fileMainName;
                            Ar::setConfig('BUNDLE_VIEW_ASSIGN.cssInsertBundles', $css);
                        endif;
                    break;
                case 'js':
                        if ($tostart) :
                            $js = arCfg('BUNDLE_VIEW_ASSIGN.jsInsertBundles_start', array());
                            $js[] = $fileMainName;
                            Ar::setConfig('BUNDLE_VIEW_ASSIGN.jsInsertBundles_start', $js);
                        else :
                            $js = arCfg('BUNDLE_VIEW_ASSIGN.jsInsertBundles', array());
                            $js[] = $fileMainName;
                            Ar::setConfig('BUNDLE_VIEW_ASSIGN.jsInsertBundles', $js);
                        endif;
                    break;
                default:
                        extract(arCfg('BUNDLE_VIEW_ASSIGN', array()));
                        include AR_APP_VIEW_PATH . $file;
                    break;
            }
        else :
            extract(arCfg('BUNDLE_VIEW_ASSIGN', array()));
            include AR_APP_VIEW_PATH . $file;
        endif;

    }

    public static function __callStatic($name, $arguments)
    {
        // 注意: $name 的值区分大小写
        echo "static method '$name' not found "
             . implode(', ', $arguments). "\n";
    }

}
