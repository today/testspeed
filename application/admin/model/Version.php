<?php
namespace app\admin\model;

use think\Model;

class Version extends Model
{
    protected $pk = 'vs_id';

    protected $createTime = 'vs_create_time';
    protected $autoWriteTimestamp = true;
}