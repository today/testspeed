<?php
namespace app\admin\model;

use think\Model;
use think\model\concern\SoftDelete;

class GoodsBrand extends Model
{
    use SoftDelete;
    protected $table = 'hw_goods_brand';
    protected $deleteTime = 'delete_time';
}