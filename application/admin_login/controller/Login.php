<?php

namespace app\admin_login\controller;

use think\Controller;
use think\captcha\Captcha;
use think\Db;
use think\facade\Session;

/**
 * 后台登陆控制器
 */
class Login extends Controller
{
    public function login(){

        return view('view/login');
    }
    /**
     * getCode 获取验证码
     * @return \think\Response
     */
    public function getCode(){
        $config =    [
            'fontSize'    =>    30,
            'length'      =>    4,
            'fontttf'     =>    "4.ttf"
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

    public function logout(){
        Session::delete('admin');
        $this->redirect('/admin_login/login/login');
    }

    public function go_in(){
        $codes = input('code','');
        $captcha = new Captcha();
        $code=500;
        $err_time= Session::get('err_time');
        if($err_time>time()){
            $msg = "错误次数过多，请在".date("Y-m-d H:i:s",Session::get('err_time'))."后进行登陆";
            Session::set('err_count',1);
            return json(['code'=>$code,'msg'=>$msg]);
        }
        if(!$captcha->check($codes)){
            $msg = "验证码错误";
        }else{
            $name = input('name','');
            $pwd = input('pwd','');
            if ($name==""||$pwd==""){
                $msg = "用户名或密码不能为空";
            }else{
                $user = Db::table('web_admin')->where("name",$name)->find();
                if(isset($user) && password_verify($pwd,$user['pwd'])){
                    Session::set('admin',$user);
                    $code=200;
                    $msg = "登陆成功";
                }else{
                    $err_count = Session::get('err_count');
                    if(empty($err_count)){
                        Session::set('err_count',1);
                    }else{
                        $err_count = $err_count+1;
                        Session::set('err_count',$err_count);
                    }
                    if ($err_count>3){
                        Session::set('err_time',time()+600);
                    }
                    $msg = "用户名或密码错误";
                }
            }
        }
        return json(['code'=>$code,'msg'=>$msg]);
    }
}