<?php

namespace app\admin\model;

use think\Model;

class InviteRecord extends Model
{
    protected $pk = 'ir_id';

    protected $createTime = 'ir_create_time';
    protected $autoWriteTimestamp = true;

    public function getIrPowerTypeAttr($value){
        $array = array(1=>'永久算力',-1=>'临时算力');
        return $array[$value];
    }
}