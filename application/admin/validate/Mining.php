<?php
namespace app\admin\validate;

use think\validate;

class Mining extends Validate
{
    protected $rule = [
        'min_token_id' => 'require|number|unique:mining|token',
        'min_number' => 'require|number',
        'min_frequency' => 'require|number',
    ];

    protected $message = [
        'min_token_id.require'=>'token ID不能为空',
        'min_token_id.number'=>'token ID类型错误',
        'min_token_id.unique'=>'token 已添加',
        'min_number.require'=>'矿产数量不能为空',
        'min_number.number'=>'矿产数量只能是数字',
        'min_frequency.require'=>'出矿频率不能为空',
        'min_frequency.number'=>'出矿频率只能是数字',

    ];

    protected $scene = [
        'edit' => ['min_number,min_frequency']
    ];

}