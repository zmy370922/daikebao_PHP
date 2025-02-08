<?php

namespace app\tearcher\controller;

use app\common\model\Device as DeviceModel;
use app\common\model\DeviceData as DeviceDataModel;
use app\common\model\DeviceBind as DeviceBindModel;
use app\common\model\DeviceHeartLog as DeviceHeartLogModel;
use app\common\model\TearcherBind as TearcherBindModel;
use app\common\model\DeviceSetting as DeviceSettingModel;
use app\common\model\DeviceSet as DeviceSetModel;
use app\common\model\HeadBind as HeadBindModel;
use dh2y\qrcode\QRcode;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;

/**
 * 后台-设备管理
 * @author 鱼鱼鱼
 * @since 2023年9月19日11:09:20
 * Class Device
 * @package app\admin\controller
 */
class Device extends Backend
{
    //列表
    public function lists()
    {
        try {
            $keywords = input('keywords', '');
            $cate=input('cate',0,'intval');
            $status = input('status', 1, 'intval');//状态 1：未占用 2：已注册 3：已占用
            $key = input('key', '');
            $order = input('order', '');
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $dids=TearcherBindModel::where('cate','=',$cate)->column('did');
            $map=[];
            if(count($dids)>0){
                $map[]=['id','in',$dids];
            }else{
                $map[]=['id','=',0];
            }

            if ($keywords) {
                $map[] = ['client', 'like', '%' . $keywords . '%'];
            }

            if ($status) {
                $map[] = ['status', '=', $status];
            }
            $map[] = ['is_del', '=', 1];

            //排序
            if ($key) {
                if ($order == 'descending') {
                    $order_by = ["$key" => 'desc'];
                } else {
                    $order_by = ["$key" => 'asc'];
                }
            } else {
                $order_by = ['add_time' => 'desc'];
            }

            $list = DeviceModel::with('tearcher')->where($map)->order($order_by)->limit(($page - 1) * $num, $num)->select();
            $code=new QRcode();
            $http=Config::get('http');
            foreach ($list as &$value) {
                //红心累计
                //$value['red'] = DeviceDataModel::where(['device_id' => $value['id'], 'heart' => 1])->count();
                //红心换购
                //$value['red_buy'] = OrderModel::where(['device_id' => $value['id']])->sum('red');
                //当前红心 = 红心累计 -  红心换购
                $value['red_current'] = $value['red'] - $value['red_buy'];
                $path_image ='uploads/qrcode/'.$value['client'].'.png';
                if(file_exists($path_image)){
                    $value['qrcode'] = Request::instance()->domain().'/'.$path_image;
                }else{
                    $value['qrcode'] =  $code->png($http['qr_url'].'index/device?client='.$value['client'],'uploads/qrcode/'.$value['client'].'.png', 6)->entry();
                }

                //绑定微信
                $bind_count = DeviceBindModel::where(['device_id' => $value['id'], 'status' => 1])->count();
                $value['bind_count']=$bind_count;
                if ($bind_count > 0) {
                    $value['is_band'] = 1;
                } else {
                    $value['is_band'] = 2;
                }
                if( $value['online']){

//                    $value['online'] = gmdate('H:i:s',  intval($value['online']));
                    $value['online'] = timesecond(intval($value['online']));
                }else{
                    $value['online'] ='00:00:00';
                }


            }
            $data = ['list' => $list];
            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

      //一键清除设备绑定
    public function device_bind_del(){
        $id = input('id', 0, 'intval');//ID
        DeviceModel::where('id','=',$id)->update(['pid'=>0,'nickname'=>'','red'=>0,'red_buy'=>0]);
        //清除红心日志
        DeviceHeartLogModel::where('device_id','=',$id)->delete();
        DeviceBindModel::where('device_id','=',$id)->delete();
        DeviceDataModel::where('device_id','=',$id)->delete();
        return message("修改成功", true);

    }
    //清空设备数据
    public function del_data()
    {
        try {
            $id = input('id', 0, 'intval');

            if (!$id) {
                return message("参数错误", false);
            }
            DeviceDataModel::where('device_id','=',$id)->delete();
            DeviceModel::where('id','=',$id)->update(['red'=>0,'red_buy'=>0]);
            //清除红心日志
            DeviceHeartLogModel::where('device_id','=',$id)->delete();
             return message("清除成功", true);

        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
    //信息
    public function info()
    {
        try {
            $id = input('id', 0, 'intval');

            if (!$id) {
                return message("参数错误", false);
            }
            $info = DeviceModel::where(['id' => $id])->find();
            return message("获取成功", true, $info, 200);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //移除设备
    public function del()
    {
        try {
            $id = input('id', 0, 'intval');//设备id

            if (!$id) {
                return message("参数错误", false);
            }
            if ($status = TearcherBindModel::where(['did' => $id,'tid'=>$this->userId])->delete()) {
                HeadBindModel::where(['did' => $id])->update(['status'=>1]);
                DeviceModel::where('id','=',$id)->update(['pid'=>0,'nickname'=>'']);
                DeviceBindModel::where('device_id','=',$id)->delete();
                DeviceDataModel::where('device_id','=',$id)->delete();
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }


  //修改设备名

    public function change_name(){

        try {
            $id = input('id', 0, 'intval');
            $nickname = input('nickname', '');

            if (!$id||!$nickname) {
                return message("参数错误", false);
            }

            if (DeviceModel::where(['id' => $id])->update(['nickname'=>$nickname])) {
                return message("操作成功", true);
            } else {
                return message("操作失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }

    }

    //设备基础数据
    public function device_set(){
        $device_id = input('device_id', '');//设备ID
        if (!$device_id) {
            return message("参数错误", false);
        }
        $info=DeviceSetModel::where('device_id','=',$device_id)->find();
        if($info){
            return message("获取成功", true,$info);
        }else{
            return message("没有找到该设备", false);
        }


    }
    //设备基础数据修改
    public function device_update(){
        $pose_active=input('pose_active','0','intval');
        $poseactive= gmdate('His',$pose_active);
        $good_time=input('good_time','0','intval');
        $goodtime= gmdate('His',$good_time*60);
        $bad_time=input('bad_time','0','intval');
        $badtime= gmdate('His',$bad_time);
        $eye_time=input('eye_time','0','intval');
        $eyetime= gmdate('His',$eye_time*60);
        $eye_active=input('eye_active','0','intval');
        $eyeactive= gmdate('His',$eye_active*60);
        $body_time=input('body_time','0','intval');
        $bodytime= gmdate('His',$body_time*60);
        $body_active=input('body_active','0','intval');
        $bodyactive= gmdate('His',$body_active*60);
        $possensit=input('possensit','0','intval');
        $goodsensit=input('goodsensit','0','intval');
        $device_id = input('device_id', '');//设备ID
        if (!$device_id) {
            return message("参数错误", false);
        }

        $arr1=array(
            'device_id'=>$device_id,
            'poseactive'=>$poseactive,
            'pose_active'=>$pose_active,
            'goodtime'=>$goodtime,
            'good_time'=>$good_time,
            'badtime'=>$badtime,
            'bad_time'=>$bad_time,
            'eyetime'=>$eyetime,
            'eye_time'=>$eye_time,
            'eyeactive'=>$eyeactive,
            'eye_active'=>$eye_active,
            'bodytime'=>$bodytime,
            'body_time'=>$body_time,
            'bodyactive'=>$bodyactive,
            'body_active'=>$body_active,
            'possensit'=>$possensit,
            'goodsensit'=>$goodsensit,

        );
        if(DeviceSetModel::where('device_id','=',$device_id)->update($arr1)){
            //添加设备修改记录
            $arr1['date']=date('Y-m-d H:i:s',time());
            $arr1['add_time']=time();
            $arr1['status']=1;
            $arr1['type']=2;
            DeviceSettingModel::create($arr1);
            //发送设备指令
            $http=Config::get('http');
            $url=$http['url'].'setting/IssueInstructions';
            $data=array(
                'deviceId'=>$device_id,
                'poseactive'=>$poseactive,
                'review'=>0,
                'goodtime'=>$goodtime,
                'badtime'=>$badtime,
                'eyetime'=>$eyetime,
                'eyeactive'=>$eyeactive,
                'bodytime'=>$bodytime,
                'bodyactive'=>$bodyactive,
                'possensit'=>$possensit,
                'goodsensit'=>$goodsensit,
            );
            $data=json_encode($data);
            $result= phpCurl($url,$data);
            zlog('admin_result',$result);
            return message("修改成功", true,$result);

        }else{
            return message("参数已设置", true);
        }

    }


    //实时统计
    public function real_data()
    {
        try {
            $device_id = input('device_id', '');//设备ID
            $type= input('type', '1','intval');//1是今日，2是近7天，3是近四周

            if($type==2){
                $e_time = strtotime(date('Y-m-d 23:59:59', strtotime('0 days')));
                $s_time = strtotime(date('Y-m-d 00:00:00', strtotime('-6 days')));
            }elseif ($type==3){
                $e_time = strtotime(date('Y-m-d 23:59:59', strtotime('0 days')));
                $s_time = strtotime(date('Y-m-d 00:00:00', strtotime('-27 days')));
            }else{
                $e_time = time();
                $s_time =strtotime(date("Y-m-d 00:00:00"));
            }

            $map = [];
            $map[] = ['device_id', '=', $device_id];

            $this_week_start=mktime(0, 0 , 0,date("m"),date("d")-date("N")+1,date("Y"));
            $this_week_end=mktime(23,59,59,date("m"),date("d")-date("N")+7,date("Y"));

            //本周红心总数
            $heart_count = DeviceHeartLogModel::where($map)->where('addtime', "between", "$this_week_start,$this_week_end")->where('status','=',1)->sum('number');



            $map[] = ['add_time', "between", "$s_time,$e_time"];
            //今日数据汇总
//            $list= DeviceDataModel::where($map)->where('add_time', "between", "$s_time,$e_time")->order(['add_time'=>'asc'])->select();

            //今日总数据
            $count= DeviceDataModel::where($map)->count();


            $map[] = ['people', '=', 1];
            //专注总数
            $focus_count = DeviceDataModel::where($map)->where(['focus' => 1])->count();
            //晃动总数
            $pshake_count = DeviceDataModel::where($map)->where(['pshake' => 1])->count();
            //标准总数
            $pgood_count = DeviceDataModel::where($map)->where(['pgood' => 1])->count();
            //驼背总数
            $pback_count = DeviceDataModel::where($map)->where(['pback' => 1])->count();
            //左倾总数
            $pleftdev_count = DeviceDataModel::where($map)->where(['pleftdev' => 1])->count();
            //右倾总数
            $prightdev_count = DeviceDataModel::where($map)->where(['prightdev' => 1])->count();
            //左旋总数
            $pleftrota_count = DeviceDataModel::where($map)->where(['pleftrota' => 1])->count();
            //右旋总数
            $prightrota_count = DeviceDataModel::where($map)->where(['prightrota' => 1])->count();
            $arr = [$pback_count, $pleftdev_count,$prightdev_count];
            $maxIndex = findMaxIndex($arr);
            if($maxIndex==0){
                $zitai='驼背';
            }elseif ($maxIndex==1){
                $zitai='左倾';
            }elseif ($maxIndex==2){
                $zitai='右倾';
            }
            //有人总数据
            $count1= DeviceDataModel::where($map)->count();
            //四种姿态的总和
            $count2=$pback_count+$pleftdev_count+$prightdev_count+$pgood_count;
            $data = [];
            if($count2>0){

                $data['pgood']=sprintf("%.2f", ($pgood_count/$count2));
                if($data['pgood']<0.4){
                    $data['pgood_msg']='今日姿态不是很标准，'.$zitai.'姿态占比比较大，需要注意纠正，请多辅助一下。';
                }elseif($data['pgood']>0.7){
                    $data['pgood_msg']='今日姿态良好，保持的也非常不错，点赞。';
                }else{
                    $data['pgood_msg']='今日姿态渐入佳境，继续保持练习，'.$zitai.'姿态占比比较大，需要注意纠正。';
                }
            }else{

                $data['pgood']=0;
                $data['pgood_msg']='暂无数据';
            }
//            if(count($list)>0){
//                $time= date('H:i',  $list[0]['add_time']);
//            }else{
//                $time='00:00';
//            }
            $data['heart_count'] = $heart_count;
            $data['focus_count'] = $focus_count;
            $data['pshake_count'] = $pshake_count;
            $data['pgood_count'] = $pgood_count;
            $data['pback_count'] = $pback_count;
            $data['pleftdev_count'] = $pleftdev_count;
            $data['prightdev_count'] = $prightdev_count;
            $data['pleftrota_count'] = $pleftrota_count;
            $data['prightrota_count'] = $prightrota_count;
//            $data['list'] = $list;
            $data['count'] = $count;
            $data['count1'] = $count1;
//            $data['time'] = $time;
            $data['count_min'] =floor($count1/6);
            $data['min_to_hours'] =toHours($data['count_min']);
            $data['focus_min'] =floor($focus_count/6);
            $data['focus_min_hours'] =toHours($data['focus_min']);
            $data['count2'] = $count2;
            $data['count2_min'] =floor($count2/6);
            $data['count2_min_hours'] =toHours($data['count2_min']);
            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }




}
