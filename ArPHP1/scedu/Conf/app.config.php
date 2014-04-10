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
                        'read' => array(
                            'default' => array(

                                    'dsn' => 'mysql:host=localhost;dbname=scedu;port=3306',

                                    'user' => 'root',

                                    'pass' => '123456',

                                    'prefix' => 'jieqi_', 

                                    'option' => array(
                                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                                        ),
                                ),
                        ),

                        'write' => array(
                            'default' => array(
                                    'dsn' => 'mysql:host=192.168.1.166;dbname=test;port=3306',

                                    'user' => 'test',

                                    'pass' => '123456',

                                    'option' => array(
                                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                                        ),
                                ),
                            ),
                        ),
                    ),
            ),
    );