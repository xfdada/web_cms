<?php
namespace app\admin_login_en\controller;
use app\admin_login_en\model\Banner;
use app\admin_login_en\model\Category;
use app\admin_login_en\model\Content;
use app\admin_login_en\model\News;
use app\admin_login_en\model\Product;
use app\admin_login_en\model\System;
use app\admin_login_en\model\Tag;
use think\Db;
use think\Request;
use think\Image;
use think\Controller;
class Api extends Controller{

    public function system(){
        $data = input('data',[]);
        $data['web_js'] = htmlspecialchars($data['web_js']);
        $sys = new System();
        $res = $sys->save($data,['id'=>1]);
        if ($res){
            return json(['code'=>200,'msg'=>'更新成功']);
        }
        return json(['code'=>500,'msg'=>'更新失败']);
    }

    public function pages(){
        $page = Content::select();
        $count = Content::count('id');
        return json(['code'=>0,'count'=>$count ,'data'=>$page]);
    }
    public function getPage($id){
        if($id!=''){
            $page = Content::find($id);
            return json(['code'=>200,'data'=>$page]);
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function page_update($id){
        if($id!=''){
            $data = input('data',[]);
            $page = new Content();
           $res =  $page->save($data,['id'=>$id]);
           if($res){
               return json(['code'=>200,'msg'=>'更新成功']);
           }else{
               return json(['code'=>500,'msg'=>'更新失败']);
           }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function banner(){
        $banner = Banner::select();
        $count = Banner::count('id');
        return json(['code'=>0,'count'=>$count ,'data'=>$banner]);
    }
    public function addBanner(){

        $data = input('data',[]);
        $banner = new Banner();
        $addr = ["123","首页轮播图","主营品牌","新闻资讯","联系我们","关于我们","产品详情","产品列表"];
        $data['addr'] = $addr[$data['type']];

        $res =  $banner->save($data);
        if($res){
            return json(['code'=>200,'msg'=>'添加成功']);
        }else{
            return json(['code'=>500,'msg'=>'添加失败']);
        }
    }
    public function getBanner($id){
        if($id!=''){
            $page = Banner::find($id);
            return json(['code'=>200,'data'=>$page]);
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function banner_update($id){
        if($id!=''){
            $data = input('data',[]);
            $banner = new Banner();
            $addr = ["123","首页轮播图","主营品牌","新闻资讯","联系我们","关于我们","产品详情","产品列表"];
            $data['addr'] = $addr[$data['type']];

            $res =  $banner->save($data,['id'=>$id]);
            if($res){
                return json(['code'=>200,'msg'=>'更新成功']);
            }else{
                return json(['code'=>500,'msg'=>'更新失败']);
            }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function banner_del($id){
        if($id!=''){
            $page = Banner::destroy($id);
            if($page){
                return json(['code'=>200,'msg'=>"删除成功"]);
            }else{
                return json(['code'=>200,'msg'=>"删除失败"]);
            }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }

    public function banner_show($id){
        $value = input('value','true');
        if($value=="true"){
            $data['show'] = 1;
        }else{
            $data['show'] = 2;
        }
        $new = new Banner();
        $res = $new->save($data,['id'=>$id]);
        if(!$res){
            return json(['code'=>500,'msg'=>"更新失败!"]);
        }
        return json(['code'=>200,'msg'=>"更新成功!"]);
    }

    /**
     * banner_num 更新排序
     * @param $id
     * @return \think\response\Json
     */
    public function banner_num($id){
        $value = input('num',0);

        $data['num'] = $value ;
        $new = new Banner();
        $res = $new->save($data,['id'=>$id]);
        if(!$res){
            return json(['code'=>500,'msg'=>"更新失败!"]);
        }
        return json(['code'=>200,'msg'=>"更新成功!"]);
    }

    public function tags(){
        $tag = Tag::select();
        return json(['code'=>200,'data'=>$tag]);
    }
    public function addTag(){
        $data['tag_name'] = input('tag_name','');
        if($data['tag_name']!=''){
            $tag = new Tag();
            $res = $tag->where('tag_name',$data['tag_name'])->find();
            if(!$res){
                $ids = $tag->save($data);
                if($ids){
                    return json(['code'=>200,'id'=>$tag->id,'msg'=>'添加成功']);
                }
                return json(['code'=>500,'msg'=>'添加失败']);
            }
            return json(['code'=>500,'msg'=>'重复添加标签']);
        }
        return json(['code'=>500,'msg'=>'请填写完整']);
    }

    public function news($page=1,$limit=10){
        $title = input("title",'');
        $offset = ($page-1)*$limit;
        $banner = News::where('title','like','%'.$title.'%')->limit($offset,$limit)->select();
        foreach ($banner as $k=>$v){
            $banner[$k]['tag_name'] = implode(',',Tag::all($v['tag_id'])->column('tag_name'));
        }
        $count = News::count('id');
        return json(['code'=>0,'count'=>$count ,'data'=>$banner]);
    }

    /**
     * addNews 添加新闻
     * @return \think\response\Json
     */
    public function addNews(){
        $data = input('data',[]);
        $data['tag_id'] = input('tag_id',[]);
        $new = new News();
        $data['add_time'] = date("Y-m-d H:i:s",time());
        $res =  $new->save($data);
        if($res){
            return json(['code'=>200,'msg'=>'添加成功']);
        }else{
            return json(['code'=>500,'msg'=>'添加失败']);
        }
    }
    public function getNew($id){
        if($id!=''){
            $new = News::find($id);
            return json(['code'=>200,'data'=>$new]);
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function new_update($id){
        if($id!=''){
            $data = input('data',[]);
            $new = new News();

            $res =  $new->save($data,['id'=>$id]);
            if($res){
                return json(['code'=>200,'msg'=>'更新成功']);
            }else{
                return json(['code'=>500,'msg'=>'更新失败']);
            }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function news_del($id){
        if($id!=''){
            $new = News::destroy($id);
            if($new){
                return json(['code'=>200,'msg'=>"删除成功"]);
            }else{
                return json(['code'=>200,'msg'=>"删除失败"]);
            }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }

    public function new_show($id){
        $value = input('value','true');
        if($value=="true"){
            $data['show'] = 1;
        }else{
            $data['show'] = 2;
        }
        $new = new News();
        $res = $new->save($data,['id'=>$id]);
        if(!$res){
            return json(['code'=>500,'msg'=>"更新失败!"]);
        }
        return json(['code'=>200,'msg'=>"更新成功!"]);
    }


    public function category($page=1,$limit=10){
        $offset = ($page-1)*$limit;
        $banner = Category::limit($offset,$limit)->select();
        foreach ($banner as $k=>$v){
            //统计产品数量
        }
        $count = Category::count('id');
        return json(['code'=>0,'count'=>$count ,'data'=>$banner]);
    }
    public function getTree(){
        $cat = new Category();
        $data = $cat ->getTree();
        return json(['code'=>200,'data'=>$data]);
    }
    public function getFullTree(){
        $cat = new Category();
        $data = $cat ->getFullTree();
        return json(['code'=>200,'data'=>$data]);
    }
    /**
     * addNews 添加类目
     * @return \think\response\Json
     */
    public function addCategory(){
        $data = input('data',[]);
        $p_info = explode(',',$data['p_info']);
        $data['p_id'] = $p_info[0];
        $data['level'] = $p_info[0]+1;
        $data['url_name'] = str_replace(' ','_',$data['url_name']);
        $new = new Category();
        $res =  $new->save($data);
        if($res){
            return json(['code'=>200,'msg'=>'添加成功']);
        }else{
            return json(['code'=>500,'msg'=>'添加失败']);
        }
    }
    public function getCategory($id){
        if($id!=''){
            $new = Category::find($id);
            $new['p_info'] = $new['p_id'].','.($new['level']-1);
            return json(['code'=>200,'data'=>$new]);
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function category_update($id){
        if($id!=''){
            $data = input('data',[]);
            $data['url_name'] = str_replace(' ','_',$data['url_name']);
            $new = new Category();
            $res =  $new->save($data,['id'=>$id]);
            if($res){
                return json(['code'=>200,'msg'=>'更新成功']);
            }else{
                return json(['code'=>500,'msg'=>'更新失败']);
            }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function category_del($id){
        if($id!=''){
            $new = Category::destroy($id);
            if($new){
                return json(['code'=>200,'msg'=>"删除成功"]);
            }else{
                return json(['code'=>200,'msg'=>"删除失败"]);
            }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }

    public function category_show($id){
        $value = input('value','true');
        if($value=="true"){
            $data['show'] = 1;
        }else{
            $data['show'] = 2;
        }
        $new = new Category();
        $res = $new->save($data,['id'=>$id]);
        if(!$res){
            return json(['code'=>500,'msg'=>"更新失败!"]);
        }
        return json(['code'=>200,'msg'=>"更新成功!"]);
    }


    public function product($page=1,$limit=10){
        $offset = ($page-1)*$limit;
        $name = input('name','');
        if($name===''){
            $sql = "SELECT a.*,b.c_name FROM web_product_en AS a LEFT JOIN web_category_en AS b ON a.`c_id`=b.`id` LIMIT {$offset}, {$limit}";
            $count = Product::count('id');
        }else{
            $sql = "SELECT a.*,b.c_name FROM web_product_en AS a LEFT JOIN web_category_en AS b ON a.`c_id`=b.`id` Where a.name like '%{$name}%' LIMIT {$offset}, {$limit}";
            $count = Product::where('name','like','%'.$name.'%')->count('id');
        }

        $banner = Db::query($sql);

        return json(['code'=>0,'count'=>$count ,'data'=>$banner]);
    }
    /**
     * addNews 添加类目
     * @return \think\response\Json
     */
    public function addProduct(){
        $data = input('data',[]);
        $new = new Product();
        $data['add_time'] = date('Y-m-d H:i:s');
        $res =  $new->save($data);
        if($res){
            return json(['code'=>200,'msg'=>'添加成功']);
        }else{
            return json(['code'=>500,'msg'=>'添加失败']);
        }
    }
    public function getProduct($id){
        if($id!=''){
            $new = Product::find($id);
            return json(['code'=>200,'data'=>$new]);
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function product_update($id){
        if($id!=''){
            $data = input('data',[]);
            $new = new Product();
            $res =  $new->save($data,['id'=>$id]);
            if($res){
                return json(['code'=>200,'msg'=>'更新成功']);
            }else{
                return json(['code'=>500,'msg'=>'更新失败']);
            }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }
    public function product_del($id){
        if($id!=''){
            $new = Product::destroy($id);
            if($new){
                return json(['code'=>200,'msg'=>"删除成功"]);
            }else{
                return json(['code'=>200,'msg'=>"删除失败"]);
            }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }

    public function product_show($id){
        $value = input('value','true');
        $name = input('name','');
        if($value=="true"){
            $data[$name] = 1;
        }else{
            $data[$name] = 2;
        }
        $new = new Product();
        $res = $new->save($data,['id'=>$id]);
        if(!$res){
            return json(['code'=>500,'msg'=>"更新失败!"]);
        }
        return json(['code'=>200,'msg'=>"更新成功!"]);
    }
}