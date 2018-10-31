<?php
namespace app\admin\validate;

use think\validate;

class Admin extends Validate
{
    protected $rule = [
        'adm_username' => 'require|alphaDash|length:5,20|unique:admin|token',
        'adm_phone' => 'require|mobile',
    ];

    protected $message = [
        'ad_username.require'=>'用户名不能为空',
        'ad_username.alphaDash'=>'用户名不支持特殊字符',
        'ad_username.length'=>'长度不符合要求',
        'ad_username.unique'=>'用户名已存在',
        'ad_phone.require'=>'手机号码不能为空',
        'ad_phone.mobile'=>'手机号码格式不正确',
    ];

    protected $scene = [
        'edit' => ['adm_phone']
    ];
}