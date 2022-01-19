<?php
namespace app\admin_login\controller;
use app\admin_login\model\Banner;
use app\admin_login\model\Category;
use app\admin_login\model\Content;
use app\admin_login\model\Message;
use app\admin_login\model\News;
use app\admin_login\model\Product;
use app\admin_login\model\System;
use app\admin_login\model\Tag;
use app\Base;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;
use think\facade\Env;
use think\Request;
use think\Image;
class Api extends Base {

    public function system(){
        $data = input('data',[]);
        $sys = new System();
        $res = $sys->save($data,['id'=>1]);
        if ($res){
            $this->deldir('tempHtml/');
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
        $addr = ["123","首页轮播图","优势产品","新闻资讯","联系我们","关于我们","产品详情","产品列表","首页推荐产品左侧","首页推荐产品右侧"];
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
            $addr = ["123","首页轮播图","优势产品","新闻资讯","联系我们","关于我们","产品详情","产品列表","首页推荐产品左侧","首页推荐产品右侧"];
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

    /**
     * 获取分类
     */
    public function cats(){
        $cat = new Category();
        $data = $cat ->getFullTrees();
        return json(['code'=>200,'data'=>$data]);
    }
    public function news($page=1,$limit=10){
        $title = input("title",'');
        $offset = ($page-1)*$limit;
        $banner = News::where('title','like','%'.$title.'%')->limit($offset,$limit)->select();
        foreach ($banner as $k=>$v){
            $banner[$k]['tag_name'] = implode(',',Category::whereIn('id',$v['tag_id'])->column('c_name'));
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
            $this->deldir('tempHtml/');
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
            $data['tag_id'] = input('tag_id','');
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
        $name = input('name','');
        if($value=="true"){
            $data[$name] = 1;
        }else{
            $data[$name] = 2;
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
        $data['level'] = $p_info[1]+1;
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
            $sql = "SELECT a.*,b.c_name FROM web_product AS a LEFT JOIN web_category AS b ON a.`c_id`=b.`id` LIMIT {$offset}, {$limit}";
            $count = Product::count('id');
        }else{
            $sql = "SELECT a.*,b.c_name FROM web_product AS a LEFT JOIN web_category AS b ON a.`c_id`=b.`id` Where a.name like '%{$name}%' LIMIT {$offset}, {$limit}";
            $count = Product::where('name','like',"%".$name."%")->count('id');
        }
        $banner = Db::query($sql);

        return json(['code'=>0,'count'=>$count ,'data'=>$banner]);
    }
    /**
     * addNews 添加产品
     * @return \think\response\Json
     */
    public function addProduct(){
        $data = input('data',[]);
        $new = new Product();
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['url_name'] = str_replace(array('-',':',' '),'_',$data['model']);
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
                $this->deldir('tempHtml/');
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

    public function message($page=1,$limit=10){
        $offset = ($page-1)*$limit;
        $banner = Message::limit($offset,$limit)->select();
        $count = Message::count('id');
        return json(['code'=>0,'count'=>$count ,'data'=>$banner]);
    }

    /**
     * 删除留言
     * @param $id
     * @return \think\response\Json
     */
    public function message_del($id){
        if($id!=''){
            $new = Message::destroy($id);
            if($new){
                return json(['code'=>200,'msg'=>"删除成功"]);
            }else{
                return json(['code'=>200,'msg'=>"删除失败"]);
            }
        }
        return json(['code'=>500,'msg'=>'参数错误']);
    }

    /**
     *  导出留言数据
     *
     *
     */
    public function exportXls(){
        $start_time = input("start_time",'');
        $end_time = input("end_time",'');
        $list = Message::where('time','>',$start_time)->where('time','<',$end_time)->select();
        $xlsName = $start_time."至".$end_time."留言表"; // 文件名
        $xlsCell = [        // 列名
            ['id', '序号'],
            ['company', '公司'],
            ['name', '姓名'],
            ['email', '邮箱'],
            ['phone', '联系电话'],
            ['requs', '需求'],
            ['time', '时间']
        ];// 表头信息
        $this->downloadExcel($xlsName, $xlsCell, $list);// 传递参数
    }

    /**
     * @param $Title
     * @param $CellNameList
     * @param $TableData
     * @return mixed
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    protected function downloadExcel($Title, $CellNameList, $TableData)
    {
        $xlsTitle    = iconv('utf-8', 'gb2312', $Title);  // excel标题
        $fileName    = $Title;                  // 文件名称
        $cellNum     = count($CellNameList);    // 单元格名 个数
        $dataNum     = count($TableData);       // 数据 条数

        $obj = new \PHPExcel();
        $originCell = [                           // 所有原生名
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];

        //getActiveSheet(0) 获取第一张表
        $obj->getActiveSheet(0)
            ->mergeCells('A1:' . $originCell[$cellNum - 1] . '1');       //合并单元格A1-F1 变成新的A1

        $obj->getActiveSheet(0)->setCellValue('A1', $fileName);      // 设置第一张表中 A1的内容

        for ($i = 0; $i < $cellNum; $i++) {                                     // 设置第二行 ,值为字段名
            $obj->getActiveSheet(0)
                ->setCellValue($originCell[$i] . '2', $CellNameList[$i][1]);      //设置 A2-F2 的值
        }
        $obj->getActiveSheet()->getColumnDimension('B')->setWidth(30);//设置列宽度
        $obj->getActiveSheet()->getColumnDimension('C')->setWidth(30);//设置列宽度
        $obj->getActiveSheet()->getColumnDimension('D')->setWidth(30);//设置列宽度
        $obj->getActiveSheet()->getColumnDimension('E')->setWidth(30);//设置列宽度
        $obj->getActiveSheet()->getColumnDimension('G')->setWidth(30);//设置列宽度
        $obj->getActiveSheet()->getColumnDimension('F')->setWidth(100);//设置列宽度
        // Miscellaneous glyphs, UTF-8  循环写入数据
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {                         // 设置第三行 ,每一行为 数据库一条数据
                $obj->getActiveSheet(0)                                 // 设 A3 值, 值为$TableData[0]['id']
                ->setCellValue($originCell[$j] . ($i + 3), $TableData[$i][$CellNameList[$j][0]]);
            }
        }
        //居中
        $obj->getActiveSheet(0)->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        ob_end_clean();//这一步非常关键，用来清除缓冲区防止导出的excel乱码
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xlsx"');
        header("Content-Disposition:attachment;filename=$fileName.xlsx");   //"xls"参考下一条备注
        $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');

        //"Excel2007"生成2007版本的xlsx，"Excel5"生成2003版本的xls 调用工厂类
        return $objWriter->save('php://output');
    }


    /**
     * 导入数据
     * @return \think\response\Json|void
     */
    public function importData(){
        $name = input("fileName",'');
        $c_id = input("c_id",'');
        if ($name==''||$c_id==''){
            return json(['code'=>500,'msg'=>'参数不全']);
        }
        $objReader = IOFactory::createReader('Xlsx');
        $objPHPExcel = $objReader->load($name);
        $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
        $highestRow = $sheet->getHighestRow();       // 取得总行数
        $data = [];
        $attr =explode(',',Db::table('web_sys')->where('id',1)->value('xls_clo'));
        for ($j = 2; $j <= $highestRow; $j++){
            $arr = [];
                for ($i=0;$i<count($attr); $i++){
                    $num = 65+$i;
                    $char = chr($num);
                    if((65+$i)>=97){
                        $char = "A".chr($num-32);
                    }
                    $arr[$attr[$i]] = $objPHPExcel->getActiveSheet()->getCell($char . $j)->getValue();
                }
                $arr['c_id'] = $c_id;
                $data[] = $arr;
        }
        $nums = Db::name('web_product')->insertAll($data);
        return json(['code'=>200,'msg'=>'导入成功,总共'.($highestRow-1).'条数据，成功插入'.$nums.'条']);
    }

    /**
     * XlsSet  数据表导入字段设置
     */
    public function XlsSet(){
        $data = input('data','');
        $lists = array_column($data,'value');
        if(!in_array('url_name',$lists)){
            return json(['code'=>500,'msg'=>'路由名称是必选项']);
        }
        $list['xls_clo'] = implode(',',$lists);
        $res = Db::table('web_sys')->where('id',1)->update($list);
        if ($res){
            return json(['code'=>200,'msg'=>'保存成功']);
        }
        return json(['code'=>500,'msg'=>'保存失败，请稍后再试']);
    }

    /**
     * 下载模板
     */
    public function downloadXls(){
        $time = time();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1:AH1')->getFont()->setBold(true)->setName('Arial')->setSize(10);
        $title =explode(',',Db::table('web_sys')->where('id',1)->value('xls_clo'));

        for ($i=0;$i<count($title);$i++){
            $num = 65+$i;
            $char = chr($num);
            if((65+$i)>=97){
                $char = "A".chr($num-32);
            }
            $sheet->setCellValue($char.'1', $title[$i]);
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'."product". date('Y-m-d'). '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        //删除清空：
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

    /**
     * 导出所有产品信息
     */
    public function exportAll(){
    }



    /**
     * 获取数据库字段
     */
    public function getField(){

        $res = Db::query("SELECT column_name,column_comment,column_type,column_default FROM information_schema.Columns WHERE table_name='web_product'");
        $num = Db::query("SELECT COUNT(*)AS num FROM information_schema.COLUMNS WHERE table_name='web_product'");
        return json(['code'=>0,'count'=>$num[0]['num'],'data'=>$res]);
    }
    /**
     * 添加字段
     *
     */
    public function addAttr(){
        $data = input('data','');
        if($data['name']==''||$data['type']==''||$data['comment']==''){
            return json(['code'=>500,'msg'=>'参数不全,请填写完整']);
        }
        if($data['type']=='int'&&$data['long']>11){
            return json(['code'=>500,'msg'=>'类型为int长度不能超过11']);
        }
        $field = Db::query("SELECT column_name FROM information_schema.Columns WHERE table_name='web_product'");
        $fields = array_column($field,'column_name');
        if(in_array($data['name'],$fields)){
            return json(['code'=>500,'msg'=>'字段名不能重复']);
        }
        Db::execute("ALTER TABLE `web_product` ADD COLUMN `{$data['name']}` {$data['type']}({$data['long']}) NULL COMMENT '{$data['comment']}'");

        return json(['code'=>200,'msg'=>'添加成功']);

    }

    public function delAttr($attr){
        if ($attr==''){
            return json(['code'=>500,'msg'=>'参数不全']);
        }
        $attrs = ['id','name','model','c_id','url_name','brand','state',
            'keyword','desc','content','show','hot','add_time','img_url','urls'];
        if(in_array($attr,$attrs)){
            return json(['code'=>500,'msg'=>'默认参数不可删除']);
        }
        $res = Db::execute("ALTER TABLE `web_product` DROP COLUMN `{$attr}`");
        if($res){
            return json(['code'=>200,'msg'=>'删除成功']);
        }
        return json(['code'=>500,'msg'=>'删除失败']);
    }

    public function editAttr($attr){
        if ($attr==''){
            return json(['code'=>500,'msg'=>'参数不全']);
        }
        $attrs = ['id','name','model','c_id','url_name','brand','state',
            'keyword','desc','content','show','hot','add_time','img_url','urls'];
        if(in_array($attr,$attrs)){
            return json(['code'=>500,'msg'=>'默认参数不可编辑']);
        }

        Db::query("ALTER TABLE `web_product` DROP COLUMN `{$attr}`");
        return json(['code'=>200,'msg'=>'删除成功']);
    }

    public function getAttr(){
        $attr = input('data','');
        if($attr==''){
            return json(['code'=>500,'msg'=>'参数不全']);
        }
        $attrs = ['id','name','model','c_id','url_name','brand','state',
            'keyword','desc','content','show','hot','add_time','img_url','urls'];
        if(in_array($attr,$attrs)){
            return json(['code'=>500,'msg'=>'默认参数不可编辑']);
        }
        $res = Db::query("SELECT column_name,column_comment,column_type,column_default FROM information_schema.COLUMNS WHERE table_name='web_product' AND column_name='{$attr}'");
        $data = $res[0];
        $data['type'] = strstr($data['column_type'], '(', TRUE);
        $data['num'] = (int) filter_var($data['column_type'], FILTER_SANITIZE_NUMBER_INT);
        return json(['code'=>200,'data'=>$data]);
    }

    /**
     * 字段更新
     */
    public function attr_update(){
        $data = input('data','');
        if($data['name']==''||$data['type']==''||$data['comment']==''){
            return json(['code'=>500,'msg'=>'参数不全,请填写完整']);
        }
        if($data['type']=='int'&&$data['long']>11){
            return json(['code'=>500,'msg'=>'类型为int长度不能超过11']);
        }
        Db::query("ALTER TABLE `web_product` CHANGE `{$data['namets']}` `{$data['name']}` {$data['type']}({$data['long']}) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '{$data['default']}' NULL COMMENT '{$data['comment']}'");
        return json(['code'=>200,'msg'=>'更新成功']);
    }


    /**
     * XlsSet  类别列表显示配置
     */
    public function setShow($id){
        $data = input('data','');
        $lists = array_column($data,'value');
        $titles = array_column($data,'title');
        $list['show_clo'] = implode(',',$lists).'-'.implode(',',$titles);
        $res = Db::table('web_category')->where('id',$id)->update($list);
        if ($res){
            Db::table('web_category')->where('p_id',$id)->update($list);
            $sub = Db::table('web_category')->where('p_id',$id)->select();
            foreach ($sub as $v){
                Db::table('web_category')->where('p_id',$v['id'])->update($list);
            }
            return json(['code'=>200,'msg'=>'保存成功']);
        }
        return json(['code'=>500,'msg'=>'保存失败，请稍后再试']);
    }

    /**
     * XlsSet  详情页顶部显示配置
     */
    public function detailShow($id){
        $data = input('data','');
        $lists = array_column($data,'value');
        $titles = array_column($data,'title');
        $list['detail_clo'] = implode(',',$lists).'-'.implode(',',$titles);
        $res = Db::table('web_category')->where('id',$id)->update($list);
        if ($res){
            Db::table('web_category')->where('p_id',$id)->update($list);
            $sub = Db::table('web_category')->where('p_id',$id)->select();
            foreach ($sub as $v){
                Db::table('web_category')->where('p_id',$v['id'])->update($list);
            }
            $this->deldir('tempHtml/');
            return json(['code'=>200,'msg'=>'保存成功']);
        }
        return json(['code'=>500,'msg'=>'保存失败，请稍后再试']);
    }

    /**
     * 清除缓存
     */
    public function clean(){
        $this->deldir('tempHtml/');
        return json(['code'=>200,'msg'=>'清除成功']);
    }
}