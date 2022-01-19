<?php
namespace app\admin_login\model;
use think\Model;

/**
 * 单页内容管理
 */
class Category extends Model{

    protected $table = 'web_category';

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
    public function getTreeFirst(){
        return $this::where('level',1)->where('show',1)->select();
    }

    public function getFullTree(){
        $tree = [];
        $res = $this::where('level',1)->where('show',1)->order('num','desc')->select();
        foreach ($res as $k=>$v){
            $tree[$k] = $v;
            $sub = $this::where('p_id',$v['id'])->where('show',1)->order('num','desc')->select();
            if($sub){
                $subs = [];
                foreach ($sub as $kk=>$vv){
                    $subs[] = $vv;
                    $subst = $this::where('p_id',$vv['id'])->where('show',1)->order('num','desc')->select();
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

    public function getFullTrees(){
        $tree = [];
        $res = $this::where('level',1)->where('show',1)->select();
        foreach ($res as $k=>$v){
            $tree[] = $v;
            $sub = $this::where('p_id',$v['id'])->where('show',1)->select();
            if($sub){
                foreach ($sub as $kk=>$vv){
                    $tree[] = $vv;
                    $subst = $this::where('p_id',$vv['id'])->where('show',1)->select();
                    if($subst){
                        foreach ($subst as $vvv){
                            $tree[] = $vvv;
                        }
                    }
                }
            }
        }
        return $tree;
    }
}