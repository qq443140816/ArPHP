<?php
/**
 * Ar default frame config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
            'PATH' => array(
            'PUBLIC' => SERVER_PATH . arCfg('requestRoute.m') . '/Public/',
            'VIEW' => ROOT_PATH . arCfg('requestRoute.m') . DS . 'View' . DS,
        ),
   );