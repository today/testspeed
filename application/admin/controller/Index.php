<?php
/*
 * @author 靳建飞 | e-mail:244029746@qq.com
 * @copyright 靳建飞 2018.3.9 星期5
 * 代币管理：页面；列表；
 */
namespace app\admin\controller;

use app\admin\model\System;
use app\admin\model\Users;
use think\Db;

class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     * 后台首页
     */
    public function index()
    {
        //获取服务器数据
        $sys = System::where('sys_id','sys')->find();
        //获取今日用户量
        $nowUser = Db::name('users')->where('usr_create_time','today')->count();
        $this->assign('navigation','index_index');
        $this->assign('nowUser',$nowUser);
        $this->assign('sys',$sys);
        return $this->fetch();
    }

    /**
     * 显示数据
     */
    public function userData(){
        $data = Users::all();
        
        return json($data);
    }
}
