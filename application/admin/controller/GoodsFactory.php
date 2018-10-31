<?php
namespace app\admin\controller;

use app\admin\model\GoodsFactory as GoodsFactoryModel;

class GoodsFactory extends Base
{
    public function index(){
        $search = $this->request->post();
        $list = GoodsFactoryModel::order('id asc');
        $name = '';
        $pageNum = 20;
        if($search){
            $pageNum = 1000000000;
            if($search['name']){
                $list = $list->where('name','like','%'.$search['name'].'%');
                $name = $search['name'];
            }
        }
        $list = $list->paginate($pageNum);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('name',$name);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function FactorySave(){
        if($this->request->method() === "POST"){
            $factory = $this->request->post();
            $output = new GoodsFactoryModel();
            $factory['password'] = md5($factory['password']);
            $result = $output->save($factory);
            if($result) {
                $this->success('添加成功','index','',1);
            }else{
                $this->error('添加失败','','',1);
            }
        }
        $id = $this->request->param('id');
        $vo = GoodsFactoryModel::get($id);
        $this->assign('list',$vo);
        return $this->fetch();
    }

    public function factoryUpdate(){
        if($this->request->method() === "POST"){
            $factory = $this->request->post();
            $output = GoodsFactoryModel::get($factory['id']);
            if($output->password != $factory['password']){
                $factory['password'] = md5($factory['password']);
            }

            $result = $output->save($factory);
            if($result) {
                $this->success('更新成功','index','',1);
            }else{
                $this->error('更新失败','','',1);
            }
        }
    }

    public function delete(){
        $result = GoodsFactoryModel::destroy($this->request->param('id'));
        if($result){
            $this->success('删除成功','index','',1);
        }else{
            $this->error('删除失败','','',1);
        }
    }
}