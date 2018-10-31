<?php
namespace app\admin\model;

use think\Model;

class AssetsInfos extends Model
{
    protected $pk = 'ai_id';

    /**
     * 资产处理
     * @param $value
     * @return float
     */
    public function getAiNumberAttr($value)
    {
        return round($value/100000,5);
    }

    /**
     * 时间转换
     * @param $value
     * @return bool|string
     */
//    public function getAiConvTimeAttr($value)
//    {
//        return date("Y-m-d H:i:s",$value);
//    }
    /**
     * 时间转换
     * @param $value
     * @return bool|string
     */
    protected $type = [
        'ai_create_time' => 'timestamp',
        'ai_conv_time' => 'timestamp',
    ];

    /**
     * 关联token 表
     */
    public function token(){
        return $this->belongsTo('Tokens','ai_token_id','tok_id')->bind('tok_id,tok_name');
    }

    /**
     * 关联user表  后台显示
     */
    public function user(){
        return $this->belongsTo('Users','ai_user_id','usr_id')->bind('usr_nickname');
    }
}