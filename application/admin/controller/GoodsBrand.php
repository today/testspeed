<?php
namespace app\admin\controller;

use app\admin\model\GoodsBrand as GoodsBrandModel;

class GoodsBrand extends Base
{
    public function index(){
        $list = GoodsBrandModel::order('asort asc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function brandSave(){
        if($this->request->method() === "POST"){
            $brand = $this->request->post();
            $file = $this->request->file('icon');
            if($file){
                $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/icon/');
                if(!$fileName){
                    return $this->error($file->getError(),'','',1);
                }
                $brand['icon'] = stripslashes($fileName->getPathName());
            }else{
                $this->error('请上传icon','','',1);
            }
            $output = new GoodsBrandModel();
            $result = $output->save($brand);

            if($result) {
                $this->success('添加成功','index','',1);
            }else{
                unlink($brand['icon']);
                $this->error('添加失败','','',1);
            }
        }
        $id = $this->request->param('id');
        $vo = GoodsBrandModel::get($id);
        $this->assign('list',$vo);
        return $this->fetch();
    }

    public function brandUpdate(){
        if($this->request->method() === "POST"){
            $brand = $this->request->post();
            $output = GoodsBrandModel::get($brand['id']);
            $img_url = $output->getData('icon');
            $file = $this->request->file('icon');

            if($file){
                $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/icon/');
                if(!$fileName){
                    return $this->error($file->getError(),'','',1);
                }
                $brand['icon'] = stripslashes($fileName->getPathName());
            }

            $result = $output->save($brand);

            if($result) {
                if(isset($brand['icon']) && is_file($img_url)){
                    unlink($img_url);
                }
                $this->success('更新成功','index','',1);
            }else{
                $this->error('更新失败','','',1);
            }
        }
    }

    public function delete(){
        $result = GoodsBrandModel::destroy($this->request->param('id'));
        if($result){
            $this->success('删除成功','index','',1);
        }else{
            $this->error('删除失败','','',1);
        }
    }

}