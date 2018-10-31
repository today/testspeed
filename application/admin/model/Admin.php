<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model
{
    protected $pk = 'adm_id';

    public function role(){
        return $this->belongsTo('Roles','adm_role','role_id')->field('role_id,role_name');
    }
    protected $readonly = ['adm_create_time'];

    public function getAdmStatusAttr($value)
    {
        $status = [0=>'禁用',1=>'启用'];
        return $status[$value];
    }

    /**
     * @var array
     * 登录关联获取权限
     */
    public function roList(){
        return $this->belongsTo('Roles','adm_role','role_id')->bind('role_list');
    }


    protected $type = [
        'adm_last_login' => 'timestamp',
    ];
}