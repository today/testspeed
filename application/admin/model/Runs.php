<?php
namespace app\admin\model;

use think\Model;

class Runs extends model
{
    protected $pk = 'run_id';

    protected $createTime = 'run_create_time';
    protected $autoWriteTimestamp = true;

}