<?php
/*
 * @author 靳建飞 | e-mail:244029746@qq.com
 * @copyright 靳建飞 2018.3.9 星期5
 * 代币管理：页面；列表；
 */
namespace app\admin\controller;
use app\admin\model\SystemMenu as SystemMenuModel;
use think\facade\Env;
use think\Request;

class SystemMenu extends Base
{


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $roles = SystemMenuModel::all();
        $this->assign(array('list'=>$roles,'rolemenu'=>getRoleMenu()));
        $this->assign(array('title'=>'网站权限资源管理','navigation'=>'systemmenu_index'));
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create_menu()
    {
        if('POST'===$this->request->method()){
            if($id = $this->request->post('id')){
                $menu = SystemMenuModel::get($id);
            }else{
                $menu = new SystemMenuModel();
            }

            $menu->sm_name = $this->request->post('rolename');
            $menu->sm_group = $this->request->post('rolegroup');
            $menu->sm_right = implode(',',$this->request->post('rights/a'));
            $output = $menu->save();
            if($output){
                $this->success('操作成功','index','',1);
            }
            $this->error('操作失败');
        }
        //资源添加界面
        $id = $this->request->param('id',0);
        $menus = SystemMenuModel::get($id);
//        dump($);die;
        if(0!==$id){
            $menus['right'] = explode(',',$menus['sm_right']);
        }
        //获取controller 目录
        $filePath = Env::get('module_path').'controller';
        $planList = array();
        $dirRes = opendir($filePath);
        while($dir = readdir($dirRes)){
            if(!in_array($dir,array('.','..','.svn','.git'))){
                $planList[] = basename($dir,'.php');
            }
        }
        $this->assign('menus',$menus);
        $this->assign('rolemenu',getRoleMenu());
        $this->assign(array('planList'=>$planList,'title'=>'添加网站权限','navigation'=>'systemmenu_index'));
        return $this->fetch();
    }

    /*
     * 删除权限资源
     */
    public function systemmenu_delete(){
        if($this->request->isAjax()){
            $id = $this->request->post('id');
            $output = SystemMenuModel::destroy($id);
            if($output){
                return $this->success('权限资源删除成功！');
            }else{
                return $this->error('删除操作执行失败！');
            }
        }
        return $this->error('非法操作');
    }
    function ajax_get_action()
    {
        if($this->request->isAjax()){
            $control = $this->request->post('controller');
            $advContrl = get_class_methods("app\\admin\\controller\\".str_replace('.php','',$control));
            $baseContrl = get_class_methods('app\admin\controller\Base');
            $diffArray  = array_diff($advContrl,$baseContrl);
            $html = '';
            foreach ($diffArray as $val){
                $html .= "<option value='".$val."'>".$val."</option>";
            }
            exit($html);
        }

    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }



}
