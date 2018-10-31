<?php
namespace app\admin\model;

use think\Model;

class Tokens extends Model
{
    protected $pk = 'tok_id';

    protected function getTokImgAttr($value){
        return HTTP_URL.DIRECTORY_SEPARATOR.$value;
    }


}