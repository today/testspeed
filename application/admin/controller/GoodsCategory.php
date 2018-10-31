<?php
namespace app\admin\controller;

use app\admin\model\GoodsBrand;
use app\admin\model\GoodsCategory as GoodsCategoryModel;

class GoodsCategory extends Base
{
    public function index(){
        $list = GoodsCategoryModel::all();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function categorySave(){
        if($this->request->method() === "POST"){
            $category = $this->request->post();
            $file = $this->request->file('icon');
            if($file){
                $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/icon/');
                if(!$fileName){
                    return $this->error($file->getError(),'','',1);
                }
                $category['icon'] = stripslashes($fileName->getPathName());
            }
            $output = new GoodsCategoryModel();
            $result = $output->save($category);

            if($result) {
                $this->success('添加成功','index','',1);
            }else{
                unlink($category['icon']);
                $this->error('添加失败','','',1);
            }
        }
        $id = $this->request->param('id');
        $vo = GoodsCategoryModel::get($id);
        $this->assign('list',$vo);
        return $this->fetch();
    }

    public function categoryUpdate(){
        if($this->request->method() === "POST"){
            $category = $this->request->post();
            $output = GoodsCategoryModel::get($category['id']);
            $img_url = $output->getData('icon');
            $file = $this->request->file('icon');

            if($file){
                $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/icon/');
                if(!$fileName){
                    return $this->error($file->getError(),'','',1);
                }
                $category['icon'] = stripslashes($fileName->getPathName());
            }

            $result = $output->save($category);

            if($result) {
                if(isset($category['icon']) && is_file($img_url)){
                    unlink($img_url);
                }
                $this->success('更新成功','index','',1);
            }else{
                $this->error('更新失败','','',1);
            }
        }
    }

    public function delete(){
        $result = GoodsCategoryModel::destroy($this->request->param('id'));
        if($result){
            $this->success('删除成功','index','',1);
        }else{
            $this->error('删除失败','','',1);
        }
    }
}