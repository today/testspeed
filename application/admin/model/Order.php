<?php
namespace app\admin\model;

use think\Model;
use think\model\concern\SoftDelete;

class Order extends Model
{
    protected $table = 'hw_order';
}