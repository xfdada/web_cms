<?php

namespace app;
use think\App;
use think\Controller;
use think\facade\Session;

class Base extends Controller
{

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        if(empty(Session::get('admin'))){
            $this->redirect('/admin_login/login/login');
        }
    }


    public function deldir($dir) {
        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->deldir($fullpath);
                }
            }
        }
        closedir($dh);

        return true;
//        //删除当前文件夹：
//        if(rmdir($dir)) {
//            return true;
//        } else {
//            return false;
//        }
    }
}