<?php
namespace app\admin_login_en\controller;
use app\admin_login_en\model\Category;
use app\admin_login_en\model\System;
use think\Controller;
class Index extends Controller{
    public function index(){
        $this->assign('menu','index');
        return view('view/index');
    }
    public function system(){
        $this->assign('menu','system');
        $info = System::find(1);
        $this->assign('info',$info);
        return view('view/system');
    }

    public function content(){
        $this->assign('menu','content');
        return view('view/content');
    }
    public function banner(){
        $this->assign('menu','banner');
        return view('view/banner');
    }
    public function news(){
        $this->assign('menu','news');
        return view('view/news');
    }

    public function category(){
        $this->assign('menu','category');
        return view('view/category');
    }

    public function product(){
        $this->assign('menu','product');
        return view('view/product');
    }
}