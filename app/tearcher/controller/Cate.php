<?php

namespace app\tearcher\controller;

use app\common\model\Device as DeviceModel;
use app\common\model\TearcherCate as TearcherCateModel;
use app\common\model\TearcherBind as TearcherBindModel;
use app\common\model\Tearcher as TearcherModel;
use app\common\model\HeadBind as HeadBindModel;

/**
 * 后台-流量套餐
 * @author 鱼鱼鱼
 * @since 2023年9月11日13:39:16
 * Class Recharge
 * @package app\admin\controller
 */
class Cate extends Backend
{
    //列表
    public function index()
    {
        try {
            $userinfo=$this->userInfo;
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];

            if($userinfo['type']==1){
                $uids=TearcherModel::where('pid','=',$this->userId)->where('type','=',2)->column('id');
                if(count($uids)>0){
                    $map[]=['tid','in',$uids];
                }else{
                    $map[]=['tid','=',0];
                }
            }else{
                $map[]=['tid','=',$this->userId];
            }
            $list = TearcherCateModel::with('tearcher')->where($map)->order(['id' => 'asc'])->limit(($page - 1) * $num, $num)->select();
            //总数
            $count = TearcherCateModel::where($map)->count();
            $data = ['list' => $list, 'total' => $count];

            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //添加
    public function add()
    {
        try {
            $title = input('title', '');

            if (!$title) {
                return message("参数错误", false);
            }

            $data = [];
            $data['title'] = $title;
            $data['date_time'] =strtotime(input('meet'));
            $data['tid'] = $this->userId;
            $data['addtime'] = time();
            if ($new = TearcherCateModel::create($data)) {
                return message("添加成功", true, $new, 200);
            } else {
                return message("添加失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }


    //编辑
    public function edit()
    {
        try {
            $id = input('id', 0, 'intval');
            $title = input('title', '');
            if (!$id || !$title ) {
                return message("参数错误", false);
            }

            $data = [];
            $data['title'] = $title;
            $data['date_time'] =strtotime(input('meet'));
            if ($res = TearcherCateModel::where(['id' => $id])->update($data)) {
                return message("编辑成功", true);
            } else {
                return message("编辑失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
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
            $count= TearcherBindModel::where(['cate' => $id])->count();
            if($count>0){
                return message("请删除班级设备之后，再来进行删除操作", false);
            }
            if ($status = TearcherCateModel::where(['id' => $id])->delete()) {

                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    public function unbind_index(){
        try {
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');
            $userinfo=$this->userInfo;
            $dids=HeadBindModel::where('tid','=',$userinfo['pid'])->where('status','=',1)->column('did');
            $map=[];
            if(count($dids)>0){
                $map[]=['id','in',$dids];
            }else{
                $map[]=['id','=',0];
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
        $tid = $this->userId;
        $userinfo=$this->userInfo;
        $did=input('did',0,'intval');
        $cate=input('cate',0,'intval');
        if($tid==0||$did==0||$cate==0){
            return message("参数错误", false);
        }
        $device=HeadBindModel::where('did','=',$did)->where('tid','=',$userinfo['pid'])->find();
        if(!$device){
            return message("设备不存在", false);
        }
        if(HeadBindModel::where('did','=',$did)->where('tid','=',$userinfo['pid'])->where('status','=',2)->find()){
            return message("该设备已绑定", false);
        }
        $data=array(
            'tid'=>$tid,
            'did'=>$did,
            'cate'=>$cate,
            'addtime'=>time(),
        );
        if ($new = TearcherBindModel::create($data)) {
            HeadBindModel::where('id','=',$device['id'])->update(['status'=>2]);
            return message("添加成功", true, $new);
        } else {
            return message("添加失败", false);
        }


    }

}
