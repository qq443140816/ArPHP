ArPHP
=====

ArPHP For Coders

ArPHP框架,单文件框架 for coder
高性能 & 极度轻巧 & 組件化 & 伸縮性


回望第一次arphp提交到现在都三年多了，时间过的真快，期间
arphp开发了很多优秀项目稳定运行。那么如果你想告别复杂冗余的大型框架，告别新手实习生都会的大众框架，
用一个高性能的极简框架来开发掌控项目，它有多种模式，架构？二次开发？嵌入开发？切入开发？
定向开发？这些arphp统统的都可以拿下，何不让自己潇洒的做一次统帅？选择arphp，你值得拥有。
更新一次不容易，一个人写文档，维护官网,一个群，开发一堆东西，文档更新太晚了, 说声sorry

还是不容易的，喜欢？用过？了解过？麻烦各位看官赏脸star一下


2017/5/9更新 arphp2.0发布

增加Lib库文件目录，推荐公用Model ，Module放入目录,支持命名空间加载公共库
如 arModule('Lib/Hello')->sayworld()  调用 namespace Lib/Module/Hello
单文件框架保持同步
优化代码增强性能
增加ArView核心视图动态渲染模板主题加载，解决传统资源文件引入混乱问题，前后端不统一资源引入问题等
增加微信公众号组件

2017/5/26    arphp2.0.1

增加 WEB_CLI模式
// 以web cli 方式运行项目 入口文件cli.php
define('AR_AS_WEB_CLI', true);
include 'arphp.php';


运行   php cli.php /main/User/info     执行main/UserController/infoAction 方法



更多使用帮助及例子请加官方qq群，


官方支持
git : https://github.com/assnr/ArPHP.git（最新版）

ArPHP开源群 : 259956472
官网：www.arphp.org


基本使用：


初始化页面
只要在入口文件里包含ArPHP初始化文件 init.php
或者引入arphp.php(单文件框架和主框架功能一模一样压缩版便于携带切入开发)



index.php

代码

1   include_once ArPHP/init.php

2   include_once arphp.php(单文件框架和主框架功能一模一样压缩版便于携带切入开发)



访问index.php即可件 hello ArPHP!


常用用法(特别感谢JackLiu对以下常用操作使用的提供)

        /*
        // mysql 事物操作
        try {
            // 开启事物
            arComp('db.mysql')->transBegin();

            // 数据库业务逻辑代码。。。。。。。。

            // 提交
            arComp('db.mysql')->transCommit();
        } catch (Exception $e) {
            // 回滚
            arComp('db.mysql')->transRollBack();
        }
        */
        // 跳转到regAction
        // $this->redirect(array('reg', array('a' => 1)), '跳转了', 4);

        // 为 session 设置 变量值
        // arComp('list.session')->set('name', 'value');

        // 清空 session
        // arComp('list.session')->flush();

        // 为 session 获取 变量值
        // arComp('list.session')->get('name');

        // 输出显示模板 默认为index.php
        // $this->display();

        // 设置布局文件
        // $this->setLayoutFile('a');

        // 数据库表table查询一行
        // $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('t1')->queryRow();

        // 用模型实现查询（已定义T1Model）
        // $sp = T1Model::model()->getDb()->where(array('id' => 1))->queryRow();

        // 用模型实现查询（未定义T1Model）
        // $sp = arModel::model('T1')->getDb()->where(array('id' => 1))->queryRow();

        // 数据库查询所有
        // $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('t1')->queryAll();
        // 为 session 获取 变量值
        // arComp('list.session')->get('name');

        // 输出显示模板 默认为index.php
        // $this->display();

        // 设置布局文件
        // $this->setLayoutFile('a');

        // 数据库表table查询一行
        // $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('t1')->queryRow();

        // 用模型实现查询（已定义T1Model）
        // $sp = T1Model::model()->getDb()->where(array('id' => 1))->queryRow();

        // 用模型实现查询（未定义T1Model）
        // $sp = arModel::model('T1')->getDb()->where(array('id' => 1))->queryRow();

        // 数据库查询所有
        // $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('t1')->queryAll();

        // 数据库查询所有以指定键返回数据
        // $sp = arComp('db.mysql')->select('')->where(array('name' => 'wyp'))->table('t1')->queryAll('id');

        // 数据库查询所有以指定键返回原始数据
        // $sp = arComp('db.mysql')->select('')->where(array('name' => 'wyp'))->table('t1')->queryColumn('id');

        // 数据库更新数据
        // $sp = arComp('db.mysql')->where(array('name' => 'wyp'))->table('t1')->update(array('column' => 'value'));

        // 数据库插入数据
        // $sp = arComp('db.mysql')->where(array('name' => 'wyp'))->table('t1')->insert(array('column' => 'value'));

        // 测试HTTP请求参数
        // $result = arRequest('');

        // 网络调用main项目 IndexConterller apiAction
        // $res = arComp('rpc.json')->callApi('/main/index/api');

        // 加密字符串
        // $result = arComp('hash.mcrypt')->encrypt('yunListMan');

        // 解密字符串
        // $dresult = arComp('hash.mcrypt')->decrypt($result);

        // 远程POST数据到指定url
        // $data = array(
        //     'userName' => 'yctest',
        //     'loginTel' => '12345678914',
        //     'token' => '9sZwwC29j_i_aJ2ZGg0DxoPzWrgKBfgz_x_g6vqgfvXnrlRz_x_gg8g=',
        // );

        // 读取配置常量
        // arCfg('CONFIG_KEY');

        // arComp('rpc.source')->method = 'post';
        // $result = arComp('rpc.source')->remoteCall('http://cs.test.cn/yu/reg', $data);

        // 调用内部Service
        // $result = arComp('rpc.service')->WsTest('tt', array('ssd', 3));

        // 执行model方法
        // MyModel::model()->yourFunction();

        // 创建完整的URL
        // echo arComp('url.route')->createUrl('', array('ni' => '33', 'p' => 4));




    // 数据库表user查询一行
        $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('user')->queryRow();

    // 用模型实现查询（已定义UserModel）
        $sp = UserModel::model()->getDb()->where(array('id' => 1))->queryRow();

    // 用模型实现查询（未定义UserModel）
        $sp = arModel::model('User')->getDb()->table('user')->where(array('id' => 1))->queryRow();

    // 数据库查询所有
        $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('user')->queryAll();

    // 数据库表user查询一行
        $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('user')->queryRow();

    // 数据库查询所有以指定键返回数据
        $sp = arComp('db.mysql')->select('')->where(array('name' => 'xxx'))->table('user')->queryAll('id');

    // 数据库查询所有以指定键返回原始数据
        $sp = arComp('db.mysql')->select('')->where(array('name' => 'xxx'))->table('user')->queryColumn('id');

    // 数据库更新数据
        $sp = arComp('db.mysql')->where(array('name' => 'wyp'))->table('user')->update(array('column' => 'value'));

    // 数据库插入数据
        $sp = arComp('db.mysql')->where(array('name' => 'wyp'))->table('user')->insert(array('column' => 'value'));

    // 贪婪匹配 , 生成的链接保留之前请求的参数， 非常适合做多级栏目切换传参
        arU('', array('greedyUrl' => true, 'aid' => 2));


