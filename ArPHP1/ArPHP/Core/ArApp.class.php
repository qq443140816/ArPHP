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
            if (!empty($component['lazy']) && $component['lazy'] == true)
                continue;
            foreach ($component as $engine => $cfg) :
                if (!empty($cfg['lazy']) && $cfg['lazy'] == true || $engine == 'lazy')
                    continue;
                $configC = !empty($cfg['config']) ? $cfg['config'] : array();

                Ar::setC($driver . '.' . $engine, $configC);
            endforeach;
        endforeach;

    }

    static private function createWebApplication($class)
    {
        if (!Ar::a($class))
            Ar::setA($class, new $class);

        return Ar::a($class);

    }

}
