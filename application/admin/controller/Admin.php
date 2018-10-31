<?php
/*
 * @author 靳建飞 | e-mail:244029746@qq.com
 * @copyright 靳建飞 2018.3.9 星期5
 * 代币管理：页面；列表；
 */
namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use app\admin\model\AdminLog;
use app\admin\model\Roles;
use app\admin\model\SystemMenu;
use think\captcha\Captcha;
use think\Db;
use think\facade\Session;

class Admin extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 登录login
     */
    public function login()
    {
        if($this->request->method() === "POST"){
            $username = $this->request->post("username",'','strip_tags,trim');//过滤用户名
            $password = $this->request->post("password",'','strip_tags,trim');//过滤密码
            $captcha = $this->request->post("captcha",'','strip_tags,trim');//过滤验证码
            $vertify = new Captcha();

            if( !$vertify->check($captcha))
            {
                return $this->error('验证码错误');
            }

            if($username==''){
                return $this->error('用户名不能为空！');
            }
            if($password == ''){
                return $this->error('密码不能为空！');
            }
            //获取用户信息
            $user = AdminModel::with('roList')->where('adm_username',$username)->find();

            if(!$user){
                return $this->error('当前账号不存在！');
            }
            if(!password_verify($password,$user['adm_password'])){
                return $this->error('密码输入错误！');
            }

//            dump($user);die;

            Session::set('adm_id',$user['adm_id'],'admin');
            Session::set('adm_username',$user['adm_username'],'admin');
            Session::set('last_login',$user['adm_last_login'],'admin');
            Session::set('last_ip',$user['adm_last_ip'],'admin');
            //更新数据
            AdminModel::where('adm_id',$user['adm_id'])->update(['adm_last_login'=>time(),'adm_last_ip'=>$this->request->ip()]);
            Session::set('role_list',$user['role_list'],'admin');

            $this->create_log('后台登录',$this->request->url());//管理日志

            return $this->success('登录成功！',url('index/index'));

        }

        $this->assign('titles','币世界后台登录');
        return $this->fetch('/login');
    }

    /**
     * 验证码
     * @return mixed
     */
    public function captcha(){
        $config = [
            'fontSize'=>40,
            'length'=>4,
            'useCurve'=>false
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

    /**
     * 推出登录
     */
    public function logout(){
        Session::clear('admin');
        return $this->success("退出成功",url('Admin/login'),'',2);
    }

    /**
     * 修改密码
     */
    public function pwd_save(){
        if($this->request->method() === "POST"){
            $id = Session::get('adm_id','admin');
            $adm_password = $this->request->post('adm_password');
            $password = $this->request->post('password');
            $repassword = $this->request->post('repassword');

            if($password !== $repassword){
                //两次密码不一致
                return  $this->error('两次密码不一致请从新输入!','','','1');
            }
            $adm = AdminModel::get($id);
            if(!password_verify($adm_password,$adm['adm_password'])){
                //原始密码错误
                return  $this->error('原密码输入错误!','','','1');
            }
            $output = AdminModel::where('adm_id',$id)->update(['adm_password'=>encrypt($password)]);
            if(!$output){
                //修改失败
                return  $this->error('密码修改失败,请从新操作!','','','1');
            }
            Session::clear('admin');
            return $this->success('密码修改成功,请使用新密码登录!','login');
        }
        $this->assign('title','修改管理密码');
        $this->assign('manage',null);
        return $this->fetch();
    }

    /**
     * 管理员列表
     * @return mixed
     */
    public function index()
    {
        $admins =AdminModel::with('role')->select();
        $this->assign('list',$admins);
        $this->assign('navigation','admin_index');
        $this->assign('title','管理员列表');
        return $this->fetch();
    }


    /**
     *添加修改管理员
     */
    public function manage_save($id=0){
        if($this->request->method()==="POST"){
            $user = $this->request->post();
            if(!$user['adm_id']){
                $validate = $this->validate($user,'Admin');//验证器
                if(true!==$validate){//通过验证
                    $this->error($validate);
                }
                $user['adm_password'] = encrypt('000000');
                $user['adm_create_time'] = time();
                $output = AdminModel::create($user);//添加管理员
                if($output){
                   return $this->success('管理员：'.$user['adm_username'].'添加成功，密码默认000000','admin/admin/index');//成功
                }
                $this->error('管理员添加失败');//失败
            }

            $validate = $this->validate($user,'app\admin\validate\Admin.edit');//更新验证

            if(true!==$validate){
                return $this->error($validate);
            }
            $output = AdminModel::update($user);
            if($output){
                return $this->success('修改成功','admin/admin/index');
            }else{
                return $this->error('管理员修改失败');//失败
            }
        }

        $manage = AdminModel::get($id);
        $role = Roles::where('role_list','neq','all')->select();
        $this->assign('navigation','admin_index');
//        $manage = null;
        $this->assign('roles',$role);
        $this->assign(array('manage'=>$manage,'title'=>'添加管理员'));
        return $this->fetch();
    }

    /**
     * 删除管理员
     * @return mixed
     */
    public function manage_delete(){
        $id = $this->request->post('id');
        $output = AdminsModel::destroy($id);
        if($output){
           return  $this->success('删除成功');
        }
        return $this->error('删除失败');
    }

    //ajax 实时验证管理员是否存在
    public function ajax_by_manage(){
        $param = $this->request->post('param');
        $name = $this->request->post('name');
        $adm = AdminModel::where($name,$param)->find();
        if($adm!==null){
            $data['status']='n';
            $data['info'] ='已存在';
        }else{
            $data['status']='y';
            $data['info'] ='可使用';
        }
        return $data;
    }

    /**
     * @return mixed
     */
    public function roles(){
        $list = Roles::where('role_id!=1')->select();
        $this->assign('list',$list);
        $this->assign(array('navigation'=>'admin_roles','title'=>'角色管理'));
        return $this->fetch();
    }

    //保存角色
    public function role_create(){
        if($this->request->method()==='POST'){
            if($id=$this->request->post('id')){
                $role = Roles::get($id);
                $msg = '修改';
            }else{
                $role = new Roles;
                $msg = '新增';
            }
            $role->role_name = $this->request->post('role_name');
            $role->role_desc = $this->request->post('role_desc');
            $role->role_list = implode(',',$this->request->post('right/a'));
            $quest = $role->save();
            if($quest){
                $this->success($msg.'操作成功',url('admin/admin/roles'),'',1);
            }
            $this->success($msg.'操作失败');
        }
        $id = $this->request->param('id',0);
        $role = Roles::get($id); //查找角色信息，获取权限
        $detail = explode(',',$role['role_list']); //角色权限数组

        $right = SystemMenu::all();//所有权限资源
        $modules = array();

        foreach($right as $v){
            $v['enable'] = in_array($v['sm_id'],$detail);//根据该字段判断是否已选择
            $modules[$v['sm_group']][] = $v;
        }

        $group = getRoleMenu();//资源分组导航
        $this->assign('modules',$modules);
        $this->assign('list',$role);
        $this->assign('group',$group);
        $this->assign(array('navigation'=>'admin_role','title'=>'添加角色'));
        return $this->fetch();
    }

    //删除角色
    public function role_delete(){
        $id = $this->request->post('id');
        $output = Roles::destroy($id);
        if($output){
            $this->success('角色删除成功');
        }else{
            $this->error('角色删除失败');
        }
    }


    /**
     * 管理员登录日志
     */
    public function log(){
        $this->assign(array('navigation'=>'admin_log','title'=>'管理操作日志'));
        $this->assign('list',array());

        $adminlog = AdminLog::order('log_time','desc')->paginate(10,false,[
            'type'     => 'bootstrap',
        ]);
        $page = $adminlog->render();
        $this->assign('page',$page);
        $this->assign('list',$adminlog);
        return $this->fetch();
    }

    /**
     * 测试清空数据
     */
//    public function qingkong(){
//        Db::execute("truncate table hw_tokens");
//        Db::execute("insert into hw_tokens (tok_id,tok_name,tok_content,tok_img,tok_create_time) values('1','DRC','酒水币','sdf',1123123)");
//        Db::execute("truncate table hw_user_assets");
//        Db::execute("truncate table hw_assets_infos");
//        Db::execute("truncate table hw_users");
//        Db::execute("truncate table hw_power");
//        Db::execute("truncate table hw_admin_log");
//        Db::execute("truncate table hw_mining");
//        Db::execute("truncate table hw_invite_record");
//        Db::execute("delete from hw_system where sys_id ='sys'");
//        Db::execute("insert into hw_system (sys_id) values('sys')");
////        dump($t);
//    }

}
