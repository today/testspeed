<?php
/*
 * @author 靳建飞 | e-mail:244029746@qq.com
 * @copyright 靳建飞 2018.3.9 星期5
 * 代币管理：页面；列表；
 */
namespace app\admin\controller;

use app\admin\model\Banner;
use app\admin\model\Configs;
use app\admin\model\Inform;
use app\admin\model\SystemMenu;
use app\admin\model\Tokens;
use app\admin\model\Users;
use app\admin\model\Version;
use think\Db;
use think\facade\Env;
use think\facade\Session;
use think\Request;

class System extends Base
{


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        /*配置列表*/
        $group_list = getConfigMenu();

        $c_type = $this->request->param('c_type','website_info'); //配置参数

        $this->assign('c_type',$c_type);
//        $res = Configs::where('c_type',$c_type)->select(); //获取数据库上的内容
        $config = tpCache($c_type);
//        $config=$res;

        //对数据库上内容进行转换
//        foreach($res as $v){
//            $config[$v['c_key']] = $v['c_value'];
//        }

        if($c_type == 'hotc_set' || $c_type== 'friend_set'){
            $token = Tokens::where('tok_id',1)->column('tok_id,tok_name');
            $this->assign('token',$token);
        }

//        dump($config);die;
        $this->assign('group_list',$group_list);
        $this->assign('config',$config);//当前配置项
        $this->assign(array('title'=>'系统参数设置','navigation'=>'system_index'));
        //C('TOKEN_ON',false);
        return $this->fetch($c_type);
    }

    /**
     * 网站信息
     */
    public function website_info(){
        if($this->request->method() == "POST") {//post request
            /*配置列表*/
            $group_list = getConfigMenu();

            $param = $this->request->except('__token__');
            $c_type = $param['c_type'];
            unset($param['c_type']);

            $config = tpCache($c_type,$param);//调用函数写入跟新数据

            $this->assign('group_list',$group_list);
            $this->assign('c_type',$c_type);
            $this->assign('config',$config);//当前配置项
            $this->assign(array('title'=>'系统参数设置','navigation'=>'system_index'));

//            return $this->fetch($c_type);die;
        }

        $this->redirect('index',['c_type'=>$c_type]);
    }

    /**
     * 短息设置
     */
    public function alisms(){
        if($this->request->method() == "POST") {//post request
            /*配置列表*/
            $group_list = getConfigMenu();

            $param = $this->request->except('__token__');
            $c_type = $param['c_type'];
            unset($param['c_type']);
            $config = tpCache($c_type,$param);//调用函数写入跟新数据

            $this->assign('group_list',$group_list);
            $this->assign('c_type',$c_type);
            $this->assign('config',$config);//当前配置项
            $this->assign(array('title'=>'系统参数设置','navigation'=>'system_index'));
//            return $this->fetch($c_type);die;
        }

        $this->redirect('index',['c_type'=>$c_type]);
    }


    /*
     *挖矿基本设置
     */
    public function hotc_set(){
        if($this->request->method() == "POST") {//post request
            /*配置列表*/
            $group_list = getConfigMenu();

            $param = $this->request->except('__token__');
            $c_type = $param['c_type'];
            unset($param['c_type']);
            $config = tpCache($c_type,$param);//调用函数写入跟新数据
            $token = Tokens::column('tok_id,tok_name');
            $this->assign('token',$token);

            $this->assign('group_list',$group_list);
            $this->assign('c_type',$c_type);
            $this->assign('config',$config);//当前配置项
            $this->assign(array('title'=>'系统参数设置','navigation'=>'system_index'));

//            return $this->fetch($c_type);die;
        }

        $this->redirect('index',['c_type'=>$c_type]);
    }

    /**
     *邀请规则设定
     */
    public function friend_set(){
        if($this->request->method() == "POST") {//post request
            /*配置列表*/
            $group_list = getConfigMenu();

            $param = $this->request->except('__token__');
            $c_type = $param['c_type'];
            unset($param['c_type']);
            $config = tpCache($c_type,$param);//调用函数写入跟新数据

            $token = Tokens::column('tok_id,tok_name');
            $this->assign('token',$token);

            $this->assign('group_list',$group_list);
            $this->assign('c_type',$c_type);
            $this->assign('config',$config);//当前配置项
            $this->assign(array('title'=>'系统参数设置','navigation'=>'system_index'));

//            return $this->fetch($c_type);die;
        }

        $this->redirect('index',['c_type'=>$c_type]);
    }

    /**
     *酒水秘籍设定
     */
    public function drink_desc(){
        if($this->request->method() == "POST") {//post request
            /*配置列表*/
            $group_list = getConfigMenu();

            $param = $this->request->except('__token__');
            $c_type = $param['c_type'];
            unset($param['c_type']);
            $config = tpCache($c_type,$param);//调用函数写入跟新数据

            $token = Tokens::column('tok_id,tok_name');
            $this->assign('token',$token);

            $this->assign('group_list',$group_list);
            $this->assign('c_type',$c_type);
            $this->assign('config',$config);//当前配置项
            $this->assign(array('title'=>'酒水秘籍设定','navigation'=>'system_index'));

//            return $this->fetch($c_type);die;
        }

        $this->redirect('index',['c_type'=>$c_type]);
    }


   /**
    * 发布通知列表
    */
   public function inform(){
       $inform = Inform::order('if_create_time','desc')->paginate(10,false,[
           'type'     => 'bootstrap',
       ]);
       $page = $inform->render();
       $this->assign('navigation','system_inform');
       $this->assign('page',$page);
       $this->assign('list',$inform);
       $this->assign('title','发布通知列表');
       return $this->fetch();
   }

   /**
    * 发布通知
    */
   public function add_inform(){
       if($this->request->method() == 'POST'){
           $content = $this->request->post('content');
           $data['if_content'] = $content;
           $data['if_person'] = Session::get('adm_username','admin');
           $result = Inform::create($data);
           if($result){
               return $this->success('通知发布成功!','inform','',1);
           }else{
               return $this->error('通知发布失败!','','',1);
           }
       }
       $this->assign(['navigation'=>'system_inform','title'=>'发布通知']);
       return $this->fetch();
   }

   /**
    * 修改当前通知状态
    */
   public function update_inform(){
       if($this->request->method() == "POST"){
           $id = $this->request->post('id');
           $status = $this->request->post('status');
           $inform = Inform::get($id);
           $inform->if_status = $status;
           $result = $inform->save();
           if($result){
               return $this->success('状态修改成功!');
           }else{
               return $this->error('状态修改失败!');
           }
       }
   }

   /**
    * 短信验证
    */
   public function smsphone(){
       $phone = $this->request->get('phone');
       $num = mt_rand(0000,9999);
       $result = Configs::where('c_type','alisms')->column('c_value','c_key');
       $output = sendSms($result['sms_appkey'],$result['sms_secret'],$phone,$result['sms_sign'],$result['sms_code'],$num);
       dump($output->Code);
   }


    /**
     * 身份验证测试
     */
    public function identity(){
        if($this->request->method() == "POST") {//post request
            $name = $this->request->post('name');
            $number = $this->request->post('number');
            $host = "https://1.api.apistore.cn";
            $path = "/idcard3";
            $bodys = "cardNo=".$number."&realName=".$name;

            $result = name_post($host,$path,$bodys);
            return $this->success($result);
        }

        $this->redirect('index');
    }

    /**
     * 清除缓存
     */
    public function allClear(){
        $r = Env::get('runtime_path'). 'temp';
        if(is_dir($r)){
            if($dh = opendir($r)){
                while(($file = readdir($dh)) != false){
                    if($file != '.' && $file != '..') {
                        unlink($r.'\\'.$file);
                    }
                }

                closedir($dh);
            }
        }
    }

    /**
     * 清除临时算力
     */
    public function clearPower(){
        $user = Session::get('adm_username','admin');

        if($user !== 'admin') {
            return $this->error('对不起您无权清除临时算力!');
        }

        Db::startTrans();
        try {
            Db::name('users')->where('usr_id > 0')->update(array('usr_sort_power'=>0));
            Db::name('system')->where('sys_key','sys_sort_power')->update(array('sys_value'=>0));
            Db::commit();
            $status = true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();

            $status = false;
        }

        if($status) {
            return $this->success('算力清除成功');
        }

        return $this->error('算力清除失败');

    }

    /**
     * 发布最新版本
     */
    public function version() {
        $version = Version::where('vs_id > 0')->order('vs_create_time','desc')->paginate(10,false,[
            'type'     => 'bootstrap',
        ]);

        $page = $version->render();

        $this->assign(['page'=>$page , 'title'=>'更新版本', 'list'=>$version , 'navigation'=>'system_version']);
        return $this->fetch();
    }

    /**
     * 发布最新版本
     */
    public function add_version() {
        if($this->request->method() === "POST") {
            $data = $this->request->post();
            $result = Version::create($data);
            if($result) {
                return $this->success('最新版本发布成功',Url('system/version'),'',1);
            }
            return $this->error('版本发布失败,请重新发布!','','',1);
        }
        $this->assign(['title'=>'发布新版本','navigation'=>'system_version']);
        return $this->fetch();
    }

    public function banner(){
        $banner = Banner::where('is_show',0)->order('asort asc')->select();
        $this->assign('list',$banner);
        return $this->fetch();
    }

    public function bannerSave(){
        if($this->request->method() === "POST"){
            $banner = $this->request->post();
            $file = $this->request->file('pic');
            if($file){
                $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/banner/');
                if(!$fileName){
                    return $this->error($file->getError(),'','',1);
                }
                $banner['pic'] = stripslashes($fileName->getPathName());
            }else{
                $this->error('请上传图片','','',1);
            }
            $output = new Banner();
            $result = $output->save($banner);

            if($result) {
                $this->success('添加成功','banner','',1);
            }else{
                unlink($banner['pic']);
                $this->error('添加失败','','',1);
            }
        }
        $id = $this->request->param('id');
        $vo = Banner::get($id);
        $this->assign('list',$vo);
        return $this->fetch();
    }

    public function bannerUpdate(){
        if($this->request->method() === "POST"){
            $banner = $this->request->post();
            $output = Banner::get($banner['id']);
            $img_url = $output->getData('pic');
            $file = $this->request->file('pic');

            if($file){
                $fileName = $file->validate(['ext'=>'png,jpg,jpeg,gif'])->rule('uniqid')->move('upload/banner/');
                if(!$fileName){
                    return $this->error($file->getError(),'','',1);
                }
                $banner['pic'] = stripslashes($fileName->getPathName());
            }

            $result = $output->save($banner);

            if($result) {
                if(isset($banner['pic']) && is_file($img_url)){
                    unlink($img_url);
                }
                $this->success('更新成功','banner','',1);
            }else{
                $this->error('更新失败','','',1);
            }
        }
    }
}
