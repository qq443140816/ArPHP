<?php
// 开启调试模式 默认开启 开启将显示DEBUG信息
define('AR_DEBUG', true);
// 默认的项目目录
define('AR_DEFAULT_APP_NAME', 'ws');
// 以http方式服务开启webservice 后续会出基于socket的php多进程服务service
define('AR_RUN_AS_SERVICE_HTTP', true);

// 引入ArPHP初始化文件
require_once  'ArPHP/init.php';
