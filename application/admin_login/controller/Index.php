<?php
namespace app\admin_login\controller;
use app\admin_login\model\Banner;
use app\admin_login\model\Category;
use app\admin_login\model\Message;
use app\admin_login\model\News;
use app\admin_login\model\Product;
use app\admin_login\model\System;
use app\Base;
use think\Db;

class Index extends Base {
    public function index(){
        $product = Product::count('id');
        $news = News::count('id');
        $banner = Banner::count('id');
        $msg = Message::count('id');
        $this->assign('product',$product);
        $this->assign('news',$news);
        $this->assign('banner',$banner);
        $this->assign('msg',$msg);
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

    public function message(){
        $this->assign('menu','message');
        return view('view/message');
    }
    public function product(){
        $attr = ['id','name','model','c_id','url_name','brand','state',
            'keyword','desc','content','show','hot','add_time','img_url','tech'];
        $res = Db::query("SELECT column_name as value,column_comment as title FROM information_schema.Columns WHERE table_name='web_product'");
        $attrs = [];
        foreach ($res as $k=>$v){
            if(!in_array($v['value'],$attr))
                $attrs[] = [
                    'attr_name'=>$v['title'],
                    'attr_value'=>$v['value']
                ];
        }
        $value = explode('-',Db::table('web_sys')->where('id',1)->value('show_clo'));
        $value = explode(',',$value[0]);
        $value = json_encode($value);
        $data = json_encode($res);
        $this->assign('value',$value);
        $this->assign('data',$data);
        $this->assign('menu','product');
        $this->assign('attr',$attrs);
        return view('view/product');
    }
    public function xls(){
        $data = Db::query('SELECT COLUMN_NAME as value,COLUMN_COMMENT as title FROM information_schema.COLUMNS WHERE table_name ="web_product"');
        $this->assign('menu','xls');
        $value = explode(',',Db::table('web_sys')->where('id',1)->value('xls_clo'));
        $data = json_encode($data);
        $value = json_encode($value);
        $this->assign('data',$data);
        $this->assign('value',$value);
        return view('view/xls');
    }

    public function listSet($id){
        $data = Db::query('SELECT COLUMN_NAME as value,COLUMN_COMMENT as title FROM information_schema.COLUMNS WHERE table_name ="web_product"');

        $attrs = ['id','name','c_id','url_name','keyword','desc','content','show','hot','add_time','img_url'];
        $list = [];
        foreach ($data as $k=>$v){
            if(!in_array($v['value'],$attrs)){
               $list[] = $v;
            }
        }
        $res = Db::table('web_category')->where('id',$id)->value('show_clo');
        $value = explode('-',$res);
        $val = explode(',',$value[0]);
        $list = json_encode($list);
        $val = json_encode($val);
        $this->assign('data',$list);
        $this->assign('value',$val);
        $this->assign('id',$id);
        return view('view/list');
    }
    public function detailSet($id){
        $data = Db::query('SELECT COLUMN_NAME as value,COLUMN_COMMENT as title FROM information_schema.COLUMNS WHERE table_name ="web_product"');
        $res = Db::table('web_category')->where('id',$id)->value('detail_clo');
        $attrs = ['id','name','c_id','url_name','keyword','desc','content','show','hot','add_time','img_url'];
        $list = [];
        foreach ($data as $k=>$v){
            if(!in_array($v['value'],$attrs)){
                $list[] = $v;
            }
        }
        $value = explode('-',$res);
        $val = explode(',',$value[0]);
        $list = json_encode($list);
        $val = json_encode($val);
        $this->assign('data',$list);
        $this->assign('value',$val);
        $this->assign('id',$id);
        return view('view/details');
    }
}