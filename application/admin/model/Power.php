<?php
/*
 * @author 靳建飞 | e-mail:244029746@qq.com
 * @copyright 靳建飞 2018.3.12 星期一
 * 用户等级模型
 */
namespace app\admin\model;

use think\Model;

class Power extends Model
{
    protected $pk = 'pow_id';

    protected $createTime = 'pow_create_time';
    protected $autoWriteTimestamp = true;
//    echo $insert;

}