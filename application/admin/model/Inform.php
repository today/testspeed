<?php
namespace app\admin\model;

use think\Model;

class Inform extends model
{
    protected $pk = 'if_id';

    protected $createTime = 'if_create_time';
    protected $autoWriteTimestamp = true;

    public function getIfStatusAttr($value){
        $array = array(1=>'开启',-1=>'禁用');
        return $array[$value];
    }
}