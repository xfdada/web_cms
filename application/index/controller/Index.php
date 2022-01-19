<?php
namespace app\index\controller;

use app\admin_login\model\Banner;
use app\admin_login\model\Category;
use app\admin_login\model\Content;
use app\admin_login\model\Message;
use app\admin_login\model\News;
use app\admin_login\model\Product;
use app\admin_login\model\Tag;
use think\captcha\Captcha;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $this->assign('menu','index');
        $mores = News::where('show',1)
            ->order('view_count','desc')->limit(0,10)->field('id,title,desc,img_url,tag_id')->select();

        $hot = Product::where('show',1)->order('hot','desc')->limit(0,20)->field('id,name,img_url,url_name')->select();
        foreach ($mores as $k=>$v){
            $tag = new Tag();
            $mores[$k]['tags'] = $tag->whereIn('id',$v['tag_id'])->select();
        }
        $banner['left'] = Banner::where('type',8)->select();
        $banner['right'] = Banner::where('type',9)->select();
        $this->assign('hot',$hot);
        $this->assign('banner',$banner);
        $this->assign('news',$mores);
        return view('index/index');
    }

    public function about()
    {
        $data = Content::find(1);
        $this->assign('menu','about');
        $mores = News::where('show',1)
            ->order('view_count','desc')->limit(0,10)->field('id,title')->select();
        $hot_pro = Product::where('hot',1)->limit(0,5)->field('url_name,name,model,brand,img_url')->select();
        $this->assign('news',$mores);
        $this->assign('data',$data);
        $this->assign('hot_pro',$hot_pro);
        return view("index/about");
    }
    public function contact()
    {
        $data = Content::find(2);
        $this->assign('data',$data);
        $this->assign('menu','contact');
        $mores = News::where('show',1)
            ->order('view_count','desc')->limit(0,10)->field('id,title')->select();
        $hot_pro = Product::where('hot',1)->limit(0,5)->field('url_name,name,model,brand,img_url')->select();
        $this->assign('news',$mores);
        $this->assign('hot_pro',$hot_pro);
        return view("index/contact");
    }

    public function feedback(){
        $data = input();
        $captcha = new Captcha();
        if(!isset($data['code'])){
            return json(['code'=>500,'msg'=>'请输入验证码']);
        }
        if(!$captcha->check($data['code'])){
            return json(['code'=>500,'msg'=>'验证码错误']);
        }
        $list['name'] = $data['name'];
        $list['company'] = $data['company'];
        $list['email'] = $data['email'];
        $list['phone'] = $data['phone'];
        $list['requs'] = $data['requs'];
        if($list['name']==""||$list['company']==""||$list['email']==""||$list['phone']==""){
            return json(['code'=>500,'msg'=>'请填写完整']);
        }
        $msg= new Message();
        $res = $msg->save($list);
        if($res){
            return json(['code'=>200,'msg'=>'success']);
        }
        return json(['code'=>500,'msg'=>'失败,请稍后重试']);
    }

    public function news()
    {
        $news = new News();
        $data = $news->where('show',1)->order('add_time','desc')->field('id,title,desc,img_url,tag_id,view_count,add_time')->paginate(20);
        foreach ($data as $k=>$v){
            $tag = new Tag();
            $data[$k]['tags'] = $tag->whereIn('id',$v['tag_id'])->select();
        }
        $page = $data->render();
        $cat = new Category();
        $tree = $cat->getFullTree();
        $this->assign('tree',$tree);
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('menu','news');
        return view("index/news");
    }
    public function tag_news($tag_id)
    {
        $news = new News();
        $data = $news->where('show',1)->whereLike('tag_id',"%".$tag_id."%")->order('add_time','desc')->field('id,title,desc,img_url,tag_id,view_count,add_time')->paginate(20);
        $tags = new Tag();
        $tag = $tags->find($tag_id);
        foreach ($data as $k=>$v){
            $data[$k]['tags'] = $tags->whereIn('id',$v['tag_id'])->select();
        }
        $cat = new Category();
        $tree = $cat->getFullTree();
        $page = $data->render();
        $this->assign('page',$page);
        $this->assign('tree',$tree);
        $this->assign('data',$data);
        $this->assign('tag',$tag);
        $this->assign('menu','news');
        return view("index/tag_news");
    }

    public function news_detail($id){
        $data = News::find($id);
        News::where('id',$id)->setInc('view_count',1);
        $tag = new Tag();
        $prev = News::where('id','<',$id)->where('show',1)->order('id','desc')->field('id,title')->find();
        $next = News::where('id','>',$id)->where('show',1)->field('id,title')->find();
        $tags = $tag->where("id",'in',$data['tag_id'])->select();
        $like_news = [];

        /**  同标签下相似新闻，取前三个标签中的数据  */
        if($tags!=""){
            $tag_list = explode(',',$data['tag_id']);
            $mores = Db::table('web_news')->where('show',1)->where("id",'<>',$id)
                ->where('tag_id','like',"%{$tag_list[0]}%")->limit(0,10)->field('id,title')->select();
            foreach ($mores as $v){
                $like_ids[] = $v['id'];
                $like_news[] = $v;
            }
            if(count($mores)<10&&count($tag_list)>=2){
                $add = $mores = Db::table('web_news')->where('show',1)->whereNotIn("id",$id.','.implode(',',$like_ids))
                    ->where('tag_id','like',"%{$tag_list[1]}%")->limit(0,10-count($mores))->field('id,title')->select();
                foreach ($add as $v){
                    $like_ids[] = $v['id'];
                    $like_news[] = $v;
                }
                if((count($mores)+count($add))<10&&count($tag_list)>=3){
                    $adds = $mores = Db::table('web_news')->where('show',1)->whereNotIn("id",$id.','.implode(',',$like_ids))
                        ->where('tag_id','like',"%{$tag_list[1]}%")->limit(0,10-count($mores)-count($add))->field('id,title')->select();
                    foreach ($adds as $v){
                        $like_ids[] = $v['id'];
                        $like_news[] = $v;
                    }
                }
            }
        }

        /** 热门新闻  热门产品 */
        $hot_news = News::where('show',1)->order('hot','desc')->limit(0,5)->field('id,title')->select();
        $hot_pro = Product::where('show',1)->where('hot',1)->limit(0,5)->field('url_name,name,model,brand,img_url')->select();
        $this->assign('news',$like_news);
        $this->assign('menu','news');
        $this->assign("tags",$tags);
        $this->assign("data",$data);
        $this->assign("prev",$prev);
        $this->assign("next",$next);
        $this->assign('hot_news',$hot_news);
        $this->assign('hot_pro',$hot_pro);
        return view('index/news_detail');
    }

    public function product_list($name='',$type='',$last=''){
        $field = Db::table('web_category')->where('url_name',$name)->field('show_clo,title,keyword,desc')->find();
        $f_one = explode('-',$field['show_clo']);
        if(empty($f_one[0])){
            $f_one[0]='model,brand';
            $f_one[1] = "型号,品牌" ;
        }
        $fields = 'id,img_url,url_name,name,'.$f_one[0];
        $this->assign('menu',$name);
        $cat = new Category();
        if($name==''&&$type==''&&$last==''){
            $pro = Product::where('show',1)->order('add_time','desc')->field($fields)->paginate(24);
        }
        else if($name!=''&&$type==''&&$last==''){
            $id = $cat->where('url_name',$name)->value('id');
            $p_id = $cat->where('show',1)->where('p_id',$id)->column('id');
            $p_id = implode($p_id,',');
            $pro = Product::where('show',1)->whereIn('c_id',$id.','.$p_id)->field($fields)->order('add_time','desc')->paginate(24);
        }else if($name!=''&&$type!=''&&$last==''){
            $id = $cat->where('url_name',$type)->value('id');;
            $p_id = $cat->where('show',1)->where('p_id',$id)->column('id');
            array_push($p_id,$id);
           $pro = Product::where('show',1)->whereIn('c_id',$p_id)->field($fields)->order('add_time','desc')->paginate(24);
        }else if($last!=''){
            $id = $cat->where('url_name',$last)->field('id')->find();
            $pro = Product::where('show',1)->where('c_id',$id)->field($fields)->order('add_time','desc')->paginate(24);
        }
        $page =  $pro->render();
        $info = [
            'title'=>explode(',',$f_one[1]),
            'value'=>explode(',',$f_one[0])
        ];
        $tree = $cat->getFullTree();
        $this->assign('type',$type);
        $this->assign('last',$last);
        $this->assign('tree',$tree);
        $this->assign('cate',$field);
        $this->assign('page',$page);
        $this->assign('pro',$pro);
        $this->assign('info',$info);
        return view('index/product_list');
    }

    public function product_detail($names){
        if(is_file('tempHtml/'.$names.'.html')){
            ob_end_clean();
            header('Content-type:text/html'); //mime信息
            ob_start();
            return file_get_contents('tempHtml/'.$names.'.html');
        }
        $info = Product::where('url_name',$names)->find();
        /** 头部信息展示    */
        $field = Db::table('web_category')->where('id',$info['c_id'])->value('detail_clo');
        $f_one = explode('-',$field);
        $top_info = [
            'title'=>explode(',',$f_one[1]),
            'value'=>explode(',',$f_one[0])
        ];
        /**    */

        /** 产品详情模块    */
            $attr = array_merge(['id','name','model','c_id','url_name','brand','state','keyword','desc','content','show','hot','add_time','img_url','tech'],explode(',',$f_one[0]));
            $res = Db::query("SELECT column_name,column_comment FROM information_schema.Columns WHERE table_name='web_product'");
            $attrs = [];
            foreach ($res as $k=>$v){
                if(!in_array($v['column_name'],$attr))
                    $attrs[] = [
                    'attr_name'=>$v['column_comment'],
                    'attr_value'=>$info[$v['column_name']]
                ];
            }
        /**    */

        /** 推荐产品    */
            $count = Product::where('c_id',$info['c_id'])->where('id','<>',$info['id'])->column('id');
            if(!empty($count)){
                $ids = array_rand($count,8);
                $rand = [];
                foreach ($ids as $v){
                    $rand[] = $count[$v];
                }
                $like_id = implode(',',$rand);
                $like_pro = Product::where('show',1)->where('id','in',$like_id)->where('c_id',$info['c_id'])->field('name,url_name,img_url')->select();
                $hots = $info['id'].','.$like_id;
            }else{
                $like_pro =[];
                $hots = $info['id'];
            }
        /**    */
        /** 热门产品    */

        $hot_id = Product::whereNotIn('id',$hots)->order('hot','desc')->limit(0,500)->column('id');
        $hot_ids = array_rand($hot_id,8);

        $rands = [];
        foreach ($hot_ids as $v){
            $rands[] = $hot_id[$v];
        }
        $hot_pro = Product::where('show',1)->where('id','in',implode(',',$rands))->field('name,url_name,img_url')->select();

        /**    */

        /** 相关新闻    */
            $hot_news = News::where('tag_id','like',"%".$info['c_id']."%")->field('id,title')->limit(0,10)->select();
        /**    */
        /** 产品类别    */
            $cat = new Category();
            $tree = $cat->getFullTree();
            //计算层级 归属哪一类
            $last = '';
            $type = '';
            $url_name = $cat->find($info['c_id']);
            if($url_name['level']==3){
                $last = $url_name['url_name'];
                $types = Product::where('id',$url_name['p_id'])->find();
                $menu = Product::where('id',$types['p_id'])->value('url_name');
            }else if($url_name['level']==2){
                $type = $url_name['url_name'];
                $menu = $cat->where('id',$url_name['p_id'])->value('url_name');
            }else{
                $menu = $url_name['url_name'];
            }
        /**    */
        $this->assign('type',$type);
        $this->assign('last',$last);
        $this->assign('menu',$menu);
        $this->assign('tree',$tree);
        $this->assign('info',$info);
        $this->assign('top_info',$top_info);
        $this->assign('like_pro',$like_pro);
        $this->assign('hot_news',$hot_news);
        $this->assign('hot_pro',$hot_pro);
        $this->assign('attrs',$attrs);
        $content = $this->fetch('index/product-detail')->getContent();
        file_put_contents('tempHtml/'.$names.'.html',$content);
        return $content;
    }


    public function hotList(){

        $f_one[0]='model,brand';
        $f_one[1] = "型号,品牌" ;
        $fields = 'id,img_url,url_name,name,'.$f_one[0];
        $hot_list = Product::where('hot',1)->where('show',1)->field($fields)->paginate(24);
        $page =  $hot_list->render();
        $this->assign('pro',$hot_list);
        $this->assign('page',$page);
        $info = [
            'title'=>explode(',',$f_one[1]),
            'value'=>explode(',',$f_one[0])
        ];
        $this->assign('info',$info);
        $this->assign('menu','hot');
        return view('index/hot-list');
    }


    public function search($keyword=''){
        if($keyword==''){
            $data = Db::table('web_product')->where('show',1)->where('hot','1')->field('name,url_name,model,img_url,state,brand,fenz')->limit(0,20)->select();
        }else{
            $data = Db::table('web_product')->where('show',1)->where('model','like',"%{$keyword}%")->field('name,url_name,model,img_url,state,brand,fenz')->select();
        }
        $cat = new Category();
        $tree = $cat->getFullTree();
        $this->assign('keyword',$keyword);
        $this->assign('tree',$tree);
        $this->assign('data',$data);
        $this->assign('menu','');
        return view('index/search');
    }
}
