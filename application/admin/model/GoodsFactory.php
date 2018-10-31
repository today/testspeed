<?php
namespace app\admin\model;

use think\Model;
use think\model\concern\SoftDelete;

class GoodsFactory extends Model
{
    use SoftDelete;
    protected $table = 'hw_goods_factory';
    protected $deleteTime = 'delete_time';
}