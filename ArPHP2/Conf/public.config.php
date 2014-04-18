<?php
    return array(
            'components' => array(
                    'db' => array(
                            'lazy' => true,
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
                ),

            'moduleLists' => array(
                    'scedu',
                    'wap'
                ),

        );