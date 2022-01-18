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

// 应用公共文件


function getInfo(){
    return \app\admin_login\model\System::find(1);
}

function getIndexBg(){
    return \app\admin_login\model\Banner::where('type',1)->where('show',1)->order('num','desc')->select();
}

function getBanner($type){
    return \app\admin_login\model\Banner::where('show',1)->where('type',$type)->find();
}

function getMenu(){
    $cat = new \app\admin_login\model\Category();
    return $cat->getFullTree();
}
function getFirst(){
    $cat = new \app\admin_login\model\Category();
    return $cat->getTreeFirst();
}
function getHot(){
    $count = \app\admin_login\model\Product::count('id');
    if($count<5){
        $start = 0;
    }else{
        $start = rand(0,$count-5);
    }
    return \app\admin_login\model\Product::where('show',1)->limit($start,5)->field('id,name,url_name,img_url')->select();
}