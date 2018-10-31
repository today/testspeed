<?php
namespace app\admin\controller;

use app\admin\model\GoodsFactory;
use app\admin\model\GoodsOrder as GoodsOrderModel;

class GoodsOrder extends Base
{
    public function index(){
        $list = GoodsOrderModel::order('id desc')->paginate(20);
        $factory = GoodsFactory::all();
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('factory',$factory);
        return $this->fetch();
    }

    public function search(){
        $list = GoodsOrderModel::order('id desc');
        $param = $this->request->param();
        if($this->request->param()){
            if($this->request->param('order_sn')){
                $list = $list->where('order_sn',$this->request->param('order_sn'));
            }
            if($this->request->param('goods_name')){
                $list = $list->where('goods_name','like','%'.$this->request->param('goods_name').'%');
            }
            if($this->request->param('real_name')){
                $list = $list->where('real_name','like','%'.$this->request->param('real_name').'%');
            }
            if($this->request->param('mobile')){
                $list = $list->where('mobile',$this->request->param('mobile'));
            }
            if($this->request->param('factory_id')){
                $list = $list->where('factory_id',$this->request->param('factory_id'));
            }
            if($this->request->param('status') || $this->request->param('status') == 0){
                $list = $list->where('status',$this->request->param('status'));
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

        $factory = GoodsFactory::all();
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('param',$param);
        $this->assign('page',$page);
        $this->assign('factory',$factory);
        return $this->fetch();
    }
 /*发货页面*/
   public function fahuo(){
     $id = $this->request->param('id');
     $order = GoodsOrderModel::get($id);
     $orderid = $order['order_sn'];
     $goods_name =  $order['goods_name'];
     $guige = $order['guige'];
     $remark = $order['remark'];
     $this->assign('oid',$id);
     $this->assign('orderid',$orderid);
     $this->assign('goods_name',$goods_name);
     $this->assign('guige',$guige);
     $this->assign('remark',$remark);
     return $this->fetch();
    }
    public function fahuocl(){
        $id = $this->request->param('id');
        $order = GoodsOrderModel::get($id);
        if($this->request->method() === "POST"){
             $order->status=$this->request->post('status');
            $order->delivery_method=$this->request->post('delivery_method');
            $order->delivery_sn=$this->request->post('delivery_sn');
             $result = $order->save($order);

            if($result) {
                $this->success('更新成功','index','',1);
            }else{
                $this->error('更新失败','','',1);
            }
        }
    }


}