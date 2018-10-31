<?php
namespace app\api\controller;

use app\admin\model\AssetsInfos;
use app\admin\model\Mining;
use app\admin\model\System;
use app\admin\model\Users;
use think\Controller;
use think\Db;
use think\facade\Cache;
use think\facade\Debug;

class Crond extends Base
{

    /**
     * 测试获取签名
     */
    public function csSign(){
        $signData['time'] = $this->request->post('time');
        $signId = $this->request->post('id');

        if(!$signData['time']){
            exit(json_encode(array('code'=>4001,'msg'=>'非法请求,参数无效')));
        }

        if($signData['time'] < ( time()-3600 )) {
            exit(json_encode(array('code'=>4001,'msg'=>'非法请求')));
        }

        if($signId){
            $signData['id'] = $signId;
        }

        return $this->output('200','ok',$this->MakeSign($signData));
    }

    /**
     * @param $code
     * @param string $msg
     * @param array $data
     * @return \think\response\Json
     */
    public function output($code,$msg='ok',$data=array()){
        $result = array(
            'code'=>$code,
            'msg' => $msg,
            'data' => $data
        );

        return json($result);
    }

/**
     * 算力排行
     */
    public function power(){
        /*
         * 排行
         */
        $list = Db::name('users')->order('usr_computing_power','desc')->column('usr_id');
        $user = Db::name('users')->order('usr_computing_power','desc')->field('usr_computing_power,usr_nickname')->limit(99)->select();
        foreach($user as $k=>&$v){
            $v['usr_sort'] = $k + 1;
        }

        Cache::set('sort_user',$user);
        Cache::set('sort_list',$list);
    }

    /**
     * 同步算力、人员 数据
     * 每周一次
     * @return \think\response\Json
     */
    public function sycnDate(){
        $userCount = Users::count();
        $powerAll = Users::sum('usr_computing_power');
        System::where('sys_id','sys')->update(['sys_computing_power'=>$powerAll, 'sys_user_num'=>$userCount]);
    }


    public function reissue(){
        Debug::remark('begin');
       $sql = "SELECT usr_id,usr_computing_power from hw_users where usr_id not in (SELECT ai_user_id FROM `hw_assets_infos` where from_unixtime(ai_create_time) > now() GROUP by ai_user_id)";
        $r = Db::query($sql);
        $user = [];
//        dump($r);die;
        foreach($r as $k=>$v)
        {
            $user[$v['usr_id']] = $v['usr_computing_power'];
        }

        $sys = System::where('sys_id','sys')->find();

        //获取总算力  //服务总算力
        $sysPower = $sys['sys_computing_power'];

        //获取普通等级矿产
        $mining = Mining::where('min_level',0)->column('min_token_id,min_id,min_number,min_frequency');

        //获取服务器上算力》0的所有用户  数组键为用户ID
//        $user = Users::where('usr_computing_power > 0')->where('usr_id < 50000')->column('usr_id,usr_computing_power');
        //遍历矿产 矿产键为代币ID
        foreach($mining as $tokId => $v) {

//            总算力为0 跳出循环
            if($sysPower == 0){
                break;
            }
            //没算力千分之代币  每一算力获得的代币 千分比
            $onePowerToken = ($v['min_number'] * 100000 * 1000)/ $sysPower;

            //矿产频率
            $min_frequency = $v['min_frequency'];
            $time_frequency = (24 / $min_frequency) * 60 * 60;
            //遍历用户
            foreach($user as $userId => $vo){
//                用户一天获得的总算力
                $userDayPower = ($vo * $onePowerToken) / 1000;

                //调用方法       分配每份矿产一天总算力       频率           用户id   tokenid   间隔时间秒
                $data =  $this->numberAvg($userDayPower,$min_frequency,$userId,$tokId,$time_frequency);
                $assets = new AssetsInfos();
                $result = $assets->saveAll($data, false);

                unset($data);
                unset($data);
            }

        }
        Debug::remark('end');
        echo Debug::getRangeTime('begin','end').'s';
    }


    /**
     * 每日晚11 点
     * 为用户生成矿产
     * sys_computing_power 总算力
     * sys_user_num  总人数
     * sys_sort_power 临时算力
     */
    public function mineralCreate(){
        ini_set('memory_limit','3072M');    // 临时设置最大内存占用为3G
        set_time_limit(0);

        Debug::remark('begin');
        // ...其他代码段

        $sys = System::where('sys_id','sys')->find();

        //获取总算力  //服务总算力
        $sysPower = $sys['sys_computing_power'];

        //获取普通等级矿产
        $mining = Mining::where('min_level',0)->column('min_token_id,min_id,min_number,min_frequency');

        //获取服务器上算力》0的所有用户  数组键为用户ID
        $user = Users::where('usr_computing_power > 0')->where('usr_id > 0')->column('usr_id,usr_computing_power');
        //遍历矿产 矿产键为代币ID
        foreach($mining as $tokId => $v) {

//            总算力为0 跳出循环
            if($sysPower == 0){
                break;
            }
            //没算力千分之代币  每一算力获得的代币 千分比
            $onePowerToken = ($v['min_number'] * 100000 * 1000)/ $sysPower;

            //矿产频率
            $min_frequency = $v['min_frequency'];
            $time_frequency = (24 / $min_frequency) * 60 * 60;
            //遍历用户
            foreach($user as $userId => $vo){
//                用户一天获得的总算力
                $userDayPower = ($vo * $onePowerToken) / 1000;

                //调用方法       分配每份矿产一天总算力       频率           用户id   tokenid   间隔时间秒
                $data =  $this->numberAvg($userDayPower,$min_frequency,$userId,$tokId,$time_frequency);
                $assets = new AssetsInfos();
                $result = $assets->saveAll($data, false);

                unset($data);
                unset($data);
            }

        }
        Debug::remark('end');
        echo Debug::getRangeTime('begin','end').'s';
    }


    /**
     * 分配
     * @param int $number
     * @param int $avgNumber
     * @return array
     */
    public function numberAvg($number, $avgNumber,$userId,$tokId,$time_frequency)
    {
        ini_set('memory_limit','3072M');    // 临时设置最大内存占用为3G
        set_time_limit(0);
        if($number == 0) {
            $array = array_fill(0, $avgNumber, 0);
        } else {
            $avg     = floor($number / $avgNumber);

            $ceilSum = $avg * $avgNumber;
            $array   = array();
            //初始时间  当前时间戳
            $create_time = time() + 1200;
            for($i = 0; $i < $avgNumber; $i++) {
                $data['ai_token_id'] = $tokId;
                $data['ai_user_id'] = $userId;
                $data['ai_content'] = "挖矿收入";
                $data['ai_create_time'] = $create_time;
//                $data['ai_number'] = "挖矿收入";//矿产数量
                $data['ai_type'] = 1;//矿产类型 1挖矿
                $data['ai_status'] = -1;//矿产状态 未转化
                //矿产数量
                if($i < $number - $ceilSum) {
                    $data['ai_number'] = $avg +1;
//                    array_push($array, $avg + 1);
                } else {
                    $data['ai_number'] = $avg;
//                    array_push($array, $avg);
                }
//                AssetsInfos::create($data);
                array_push($array, $data);
                unset($data);
                //初始时间加间隔
                $create_time = $create_time + $time_frequency;
            }
        }
       return $array;
    }


    public function delMing()
    {
        dump(str_pad(4,6,0,STR_PAD_LEFT));die;
    }

}