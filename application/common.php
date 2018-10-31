<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use Aliyun\SignatureHelper;
use think\facade\Cache;
use think\Db;

// 应用公共文件
//加密
function encrypt($str){
    return password_hash($str,PASSWORD_DEFAULT);
}

/**
 * 获取缓存或者更新缓存
 * @param string $config_key 缓存文件名称
 * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
 * @return array or string or bool
 */
function tpCache($config_key,$data = array()){
    $param = explode('.', $config_key);
    if(empty($data)){
        $config = cache($param[0]);//直接获取缓存文件
        if(!$config){
            //缓存文件不存在就读取数据库
            $res = Db::name('configs')->where("c_type",$param[0])->select();
            if($res){
                foreach($res as $k=>$val){
                    $config[$val['c_key']] = $val['c_value'];
                }
                cache($param[0],$config,3600);
            }
        }

        return $config;

    }else{
//        更新缓存
        $result =  Db::name('configs')->where("c_type", $param[0])->select();
        if($result){
            foreach($result as $val){
                $temp[$val['c_key']] = $val['c_value'];
            }
            foreach ($data as $k=>$v){
                $newArr = array('c_key'=>$k,'c_value'=>trim($v),'c_type'=>$param[0]);
                if(!isset($temp[$k])){
                    Db::name('configs')->insert($newArr);//新key数据插入数据库
                }else{
                    if($v!=$temp[$k])
                        Db::name('configs')->where("c_key", $k)->update($newArr);//缓存key存在且值有变更新此项
                }
            }
            //更新后的数据库记录
            $newRes = Db::name('configs')->where("c_type", $param[0])->select();
            foreach ($newRes as $rs){
                $newData[$rs['c_key']] = $rs['c_value'];
            }
        }else{
            foreach($data as $k=>$v){
                $newArr[] = array('c_key'=>$k,'c_value'=>trim($v),'c_type'=>$param[0]);
            }
            Db::name('configs')->insertAll($newArr);
            $newData = $data;
        }
        (cache($param[0],$newData,3600));
    }
}

function make_coupon_card($c) {
    $code = 'ABCDEFGHJKLMNOPQRSTUVWXYZ';
    $rand = $code[rand(0,25)]
        .strtoupper(dechex(date('m')))
        .date('d').substr(time(),-5)
        .substr(microtime(),2,5)
        .sprintf('%02d',rand(0,99));
    for(
        $a = md5( $rand, true ),
        $s = '123456789ABCDEFGHJKLMNOPQRSTUV',
        $d = '',
        $f = 0;
        $f < $c;
        $g = ord( $a[ $f ] ),
        $d .= $s[ ( $g ^ ord( $a[ $f + $c ] ) ) - $g & 0x1F ],
        $f++
    );
    return $d;
}

/**
 * @param $str
 * @param $len
 * @return string
 * 文章截取
 */
function utf_substr($str,$len)
{
    for($i=0;$i<$len;$i++)
    {
        $temp_str=substr($str,0,1);
        if(ord($temp_str) > 127)
        {
            $i++;
            if($i<$len)
            {
                $new_str[]=substr($str,0,3);
                $str=substr($str,3);
            }
        }
        else
        {
            $new_str[]=substr($str,0,1);
            $str=substr($str,1);
        }
    }
    return join($new_str).'....';
}


/**
 * 生成签名
 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
 */
function MakeSign()
{
    //签名步骤一：按字典序排序参数
    ksort($this->values);
    $string = $this->ToUrlParams();
    //签名步骤二：在string后加入KEY
    $string = $string . "&key=".WxPayConfig::KEY;
    //签名步骤三：MD5加密
    $string = md5($string);
    //签名步骤四：所有字符转为大写
    $result = strtoupper($string);
    return $result;
}

/**
 * 格式化参数格式化成url参数
 */
function ToUrlParams()
{
    $buff = "";
    foreach ($this->values as $k => $v)
    {
        if($k != "sign" && $v != "" && !is_array($v)){
            $buff .= $k . "=" . $v . "&";
        }
    }

    $buff = trim($buff, "&");
    return $buff;
}

/**
 * 短信接口方法
 * @return mixed
 */
function sendSms($appid,$secret,$phone,$signName,$templateCode,$code,$commont='') {

    $params = array ();

    // *** 需用户填写部分 ***

    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = $appid;//"your access key id";
    $accessKeySecret = $secret;//"your access key secret";

    // fixme 必填: 短信接收号码
    $params["PhoneNumbers"] = $phone;//"17000000000";

    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = $signName;//"短信签名";

    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    $params["TemplateCode"] = $templateCode;//"SMS_0000001";

    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
    $params['TemplateParam'] = Array (
        "code" => $code,//"12345",
//        "product" => "阿里通信"
    );

    // fixme 可选: 设置发送短信流水号
    $params['OutId'] = "12345";

    // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
    $params['SmsUpExtendCode'] = "1234567";


    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }

    // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
    $helper = new SignatureHelper();

    // 此处可能会抛出异常，注意catch
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        ))
    );

    return $content;
}


/**
 * 身份认证方法
 */
function name_post($host,$path,$bodys){
    $method = "POST";
    $appcode = "ef764e9c5d7a4af1aff6456b5f343dca";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    //根据API的要求，定义相对应的Content-Type
    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
    $querys = "";
    $url = $host . $path;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
    $result = curl_exec($curl);
    return $result;
}

function http_get($url){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}

//json only
function http_post($url,$data){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    //$header = array("Content-Type: application/json; encoding=utf-8");
    curl_setopt($oCurl, CURLOPT_URL, $url);//设置链接
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
    curl_setopt($oCurl, CURLOPT_POST, 1);//设置为POST方式
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);//POST数据
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    dump($sContent);die;
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}


/**
 * @param $num         科学计数法字符串  如 2.1E-5
 * @param int $double 小数点保留位数 默认5位
 * @return string
 */

function sctonum($num, $double = 5){
    if(false !== stripos($num, "e")){
        $a = explode("e",strtolower($num));
        return bcmul($a[0], bcpow(10, $a[1], $double), $double);
    }
}
