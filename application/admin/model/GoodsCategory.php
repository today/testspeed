<?php
namespace app\admin\model;

use think\Model;
use think\model\concern\SoftDelete;

class GoodsCategory extends Model
{
    use SoftDelete;
    protected $table = 'hw_goods_category';
    protected $deleteTime = 'delete_time';
}