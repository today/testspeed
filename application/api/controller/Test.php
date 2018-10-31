<?php
namespace app\api\controller;

use Pay\Pay;

class Test extends Base
{
    public function index(){
        $config = $this->PayConfig();
        $pay = new Pay($config);
        $options = [
            'out_trade_no'     => time(), // 订单号
            'total_fee'        => '1', // 订单金额，**单位：分**
            'body'             => '测试', // 订单描述
            'spbill_create_ip' => '127.0.0.1', // 支付人的 IP
            'notify_url'       => 'http://localhost/notify.php', // 定义通知URL
        ];
        try {
            $options = $pay->driver('alipay')->gateway('app')->apply($options);
            var_dump($options);
        } catch (Exception $e) {
            echo "创建订单失败，" . $e->getMessage();
        }

    }
}