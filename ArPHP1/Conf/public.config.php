<?php
    return array(
            'components' => array(
                    'db' => array(
                            'lazy' => true,
                            'class' => 'DbMysql',
                            'config' => array(
                                'read' => array(
                                    'default' => array(
                                            // 'dsn' => 'mysql:host=10.10.1.21;dbname=scedu;port=3306',

                                            // 'user' => 'root',

                                            // 'pass' => 'abcd~!@#$%^&*()_+',

                                            // 'prefix' => 'jieqi_', 

                                            // 'option' => array(
                                            //         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                            //         PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                                            //         PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                                            //     ),

                                            'dsn' => 'mysql:host=localhost;dbname=scedu;port=3306',

                                            'user' => 'root',

                                            'pass' => '123456',

                                            'prefix' => 'jieqi_', 

                                            'option' => array(
                                                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                                                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                                                ),
                                        ),
                                ),

                            ),
                        ),

                    'rpc' => array(
                            'class' => 'Json',
                            'lazy' => true,
                            'config' => array(
                                    // 'remotePrefix' => 'http://192.168.1.109/test/api',
                                    'remotePrefix' => 'http://localhost/test/api',
                                ),
                        ),

                    'cache' => array(
                            'class' => 'Redis',
                            'lazy' => true,
                            'config' => array(
                                    'read' => array('nn'),
                                ),
                        ),
                ),

            'moduleLists' => array(
                    'test'
                ),

        );