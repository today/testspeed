<?php

//权限资源分组导航
function getRoleMenu(){
    return  array('mining'=>'矿产管理','admin'=>'权限管理','user'=>'用户管理',
        'token'=>'token管理','system'=>'参数设置','content'=>'公告管理');
}
//,'count'=>'统计报表'
/**
 * @return mixed
 * 配置tab设置
 */
function getConfigMenu(){
    return array('website_info'=>'网站信息','alisms'=>'短信设置','hotc_set'=>'挖矿基本设置','friend_set'=>'邀请规则','drink_desc'=>'酒水秘籍设置','identity'=>'身份证认证测试');
}

//导航列表获取
function getMenuArr(){
    $menuArr = include \think\facade\Env::get('module_path').'conf/menu.php';
    $act_list = session('act_list');
    if($act_list != 'all' && !empty($act_list)){
        $right = M('system_menu')->where("id in ($act_list)")->cache(true)->getField('right',true);
        foreach ($right as $val){
            $role_right .= $val.',';
        }
        foreach($menuArr as $k=>$val){
            foreach ($val['child'] as $j=>$v){
                foreach ($v['child'] as $s=>$son){
                    if(strpos($role_right,$son['op'].'@'.$son['act']) === false){
                        unset($menuArr[$k]['child'][$j]['child'][$s]);//过滤菜单
                    }
                }
            }
        }
        foreach ($menuArr as $mk=>$mr){
            foreach ($mr['child'] as $nk=>$nrr){
                if(empty($nrr['child'])){
                    unset($menuArr[$mk]['child'][$nk]);
                }
            }
        }
    }
    return $menuArr;
}


/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 颜色处理
 * @param $value
 */
function colorArray($value){
    $arr = ['未处理'=>'label-warning','已处理'=>'label-info'];
    return $arr[$value];
}

/**
 * 字体颜色
 */
function textColorArray($value){
    $arr = ['0'=>'','1'=>'','2'=>'text-danger'];
    return $arr[$value];
}
