<?php

namespace app\admin\controller;

use app\common\model\Tearcher as TearcherModel;
use app\common\model\Device as DeviceModel;
use app\common\model\HeadBind as HeadBindModel;
use app\common\model\TearcherBind as TearcherBindModel;

/**
 * 后台-学校管理
 * Class Menu
 * @package app\admin\controller
 */
class Tearcher extends Backend
{
    //列表
    public function menu_list()
    {
        try {
            //名称
            $menu_name = input('name', '');
            //名称
            $map = [];
            if ($menu_name) {
                $menu_name = trim($menu_name);
                $map[] = ['nickname', 'like', '%' . $menu_name . '%'];
            }
            $map[] = ['pid', '=', 0];


            $list = TearcherModel::with('children')->where($map)->order(['id' => 'desc'])->select();

            return message("获取成功", true, $list, 200);
        } catch (\Exception $e) {
            return message('错误：' . $e->getMessage(), false);
        }
    }

    //添加
    public function add()
    {
        try {
            $pid= input('pid', 0, 'intval');//上级id
            $cate= input('cate', 0, 'intval');
            $nickname= input('nickname');
            $phone= input('phone');
            $username= input('username');
            $password= input('password');
            $data = [];

            $data['pid'] = $pid;
            $data['cate'] = $cate;
            $data['nickname'] = $nickname;
            $data['phone'] = $phone;
            $data['username'] = $username;
            $data['password'] = md5($password);
            if($pid>0){
                $data['type'] =2;
            }else{
                $data['type'] = 1;
            }

            $data['addtime'] = time();

            if(TearcherModel::where('username','=',$username)->find()){
                return message("该账号已存在", false);
            }
            if ($new = TearcherModel::create($data)) {
                action_log($this->userId, 'tearcher', '老师模块操作', '添加老师校长账号', $new['id']);
                return message("添加成功", true, $new);
            } else {
                return message("添加失败", false);
            }
        } catch (\Exception $e) {
            return message('错误：' . $e->getMessage(), false);
        }
    }


    //编辑
    public function edit()
    {
        try {
            $pid= input('pid', 0, 'intval');//上级id
            $id= input('id', 0, 'intval');//上级id
            $cate= input('cate', 0, 'intval');
            $nickname= input('nickname');
            $phone= input('phone');
            $username= input('username');
            $password= input('password');

            if (!$id) {
                return message("参数错误", false);
            }


            $data = [];

            $data['pid'] = $pid;
            $data['cate'] = $cate;
            $data['nickname'] = $nickname;
            $data['phone'] = $phone;
            $data['username'] = $username;
            if($password){
                $data['password'] = md5($password);
            }
            if(TearcherModel::where('username','=',$username)->where('id','<>',$id)->find()){
                return message("该账号已存在", false);
            }
            if ($res = TearcherModel::where(['id' => $id])->update($data)) {
                action_log($this->userId, 'tearcher', '老师模块操作', '修改老师校长账号', $id);
                return message("编辑成功", true);
            } else {
                return message("编辑失败", false);
            }
        } catch (\Exception $e) {
            return message('错误：' . $e->getMessage(), false);
        }
    }

    //删除
    public function del()
    {
        try {
            $id = input('id', 0, 'intval');

            if (!$id) {
                return message("参数错误", false);
            }
           $count= TearcherModel::where(['pid' => $id])->count();
            if($count>0){
                return message("请删除下级账号之后，再来进行删除操作", false);
            }


            if ($status = TearcherModel::where(['id' => $id])->delete()) {
                action_log($this->userId, 'tearcher', '老师模块操作', '修改老师校长账号', $id);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
    //解锁设备/封锁设备
    public function change_type()
    {
        try {
            $id = input('id', 0, 'intval');
            $status = input('status', 0, 'intval');

            if (!$id||!$status) {
                return message("参数错误", false);
            }

            if (TearcherModel::where(['id' => $id])->update(['status'=>$status])) {
                if($status==1){
                    $title='解锁账号';
                }else{
                    $title='封锁账号';
                }
                action_log($this->userId, 'tearcher', '账号操作', $title, $id);
                return message("操作成功", true);
            } else {
                return message("操作失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
    //获取绑定的设备

    public function bind_index(){

        try {
            $tid = input('tid', 0, 'intval');
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            if (!$tid) {
                return message("参数错误", false);
            }
            $dids=HeadBindModel::where('tid','=',$tid)->column('did');
            $map=[];
            if(count($dids)>0){
                $map[]=['id','in',$dids];
            }else{
                $map[]=['id','=',0];
            }
            $keywords = input('keywords', '');//商品搜索
            if ($keywords) {
                $map[] = ['client', 'like', '%' . $keywords . '%'];
            }
            $map[] = ['is_del', '=', 1];
            $order_by = ['add_time' => 'desc'];
            $list = DeviceModel::where($map)->order($order_by)->limit(($page - 1) * $num, $num)->select();
            //总数
            $count = DeviceModel::where($map)->count();
            $data = ['list' => $list, 'total' => $count];
            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }

    }
    //平台未绑定的设备
    public function unbind_index(){
        try {
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');
            $dids=HeadBindModel::column('did');
            $map=[];
            if(count($dids)>0){
                $map[]=['id','notin',$dids];
            }else{
                $map[]=['id','>',0];
            }
            $map[] = ['is_del', '=', 1];
            $keywords = input('keywords', '');//商品搜索
            if ($keywords) {
                $map[] = ['client', 'like', '%' . $keywords . '%'];
            }

            $order_by = ['add_time' => 'desc'];
            $list = DeviceModel::where($map)->order($order_by)->limit(($page - 1) * $num, $num)->select();
            //总数
            $count = DeviceModel::where($map)->count();
            $data = ['list' => $list, 'total' => $count];

            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //给校长绑定设备
    public function do_bind(){
        $tid = input('tid', 0, 'intval');
        $did=input('did',0,'intval');
        if($tid==0||$did==0){
            return message("参数错误", false);
        }
        if(HeadBindModel::where('did','=',$did)->find()){
            return message("该设备已绑定", false);
        }
        $data=array(
            'tid'=>$tid,
            'did'=>$did,
            'addtime'=>time(),
        );
        if ($new = HeadBindModel::create($data)) {
            action_log($this->userId, 'head_bind', '设备模块操作', '添加设备给校长', $new['id']);
            return message("添加成功", true, $new);
        } else {
            return message("添加失败", false);
        }


    }
    //删除解绑设备

    public function del_bind(){
        $did=input('did',0,'intval');
        if($did==0){
            return message("参数错误", false);
        }
        HeadBindModel::where('did','=',$did)->delete();
        TearcherBindModel::where('did','=',$did)->delete();
        return message("删除成功", true);
    }







}
