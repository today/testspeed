<?php
namespace app\admin\model;

use think\Model;

class AdminLog extends Model
{
    protected $pk = 'log_id';

    protected $createTime = 'log_time';
    protected $autoWriteTimestamp = true;


}