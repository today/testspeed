<?php
namespace app\admin\controller;

use app\admin\model\Goods as GoodsModel;
use app\admin\model\GoodsBrand;
use app\admin\model\GoodsCategory;
use app\admin\model\GoodsFactory;

class Goods extends Base
{
    public function index(){
        $list = GoodsModel::order('id desc')->paginate(20);
        $listArr = array();
        foreach($list as $item){
            $brand = GoodsBrand::find($item['brand_id']);
            $category = GoodsCategory::find($item['category_id']);
            if($item['factory_id']){
                $factory = GoodsFactory::find($item['factory_id']);
                $factory_name = $factory['name'];
            }else{
                $factory_name = '   暂无';
            }
            $item['brand_name'] = $brand['name'];
            $item['category_name'] = $category['name'];
            $item['factory_name'] = $factory_name;
            $listArr[] = $item;
        }
        $page = $list->render();
        $factory = GoodsFactory::all();
        $category = GoodsCategory::all();
        $brand = GoodsBrand::all();
        $this->assign('factory',$factory);
        $this->assign('category',$category);
        $this->assign('brand',$brand);
        $this->assign('list',$listArr);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function search(){
        $list = GoodsModel::order('id desc');
        $param = $this->request->param();
        if($this->request->param()){
            if($this->request->param('name')){
                $list = $list->where('name','like','%'.$this->request->param('name').'%');
            }
            if($this->request->param('is_use')){
                if($this->request->param('is_use') == 'yes'){
                    $list = $list->where('is_use',0);
                }else{
                    $list = $list->where('is_use',1);
                }
                $list = $list->where('is_use',$this->request->param('is_use'));
            }
            if($this->request->param('is_hot')){
                if($this->request->param('is_hot') == 'yes'){
                    $list = $list->where('is_hot',1);
                }else{
                    $list = $list->where('is_hot',0);
                }
            }
            if($this->request->param('category_id')){
                $list = $list->where('category_id',$this->request->param('category_id'));
            }
            if($this->request->param('brand_id')){
                $list = $list->where('brand_id',$this->request->param('brand_id'));
            }
            if($this->request->param('factory_id')){
                $list = $list->where('factory_id',$this->request->param('factory_id'));
            }
            if($this->request->param('start_time') && $this->request->param('end_time')){
                $list = $list->whereTime('created_at', 'between', [$this->request->param('start_time'), $this->request->param('end_time')]);
            }else{
                if($this->request->param('start_time')){
                    $list = $list->where('created_at','> time',$this->request->param('start_time'));
                }
                if($this->request->param('end_time')){
                    $list = $list->where('created_at','<= time',$this->request->param('end_time'));
                }
            }
        }
        $list = $list->paginate(20,false,['query'=>request()->param()]);
        $listArr = array();
        foreach($list as $item){
            $brand = GoodsBrand::find($item['brand_id']);
            $category = GoodsCategory::find($item['category_id']);
            if($item['factory_id']){
                $factory = GoodsFactory::find($item['factory_id']);
                $factory_name = $factory['name'];
            }else{
                $factory_name = '   暂无';
            }
            $item['brand_name'] = $brand['name'];
            $item['category_name'] = $category['name'];
            $item['factory_name'] = $factory_name;
            $listArr[] = $item;
        }
        $page = $list->render();
        $factory = GoodsFactory::all();
        $category = GoodsCategory::all();
        $brand = GoodsBrand::all();
        $this->assign('factory',$factory);
        $this->assign('category',$category);
        $this->assign('brand',$brand);
        $this->assign('list',$listArr);
        $this->assign('page',$page);
        $this->assign('param',$param);
        return $this->fetch();
    }

    public function goodsSave(){
        if($this->request->method() === "POST"){
            $goods = $this->request->post();
            $file = $this->request->file('image');
            
            if($file){
                $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/goods/');
                if(!$fileName){
                    return $this->error($file->getError(),'','',1);
                }
                $goods['image'] = stripslashes($fileName->getPathName());
            }else{
                $this->error('请上传icon','','',1);
            }

            $files = $this->request->file('dimage');
            if($files){
                $imageArr = array();
                foreach($files as $file){
                    $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/goods/');
                    if(!$fileName){
                        return $this->error($file->getError(),'','',1);
                    }
                    $imageArr[] = stripslashes($fileName->getPathName());
                }
                //unset($imageArr[0]);
                $goods['files'] = serialize(array_merge($imageArr));
            }else{
                $this->error('请上传产品详情图','','',1);
            }
            $goods['detail'] = $_POST['detail'];
            $output = new GoodsModel();
            $result = $output->save($goods);

            if($result) {
                $this->success('添加成功','index','',1);
            }else{
                unlink($goods['icon']);
                $this->error('添加失败','','',1);
            }
        }
        $id = $this->request->param('id');
        $vo = GoodsModel::get($id);
        $brand=GoodsBrand::where('is_show',0)->order('asort asc')->select();
        $category=GoodsCategory::where('is_use',0)->order('id asc')->select();
        $factory = GoodsFactory::all();
        $filesImage = array();
        if($vo){
            $filesImage = unserialize($vo->files);
        }

        $this->assign('brandlist',$brand);
        $this->assign('categorylist',$category);
        $this->assign('factory',$factory);
        $this->assign('list',$vo);
        $this->assign('filesImage',$filesImage);
        return $this->fetch();
    }

    public function goodsUpdate(){
        if($this->request->method() === "POST"){
            $goods = $this->request->post();
            $output = GoodsModel::get($goods['id']);
            $img_url = $output->getData('image');
            $file = $this->request->file('image');

            if($file){
                $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/goods/');
                if(!$fileName){
                    return $this->error($file->getError(),'','',1);
                }
                $goods['image'] = stripslashes($fileName->getPathName());
            }
            $files = $this->request->file('dimage');
            $imagesArr = unserialize($output->files);
            if(empty($imagesArr) && !$files){
                $this->error('请上传产品详情展示图','/admin/goods/goodssave/id/'.$output->id,'',1);
            }
            if($files){
                $filesArr = array();
                foreach($files as $file){
                    $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/goods/');
                    if(!$fileName){
                        return $this->error($file->getError(),'','',1);
                    }
                    $filesArr[] = stripslashes($fileName->getPathName());
                }
                $goods['files'] = serialize(array_merge($filesArr));
            }

            $result = $output->save($goods);

            if($result) {
                if(isset($goods['icon']) && is_file($img_url)){
                    unlink($img_url);
                }
                $this->success('更新成功','index','',1);
            }else{
                $this->error('更新失败','/admin/goods/goodssave/id/'.$output->id,'',1);
            }
        }
    }

    public function deleteImages(){
        $goods = GoodsModel::find($this->request->post('id'));
        $key = $this->request->post('key');
        if($goods){
            $imagesArr = unserialize($goods->files);
            unset($imagesArr[$key]);
            $files = serialize(array_merge($imagesArr));
            $goods->files = $files;
            $goods->save();
            $result = array(
                'code'=>200,
                'msg' => 'success',
            );
            return json($result);
        }else{
            $result = array(
                'code'=>101,
                'msg' => '无效的id',
            );
            return json($result);
        }
    }

    public function delete(){
        $result = GoodsModel::destroy($this->request->param('id'));
        if($result){
            $this->success('删除成功','index','',1);
        }else{
            $this->error('删除失败','','',1);
        }
    }

    public function status(){
        $goods = GoodsModel::find($this->request->post('id'));
        if($goods->is_use == 0){
            $goods->is_use = 1;
        }elseif($goods->is_use == 1){
            $goods->is_use = 0;
        }
        $goods->save();
        $result = array(
            'code'=>200,
            'msg' => 'success',
        );
        return json($result);
    }
}