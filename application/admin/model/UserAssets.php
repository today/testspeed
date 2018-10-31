<?php
namespace app\admin\model;

use think\Model;

class UserAssets extends Model
{
    protected $pk = 'ua_id';

//    protected $type = [
//        'ua_create_time'=>'datetime'
//    ];

    protected $createTime = 'ua_create_time';
//    protected $updateTime = 'ua_handling_time';

    protected $autoWriteTimestamp = true;

    /**
     * 剩余资产处理
     * @param $value
     * @return float
     */
    public function getUaSurplusNumberAttr($value){
        return round($value/100000,6);
    }

    /**
     * 对收入资产进行处理
     */
    public function getUaNumberAttr($value){
        return round($value/100000,6);
    }

    /**
     * @return $this
     * 关联token表
     */
    public function token(){
        return $this->belongsTo('Tokens','ua_token_id','tok_id')->bind('tok_id,tok_name,tok_img');
    }

    /**
     * @return $this
     * 关联token表  获取token 信息
     */
    public function tokeninfo(){
        return $this->belongsTo('Tokens','ua_token_id','tok_id')->bind('tok_id,tok_name,tok_img,tok_content,tok_url,tok_book_url');
    }


}