<?php

namespace app\tearcher\controller;

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

            $map[] = ['pid', '=',$this->userId];


            $list = TearcherModel::where($map)->order(['id' => 'desc'])->select();

            return message("获取成功", true, $list, 200);
        } catch (\Exception $e) {
            return message('错误：' . $e->getMessage(), false);
        }
    }

    //添加
    public function add()
    {
        try {
            $pid=$this->userId;//上级id
            $userinfo=$this->userInfo;
            $count=TearcherModel::where('pid','=',$pid)->where('type','=',2)->count();
            if($userinfo['cate']==1){
                if($count>=1){
                    return message("创建老师的数量已满", false);
                }
            }elseif ($userinfo['cate']==2){
                if($count>=2){
                    return message("创建老师的数量已满", false);
                }
            }elseif ($userinfo['cate']==3){
                if($count>=5){
                    return message("创建老师的数量已满", false);
                }
            }elseif ($userinfo['cate']==4){
                if($count>=10){
                    return message("创建老师的数量已满", false);
                }
            }elseif ($userinfo['cate']==5){
                if($count>=10){
                    return message("创建老师的数量已满", false);
                }
            }
            $nickname= input('nickname');
            $phone= input('phone');
            $username= input('username');
            $password= input('password');
            $data = [];
            $data['pid'] = $pid;
            $data['nickname'] = $nickname;
            $data['phone'] = $phone;
            $data['username'] = $username;
            $data['password'] = md5($password);
            $data['type'] =2;

            $data['addtime'] = time();
            if(TearcherModel::where('username','=',$username)->find()){
                return message("该账号已存在", false);
            }
            if ($new = TearcherModel::create($data)) {
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

            $id= input('id', 0, 'intval');//
            $nickname= input('nickname');
            $phone= input('phone');
            $username= input('username');
            $password= input('password');

            if (!$id) {
                return message("参数错误", false);
            }
            $data = [];
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
                return message("操作成功", true);
            } else {
                return message("操作失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //设备管理
    //获取绑定的设备

    public function bind_index(){

        try {
            $tid =$this->userId;
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $list=HeadBindModel::with('device')->where('tid','=',$tid)->limit(($page - 1) * $num, $num)->select();

            //总数
            $count = HeadBindModel::where('tid','=',$tid)->count();
            $data = ['list' => $list, 'total' => $count];
            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }

    }




}
