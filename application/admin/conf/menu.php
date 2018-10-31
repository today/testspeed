<?php
return	array(
        array('name' => '首页' ,'ico' => 'fa-legal', 'act'=> 'index','op'=>'Index'),
        array('name' => '挖矿管理','ico' => 'fa-legal','child'=>array(
            array('name' => '矿产管理', 'act'=>'mining', 'op'=>'Mining'),
        )),

        array('name' => '用户管理','ico' => 'fa-users','child'=>array(
            array('name'=>'会员列表','act'=>'index','op'=>'User'),
            array('name'=>'会员矿产','act'=>'miningList','op'=>'User'),
//            array('name'=>'充值记录','act'=>'recharge','op'=>'User'),
        )),
    array('name' => '商城管理','ico' => 'fa-legal','child'=>array(
        array('name' => '分类管理', 'act'=>'index', 'op'=>'GoodsCategory'),
        array('name' => '品牌管理', 'act'=>'index', 'op'=>'GoodsBrand'),
        array('name' => '厂家管理', 'act'=>'index', 'op'=>'GoodsFactory'),
        array('name' => '商品管理', 'act'=>'index', 'op'=>'Goods'),
        array('name' => '订单管理', 'act'=>'index', 'op'=>'GoodsOrder'),
    )),
        array('name' => '权限管理','ico' => 'fa-lock','child'=>array(
            array('name' => '管理员列表', 'act'=>'index', 'op'=>'Admin'),
            array('name' => '角色管理', 'act'=>'roles', 'op'=>'Admin'),
            array('name'=>'权限资源列表','act'=>'index','op'=>'SystemMenu'),
            array('name' => '管理员日志', 'act'=>'log', 'op'=>'Admin'),
        )),

        array('name' => 'token管理','ico' => 'fa-btc','child'=>array(
            array('name' => 'token管理', 'act'=>'index', 'op'=>'Tokens'),
        )),

    //        array('name' => '咨询管理','ico' => 'fa-edit','child'=>array(
//            array('name' => '咨询管理', 'act'=>'index', 'op'=>'UserContent'),
//            array('name' => '反馈管理', 'act'=>'feedback', 'op'=>'UserContent'),
//        )),
//        array('name' => '数据管理','ico' => 'fa-edit','child'=>array(
//            array('name' => '数据备份', 'act'=>'tools_data', 'op'=>'System'),
//            array('name' => '数据还原', 'act'=>'restore_data', 'op'=>'System'),
//            array('name' => '数据恢复', 'act'=>'log', 'op'=>'Admin'),
//            array('name' => 'SQL查询', 'act'=>'log', 'op'=>'Admin'),
//        )),
        array('name' => '系统设置','ico' => 'fa-edit','child' => array(
            array('name'=>'参数设置','act'=>'index','op'=>'System'),
            array('name'=>'幻灯片','act'=>'banner','op'=>'System'),
            array('name'=>'发布通知','act'=>'inform','op'=>'System'),
            array('name'=>'更新版本','act'=>'version','op'=>'System'),
            //array('name'=>'验证码设置','act'=>'index4','op'=>'System'),
    //            array('name'=>'自定义导航栏','act'=>'navigationList','op'=>'System'),
//            array('name'=>'友情链接','act'=>'linkList','op'=>'Article'),
    //            array('name'=>'清除缓存','act'=>'cleanCache','op'=>'System'),
    //            array('name'=>'自提点','act'=>'index','op'=>'Pickup'),
        )),
//    array('name' => '日志管理','ico' => 'fa-file-o','act'=>'index', 'op'=>'Logs'),

);