<?php
/*
 * @author 靳建飞 | e-mail:244029746@qq.com
 * @copyright 靳建飞 2018.3.9 星期5
 * 代币管理：页面；列表；
 */
namespace app\admin\controller;

use app\admin\model\AssetsInfos;
use app\admin\model\Mining;
use app\admin\model\UserAssets;
use app\admin\model\Users;

class User extends Base
{
    /**
     * 用户列表
     */
    public function index(){
        $type = $this->request->param('type','usr_create_time');
        $type_asc = $type == 'usr_create_time_asc'?'asc':'desc';
        $data = $type == 'usr_create_time_asc'?'usr_create_time':$type;

        $search= $this->request->param('search');

       $users =  Users::whereor('usr_phone','like','%'.$search.'%')->whereor('usr_nickname','like','%'.$search.'%')
           ->whereor('usr_realname','like','%'.$search.'%')->order($data,$type_asc)->paginate(15,false,[
            'type'     => 'bootstrap',
                'path'     => url('user/index',['type'=>$type,'search'=>$search]),
        ]);
//        dump($users);
        $page = $users->render();

        $this->assign('search',$search);
        $this->assign('type',$type);
        $this->assign('navigation','user_index');
        $this->assign('page',$page);
        $this->assign('list',$users);
        $this->assign('title','用户列表');
        return $this->fetch();
    }


    /**
     * ajax 获取个人信息
     */
    public function get_by_user(){
        $id = $this->request->get('id');

        $user = Users::get($id);
        $assets = UserAssets::with('token')->where('ua_user_id',$id)->select();
//        dump($assets);die;
        $user['usr_is_identity'] = $user['usr_is_identity']==1?'是':'否';
        $user['usr_vip'] = $user['usr_vip']==1?'是':'否';
        $user['sex'] = $user['sex']==1?'男':'女';

        $info = '';
        foreach($assets as $v){
            $info .= '<tr>
                        <td><img src="'.$v['tok_img'].'" width="10px" alt="">'.$v["tok_name"].'</td>
                        <td>'.sctonum($v['ua_number'],5).'</td>
                        <td>'.sctonum($v['ua_surplus_number'],5).'</td>
                    </tr>';
        }
        $token = '';
        $html = ' <div class="full-height-scroll">
                        <div class="table-responsive">
                            <h2>用户【'.$user['usr_nickname'].'】详细资料</h2>
                            <table class="table table-striped table-hover">
                                <tbody>
                                <tr>
                                    <td>用户昵称</td>
                                    <td>'.$user['usr_nickname'].'</td>
                                    <td>用户电话</td>
                                    <td>'.$user['usr_phone'].'</td>
                                </tr>
                                <tr>
                                    <td>用户算力</td>
                                    <td>'.$user['usr_computing_power'].'</td>
                                    <td>临时算力</td>
                                    <td>'.$user['usr_sort_power'].'</td>  
                                </tr>
                                 <tr>
                                    <td>邀请码</td>
                                    <td>'.$user['usr_code'].'</td>
                                    <td>邀请人数</td>
                                    <td>'.$user['usr_invite_number'].'</td>
                                </tr>
                                <tr>
                                    <td>实名认证</td>
                                    <td>'.$user['usr_is_identity'].'</td>
                                    <td>认证时间</td>
                                    <td>'.date('Y-m-d H:i:s',$user['usr_identity_time']).'</td> 
                                </tr>
                                <tr>
                                    <td>创建时间</td>
                                    <td>'.$user['usr_create_time'].'</td>
                                    <td>登录时间</td>
                                    <td>'.$user['usr_last_time'].'</td>
                                </tr>
                                <tr>
                                    <td>姓名</td>
                                    <td>'.$user['usr_realname'].'</td>
                                    <td>身份证号</td>
                                    <td>'.$user['usr_invite_number'].'</td>
                                </tr>
                                <tr>
                                    <td>是否是VIP</td>
                                    <td>'. $user['usr_vip'] .'</td>
                                    <td>区号</td>
                                    <td>'.$user['addrCode'].'</td>
                                </tr>
                                <tr>
                                    <td>生日</td>
                                    <td>'.$user['birth'].'</td>
                                    <td>性别</td>
                                    <td>'.$user['sex'].'</td>
                                </tr>
                                
                                <tr>
                                    <td>地址</td>
                                    <td>'.$user['addr'].'</td>
                                    <td>省/自治区</td>
                                    <td>'.$user['province'].'</td>
                                </tr>
                                <tr>
                                    <td>盟市</td>
                                    <td>'.$user['city'].'</td>
                                    <td>旗县</td>
                                    <td>'.$user['area'].'</td>
                                </tr>
                                </tbody>
                            </table>
                            
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>资产名称</th>
                                    <th>资产收入</th>
                                    <th>资产余额</th>
                                </tr>
                                </thead>
                                <tbody>
                                '.$info.'
                                </tbody>
                            </table>
                        </div>
                    </div>';
        exit(json_encode($html));
    }

    /**
     * 用户矿产
     */
    public function miningList(){
        $assets = AssetsInfos::with('token,user')->where('ai_id > 0')->order('ai_create_time','desc')->paginate(15,false,[
            'type'     => 'bootstrap',
            'path'     => url('user/miningList'),
        ]);
        $page = $assets->render();
        $this->assign('title','用户产生矿产');
        $this->assign('page',$page);
        $this->assign('list',$assets);
        $this->assign('navigation','user_mininglist');
        return $this->fetch();
    }

}