<?php
namespace app\admin\controller;

use app\admin\model\Mining as MiningModel;
use app\admin\model\Tokens;

class Mining extends Base
{


    /**
     * 被挖矿产浏览
     */
    public function mining(){
        $mining = MiningModel::with('listtoken')->select();
        $this->assign(array('navigation'=>'mining_mining','title'=>'挖矿矿产浏览'));
        $this->assign('list',$mining);
        return $this->fetch();
    }

    /**
     * 添加矿产
     */
    public function add_mining(){
        if( $this->request->method() == "POST" ) {
            $mining = $this->request->post();
            $validate = $this->validate($mining,'Mining');//验证器
            if(true!==$validate){//通过验证
               return $this->error($validate,'','',1);
            }
            $result = MiningModel::create($mining);
            if($result){
                return $this->success('矿产添加成功!','mining','',1);
            }
            return $this->error('矿产添加失败','mining','',1);
        }

        //可选矿产
        $token = Tokens::where('tok_id',1)->column('tok_id,tok_name');
        $this->assign(array('navigation'=>'mining_mining','title'=>'添加矿产'));
        $this->assign('token',$token);
        return $this->fetch();
    }

    /**
     * 修改矿产
     */
    public function update_mining(){
        if( $this->request->method() == "POST" ) {
            $mining = $this->request->post();

            $validate = $this->validate($mining,'Mining.edit');//验证器
            if(true!==$validate){//通过验证
                return $this->error($validate,'','',1);
            }
            $min = MiningModel::with('listtoken')->where('min_id',$mining['min_id'])->find();
            $min->min_number = $mining['min_number'];
            $min->min_frequency = $mining['min_frequency'];
//            $min->min_level = $mining['min_level'];
            $result = $min->save();
            if($result){
                return $this->success('矿产'.$min['tok_name'].'修改成功!','mining','',1);
            }
            return $this->error('矿产'.$min['tok_name'].'修改失败','mining','',1);
        }

        $id = $this->request->param('id');
        $mining = MiningModel::with('listtoken')->where('min_id',$id)->find();//已添加矿产
        $this->assign(array('navigation'=>'mining_mining','title'=>'修改'.$mining->tok_name.'矿产'));
        $this->assign('mining',$mining);
        return $this->fetch();
    }

    /**
     * 删除矿产
     */
    public function mining_delete(){
        $id = $this->request->post('id');
        $result = MiningModel::destroy($id);
        if($result) {
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }
}
