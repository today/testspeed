<?php
/*
 * @author 靳建飞 | e-mail:244029746@qq.com
 * @copyright 靳建飞 2018.3.9 星期5
 * 代币管理：页面；列表；
 */
namespace app\admin\controller;
use app\common\model\UserQueries;

/**
 * 用户咨询反馈列表
 * Class UserContent
 * @package app\admin\controller
 */
class UserContent extends Base
{
    /**
     * 咨询列表
     * @return mixed
     */
    public function index(){
        $group_list = ['0'=>'未处理咨询','1'=>'已处理咨询'];
        $this->assign('navigation','usercontent_index');
        $back_type = $this->request->param('back_type',0);
        $back = UserQueries::where('uq_type',1)->where('uq_status',$back_type)->order('uq_star','desc')->select();
        $this->assign('list',$back);
        $this->assign('back_type',$back_type);
        $this->assign('group_list',$group_list);
        return $this->fetch('user_cont');
    }

    /**
     * 修改状态js
     */
    public function ajaxUpdateStatus(){
        if($this->request->isAjax()){
            $date['uq_id'] = $this->request->post('id');
            $date['uq_status'] = $this->request->post('value');
            $date['uq_answer'] = $this->request->post('answer');
            $output = UserQueries::update($date);
            if($output){
                return $this->success('状态修改成功！');
            }else{
                return $this->error('状态修改失败！');
            }
        }
    }


    /**
     * 反馈列表
     * @return mixed
     */
    public function feedback(){
        $group_list = ['0'=>'未处理反馈','1'=>'已处理反馈'];
        $this->assign('navigation','usercontent_feedback');
        $back_type = $this->request->param('back_type',0);
        $back = UserQueries::where('uq_type',2)->where('uq_status',$back_type)->order('uq_star','desc')->select();
        $this->assign('list',$back);
        $this->assign('back_type',$back_type);
        $this->assign('group_list',$group_list);
        return $this->fetch('user_feedback');
    }

}