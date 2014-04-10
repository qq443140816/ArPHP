<?php
/**
 * Ar default frame config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
        'components' => array(

                'db' => array(
                        'class' => 'DbMysql',
                        'config' => array(
                            ),
                    ),

                'url' => array(
                        'class' => 'route',
                    ),
            ),
    );