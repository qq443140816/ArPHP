<?php 
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

/**
 * class Ar.
 */
class ArApp {

    static public function run()
    {
        self::initComponents(Ar::getConfig('components'));

        $app = self::createWebApplication('ArWebApplication');

        $app->start();

    }

    static private function initComponents(array $config)
    {
        foreach ($config as $driver => $component) :
            $configC = !empty($component['config']) ? $component['config'] : array();
            Ar::setC($driver . '.' . $component['class'], $configC);
        endforeach;

    }

    static private function createWebApplication($class)
    {
        if (!Ar::a($class))
            Ar::setA($class, new $class);

        return Ar::a($class);

    }

}
