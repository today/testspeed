<?php
/*
 * @author 靳建飞 | e-mail:244029746@qq.com
 * @copyright 靳建飞 2018.3.9 星期5
 * 代币管理：页面；列表；
 */

namespace app\admin\controller;

use app\admin\model\AssetsInfos;
use app\admin\model\Mining;
use app\admin\model\Tokens as TokensModel;

class Tokens extends Base{
    /**
     * 代币展示
     */
    public function index(){
        $token = TokensModel::all();
        $token = TokensModel::order('tok_create_time','desc')->paginate(10,false,[
            'type'     => 'bootstrap',
        ]);
        $page = $token->render();
        $this->assign('page',$page);
        $this->assign('title','token管理');
        $this->assign('navigation','tokens_index');
        $this->assign('list',$token);
        return $this->fetch();
    }

    /**
     * 代币添加与修改
     */
    public function token_save(){
        if($this->request->method() === "POST"){
            $token = $this->request->post();
            $file = $this->request->file('file_img');
            if(!$file){
                //无图片
                return $this->error('请上传图片!','','',1);
            }

            $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/token/');
            if(!$fileName){
                return $this->error($file->getError(),'','',1);
            }
            $token['tok_img'] = $fileName->getPathName();
            $token['tok_create_time'] = time();
            $output = new TokensModel();
            $result = $output->save($token);

            if($result) {
                $this->success($output->tok_name.'添加成功','index','',1);
            }else{
                unlink($token['tok_img']);
                $this->error($output->tok_name.'添加失败','','',1);
            }
        }
        $id = $this->request->param('id');
        $vo = TokensModel::get($id);
        $this->assign('list',$vo);
        $this->assign('title','token添加');
        $this->assign('navigation','tokens_index');
        return $this->fetch();
    }

    /**
     * @return mixed|void
     * 代币修改
     */
    public function token_update(){
        if($this->request->method() === "POST"){
            $token = $this->request->post();
            $output = TokensModel::get($token['tok_id']);
            $img_url = $output->getData('tok_img');
            $file = $this->request->file('file_img');

            if($file){
                $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/token');
                if(!$fileName){
                    return $this->error($file->getError(),'','',1);
                }
                $token['tok_img'] = $fileName->getPathName();
            }

            $result = $output->save($token);

            if($result) {
                if(isset($token['tok_img']) && is_file($img_url)){
                    unlink($img_url);
                }
                $this->success($output['tok_name'].'更新成功','index','',1);
            }else{

                $this->error($output['tok_name'].'更新失败','','',1);
            }
        }
        return $this->redirect('index');
    }

    /**
     * 代币删除
     */
//    public function token_delete(){
//        if($this->request->method() == "POST"){
//            $id = $this->request->post('id');
//            $aiToken = AssetsInfos::where('ai_token_id',$id)->limit(1)->find();
//            if($aiToken){
//                return $this->error('token 已发放,不可删除!');
//            }
//            $mining = Mining::where('min_token_id',$id)->limit(1)->find();
//            if($mining){
//                return $this->error('token 已在矿产中添加,不可删除!');
//            }
//            $token = TokensModel::get($id);
//            $tok_img = $token->getData('tok_img');
//            $result = $token->delete();
//            if($result){
//                unlink($tok_img);
//                return $this->success('token 删除成功!');
//            }else{
//                return $this->error('token 删除失败');
//            }
//        }
//
//    }
    /**
     * 验证代币名称是否存在
     */
    public function ajax_by_token(){
        $param = $this->request->post('param');
        $name = $this->request->post('name');
        $adm = TokensModel::where($name,$param)->find();
        if($adm!==null){
            $data['status']='n';
            $data['info'] ='已存在';
        }else{
            $data['status']='y';
            $data['info'] ='可使用';
        }
        return $data;
    }
}