<?php
namespace app\admin_login\controller;
use think\Request;
use think\Image;
use think\Controller;
class Upload extends Controller{

    public function upload(Request $request){
        $file = $request->file('file');
        $info = $file->move( 'uploads/img/');
        if($info){
            $name = str_replace("\\","/",$info->getSaveName());
            $url = "uploads/img/".$name ;
            $code = 200;
        }else{
            $code = 500;
            $url = "";
        }
        return json(['code'=>$code,'data'=>$url]);
    }
    public function upload_getThumb(Request $request){
        $file = $request->file('file');
        $info = $file->move( 'uploads/img');
        if($info){
            $url = "uploads/img/".$info->getSaveName();
            $ext = $info->getExtension();
            $thumb = "uploads/img/".date("Ymd",time())."/".md5(date("YmdHis",time()).rand(0,9999)).'.'.$ext;
            $this->thumb($url,$thumb);
            $code = 200;
        }else{
            $code = 500;
            $thumb = "";
        }
        return json(['code'=>$code,'data'=>$thumb]);
    }

    private function thumb($url,$name){
        $image = Image::open('./'.$url);
        // 按照原图的比例生成一个最大为660*360的缩略图并保存为thumb.png  居中裁剪
        $image->thumb(660,360, Image::THUMB_CENTER)->save('./'.$name);
    }



    public function uploadData(Request $request){
        $file = $request->file('file');
        $info = $file->move( 'uploads/xml/');
        if($info){
            $name = str_replace("\\","/",$info->getSaveName());
            $url = "uploads/xml/".$name ;
            $code = 200;
        }else{
            $code = 500;
            $url = "";
        }
        return json(['code'=>$code,'data'=>$url]);
    }
}