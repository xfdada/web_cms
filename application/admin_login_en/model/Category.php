<?php
namespace app\admin_login_en\model;
use think\Model;

/**
 * 单页内容管理
 */
class Category extends Model{

    protected $table = 'web_category_en';

    public function getTree(){
        $tree = [];
        $res = $this::where('level',1)->select();
        foreach ($res as $k=>$v){
            $tree[$k] = $v;
            $sub = $this::where('p_id',$v['id'])->select();
            if($sub){
                  $subs = [];
                  foreach ($sub as $vv){
                      $subs[] = $vv;
                  }
                $tree[$k]['sub'] = $subs;
            }
        }
        return $tree;
    }

    public function getFullTree(){
        $tree = [];
        $res = $this::where('level',1)->select();
        foreach ($res as $k=>$v){
            $tree[$k] = $v;
            $sub = $this::where('p_id',$v['id'])->select();
            if($sub){
                $subs = [];
                foreach ($sub as $kk=>$vv){
                    $subs[] = $vv;
                    $subst = $this::where('p_id',$vv['id'])->select();
                    $subss = [];
                    if($subst){
                        foreach ($subst as $vvv){
                            $subss[] = $vvv;
                        }
                        $subs[$kk]['sub'] = $subss;
                    }
                }
                $tree[$k]['sub'] = $subs;
            }
        }
        return $tree;
    }
}