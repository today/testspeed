<?php
namespace app\admin\model;

use think\Model;

class Users extends Model
{
    protected $pk = 'usr_id';

    protected $createTime = 'usr_create_time';
    // 定义时间戳字段名
    protected $updateTime = 'usr_last_time';
    protected $autoWriteTimestamp = true;


    /**
     * 关联用户资产表
     */
    public function uasset(){
        return $this->hasOne('UserAssets','ua_user_id','usr_id')->bind('ua_surplus_number');
    }
}