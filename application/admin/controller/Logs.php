<?php
/*
 * @author 靳建飞 | e-mail:244029746@qq.com
 * @copyright 靳建飞 2018.4.11 星期三
 * 模板系统调试错误日志控制器；
 */
namespace app\admin\controller;

use think\facade\Env;


class Logs extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 错误日志显示
     */
    public function index(){
        $dir = Env::get('runtime_path') . 'log/';
        $handler = opendir($dir);
        $files = array();

        while (($filename = readdir($handler)) !== false) {//务必使用!==，防止目录下出现类似文件名“0”等情况
            if ($filename != "." && $filename != "..") {
                $handlers = opendir($dir.$filename);

                while ( ($filenames = readdir($handlers)) !== false ) {
                    if($filenames != "." && $filenames != "..") {
                        $files[$filename.$filenames] = array('name' => $filename,'file'=>$filenames,'address'=>$dir.$filename);
                    }
                }

            }
        }

        $files??krsort($files);
        $this->assign('title','系统调试日志');
        $this->assign('list',$files);
        return $this->fetch();

    }


    /**
     * 调试日志查看
     */
    public function showLog(){

        $address = input('get.address');
        $name = input('get.names');
        $file_path = $address.'/'.$name;

        if(file_exists($file_path)){
            $str = file_get_contents($file_path);//将整个文件内容读入到一个字符串中
            $str = str_replace("\r\n","<br />",$str);
        }

        return $this->success($address.$name,'',$str);
//        dump($address);
    }

    /**
     * 调试日志下载
     */
    public function dowLog(){

        $name = input('names');
        $file = input('file');
        $dir = Env::get('runtime_path') . 'log/';
        $filename=$dir.$file.'/'.$name;
        $basename=pathinfo($filename);

        header("Content-Type: txt/log"); //指定下载文件类型的
        header("Content-Disposition:attachment;filename=".$file.'-'.$basename["basename"]);
//指定下载文件的描述信息
        header("Content-Length:".filesize($filename));  //指定文件大小的
        readfile($filename);//将内容输出，以便下载。
    }

}
