<?php
namespace app\admin\validate;

use think\validate;

class Link extends Validate
{
    protected $rule = [
        'fl_title' => 'require|alphaDash|max:40|token',
        'fl_url' => 'activeUrl',
        'fl_sort' => '',
    ];

    protected $message = [
        'ad_username.require'=>'用户名不能为空',
        'ad_username.alphaDash'=>'用户名不支持特殊字符',
        'ad_username.length'=>'长度不符合要求',
        'ad_username.unique'=>'用户名已存在',
        'ad_email.require'=>'邮箱不能为空',
        'ad_email.email'=>'邮箱格式不正确',
        'ad_phone.require'=>'手机号码不能为空',
        'ad_phone.mobile'=>'手机号码格式不正确',
    ];

    protected $scene = [
        'edit' => ['adm_email','adm_phone']
    ];
}