<?php
namespace app\admin\model;

use think\Model;

class Mining extends Model
{
    protected $pk = 'min_id';

    protected $readonly = ['min_create_time'];

    protected $autoWriteTimestamp = true;

    protected $createTime = 'min_create_time';

    protected $type = [
        'min_create_time' => 'timestamp',
    ];

    public function listtoken(){
        return $this->belongsTo('Tokens','min_token_id','tok_id')->bind('tok_id,tok_name,tok_img');
    }
}