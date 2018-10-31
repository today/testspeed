<?php
namespace app\admin\model;

use think\Model;
use think\model\concern\SoftDelete;

class Goods extends Model
{
    use SoftDelete;
    protected $table = 'hw_goods';
    protected $deleteTime = 'delete_time';
}