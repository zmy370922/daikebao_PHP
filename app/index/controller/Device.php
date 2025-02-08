<?php
/**
 * Created by
 * User: 鱼鱼鱼
 * Date: 2023年6月25日
 * Time: 16:02:54
 * desc: 设备相关
 */

namespace app\index\controller;

use app\common\model\DeviceBind as DeviceBindModel;
use app\common\model\DeviceData as DeviceDataModel;
use app\common\model\DeviceHeartLog;
use app\common\model\DeviceReport as DeviceReportModel;
use app\common\model\DeviceSetting as DeviceSettingModel;
use app\common\model\Device as DeviceModel;
use app\common\model\DeviceSet as DeviceSetModel;
use app\common\model\DeviceHeartLog as DeviceHeartLogModel;
use app\common\model\NewDeviceData as NewDeviceDataModel;
use app\common\model\NewDeviceSetting as NewDeviceSettingModel;
use app\common\model\NewDeviceSet as NewDeviceSetModel;

use app\common\model\Version as VersionModel;
use think\facade\Config;
use think\facade\Db;

class Device extends Backend
{

    public function san_detail(){
        $san_pay = input('san_pay', '');//
        if (!$san_pay) {
            return message("参数错误", false);
        }
        $san_array= explode('=',$san_pay);
        if($san_array["0"]!='client'){
            return message("绑定路径不正确", false);
        }
        if(!$san_array["1"]){
            return message("参数错误", false);
        }

        $map=[];
        $map[]=['client','=',$san_array["1"]];
        $map[]=['is_del','=',1];
        $info = DeviceModel::where($map)->find();
        if($info){
            return message("获取成功", true,$info);
        }else{
            return message("设备不存在", false);
        }

    }
    //实时状态
    public function real_status()
    {
        try {
            $device_id = input('device_id', '');//设备ID
            if (!$device_id) {
                return message("参数错误", false);
            }

            $map = [];
            $map[] = ['device_id', '=', $device_id];
//            $map[] = ['people', '=', 1];

            $device=DeviceModel::where('id','=',$device_id)->find();
            if($device){
                if($device['status']==1||$device['isonline']==0){
                    $info['type']=0;
                }else{
                    $info = DeviceDataModel::where($map)->order(['id' => 'desc'])->find();
                    if($info['people']==1){
                        $info['type']=9;
                    }else{
                        $info['type']=0;
                    }
                }



                return message("获取成功", true, $info);
            }else{
                return message("设备不存在", false);
            }


        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //实时状态评价
    public function real_opera()
    {
        try {
            $id = input('id', '');//ID
            $review = input('review', 0, 'intval');//评价，1表示表扬，2表示鼓励，3表示吐槽
            $info=DeviceDataModel::where('id', '=', $id)->find();

            $map = [];
            $map[] = ['id', '=', $id];

            $device_date=DeviceSetModel::where('device_id','=',$info['device_id'])->find();

            //发送设备指令
            $http=Config::get('http');
            $url=$http['url'].'setting/IssueInstructions';
            $data=array(
                'deviceId'=>$device_date['device_id'],
                'poseactive'=>$device_date['poseactive'],
                'review'=>$review,
                'goodtime'=>$device_date['goodtime'],
                'badtime'=>$device_date['badtime'],
                'eyetime'=>$device_date['eyetime'],
                'eyeactive'=>$device_date['eyeactive'],
                'bodytime'=>$device_date['bodytime'],
                'bodyactive'=>$device_date['bodyactive'],
                'possensit'=>$device_date['possensit'],
                'goodsensit'=>$device_date['goodsensit'],
            );

            $data=json_encode($data);
            zlog('data',$data);
            $result= phpCurl($url,$data);
            DeviceDataModel::where($map)->update(['review' => $review]);
            zlog('result',$result);
            return message("评价成功", true);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //实时统计
    public function real_data()
    {
        try {
            $device_id = input('device_id', '');//设备ID
            $e_time = time();
            $s_time =strtotime(date("Y-m-d 00:00:00"));

            $w_time =$e_time-86400*7;
            $map = [];
            $map[] = ['device_id', '=', $device_id];

            $this_week_start=mktime(0, 0 , 0,date("m"),date("d")-date("N")+1,date("Y"));
            $this_week_end=mktime(23,59,59,date("m"),date("d")-date("N")+7,date("Y"));

            //本周红心总数
            $heart_count = DeviceHeartLogModel::where($map)->where('addtime', "between", "$this_week_start,$this_week_end")->where('status','=',1)->sum('number');

            $map[] = ['add_time', "between", "$s_time,$e_time"];
            //今日数据汇总
            $list= DeviceDataModel::where($map)->where('add_time', "between", "$s_time,$e_time")->order(['add_time'=>'asc'])->select();

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
            if(count($list)>0){
                $time= date('H:i',  $list[0]['add_time']);
            }else{
                $time='00:00';
            }


            $data['heart_count'] = $heart_count;
            $data['focus_count'] = $focus_count;
            $data['pshake_count'] = $pshake_count;
            $data['pgood_count'] = $pgood_count;
            $data['pback_count'] = $pback_count;
            $data['pleftdev_count'] = $pleftdev_count;
            $data['prightdev_count'] = $prightdev_count;
            $data['pleftrota_count'] = $pleftrota_count;
            $data['prightrota_count'] = $prightrota_count;
            $data['list'] = $list;
            $data['count'] = $count;
            $data['count1'] = $count1;
            $data['time'] = $time;
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


    //设备信息
    public function info ()
    {
        try {
            $device_id = input('device_id', '');//设备id
            if(!$device_id){
                return message("参数错误", false);
            }else{
                $info=DeviceModel::where('device_id','=',$device_id)->where('is_del','=',1)->find();
            }

            return message("获取成功", true,$info);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //设备绑定
    public function bind()
    {
        try {
            $device_id = input('device_id', '');//设备ID
            if (!$device_id) {
                return message("参数错误", false);
            }
            $device_info=DeviceModel::where('id','=',$device_id)->where('is_del','=','1')->find();
            if(!$device_info){
                return message("设备不存在", false);
            }
            $map = [];
            $map[] = ['uid', '=', $this->userId];
            $map[] = ['device_id', '=', $device_id];
            $info=DeviceBindModel::where($map)->find();
            if($info){
                $list=DeviceBindModel::where($map)->find();
                return message("您已经绑定该设备了", true, $list);
//                if($info['status']==1){
//                    return message("您已经绑定该设备了", true, $list);
//                }elseif($info['status']==2){
//                    DeviceBindModel::where('id', '=', $info['id'])->update(['status'=>3]);
//
//                    return message("绑定成功,等待主绑定人审核", true, $list);
//                }elseif($info['status']==3){
//                    return message("等待主绑定人审核", true, $list);
//                }

            }else{
                $data=array(
                    'uid'=>$this->userId,
                    'device_id'=>$device_id,
                    'bind_time'=>time(),
                    'name'=>get_nickname(),
                );
                DeviceBindModel::where('uid', '=', $this->userId)->update(['type'=>2]);
                $data['pid']=$this->userId;
                $data['status']=1;
                $data['type']=1;
                DeviceModel::where('id','=',$device_info['id'])->update(['pid'=>$this->userId]);
                DeviceBindModel::create($data);
                $list=DeviceBindModel::where($map)->find();
                return message("绑定成功", true, $list);
//                if($device_info['pid']==0){
//                    DeviceBindModel::where('uid', '=', $this->userId)->update(['type'=>2]);
//                    $data['pid']=$this->userId;
//                    $data['status']=1;
//                    $data['type']=1;
//                    DeviceModel::where('id','=',$device_info['id'])->update(['pid'=>$this->userId]);
//                    DeviceBindModel::create($data);
//                    $list=DeviceBindModel::where($map)->find();
//                    return message("绑定成功", true, $list);
//                }else{
//                    $data['pid']=$device_info['pid'];
//                    $data['status']=3;
//                    DeviceBindModel::create($data);
//                    $list=DeviceBindModel::where($map)->find();
//                    return message("绑定成功,等待主绑定人审核", true, $list);
//                }

            }

        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //设备解绑
    public function unbind()
    {
        try {
            $device_id = input('device_id', '');//设备ID
            if (!$device_id) {
                return message("参数错误", false);
            }
            $device_info=DeviceModel::where('id','=',$device_id)->where('is_del','=','1')->find();
            if(!$device_info){
                return message("设备不存在", false);
            }
            $map = [];
            $map[] = ['uid', '=', $this->userId];
            $map[] = ['device_id', '=', $device_id];
            $info=DeviceBindModel::where($map)->find();
            if($info){
                if($info['type']==1){
                    DeviceBindModel::where('uid', '=', $this->userId)->update(['type'=>2]);
                    $where = [];
                    $where[] = ['uid', '=', $this->userId];
                    $where[] = ['device_id', '<>', $device_id];
                    $where[] = ['status', '=', 1];
                    $info1=DeviceBindModel::where($where)->find();
                    if($info1){
                        DeviceBindModel::where('id','=',$info1['id'])->update(['type' => 1]);
                    }

                }
                DeviceBindModel::where('id','=',$info['id'])->delete();
                if($device_info['pid']== $this->userId){
                    $where1[] = ['device_id', '=', $device_id];
                    $where1[] = ['pid', '=', $this->userId];
                    DeviceBindModel::where($where1)->delete();
                    DeviceDataModel::where('device_id','=',$device_id)->delete();
                    DeviceModel::where("id",'=',$device_info['id'])->update(['pid'=>0,'red'=>0,'red_buy'=>0]);
                    //清除红心日志
                    DeviceHeartLog::where('device_id','=',$device_id)->delete();
                    //
                    NewDeviceDataModel::where('device_id','=',$device_id)->delete();
                    NewDeviceSetModel::where('device_id','=',$device_id)->delete();
                    NewDeviceSettingModel::where('device_id','=',$device_id)->delete();



                }

                return message("解绑成功", true);
            }else{
                return message("暂无该设备", false);
            }

        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //机器人报修
    public function report()
    {
        try {
            $device_id = input('device_id', '');//设备ID
            $reason = input('reason', '');//报修原因
            if (!$device_id || !$reason) {
                return message("参数错误", false);
            }

            $data = [];
            $data['uid'] = $this->userId;
            $data['device_id'] = $device_id;
            $data['reason'] = $reason;
            $data['add_time'] = time();
            if ($new = DeviceReportModel::create($data)) {
                return message("提交成功", true, $new);
            } else {
                return message("提交失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //设备申请列表
    public function shenqing(){
        try {
            $device_id = input('device_id', '');//设备ID
            if (!$device_id) {
                return message("参数错误", false);
            }
            $map = [];
            $map[] = ['uid', '<>', $this->userId];
            $map[] = ['pid', '=', $this->userId];
            $map[] = ['device_id', '=',$device_id];
            $map[] = ['status', 'in','1,3'];
            $list = DeviceBindModel::with(['device','user'])->where($map)->order(['bind_time' => 'desc'])->select();
            return message("获取成功", true, $list);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //设备申请处理
    public function device_status(){
        $status = input('status', '');//状态;1是成功2是失败，3是解绑
        $id = input('id', '0');//绑定的id
        if(!$status||$id==0){
            return message("参数错误", false);
        }
        $info=DeviceBindModel::where('id','=',$id)->find();
        if($info){
            if($status==1){
                DeviceBindModel::where('uid', '=', $info['uid'])->update(['status'=>2,'type'=>2]);
                DeviceBindModel::where('id','=',$id)->update(['status'=>1,'type'=>1]);
                return message("处理成功", true);

            }elseif ($status==2){
                DeviceBindModel::where('id','=',$id)->update(['status'=>2]);
                return message("处理成功", true);

            }elseif ($status==3){
                DeviceBindModel::where('id','=',$id)->delete();
                return message("处理成功", true);

            }else{
                return message("暂无操作", false);
            }
        }else{
            return message("审核失败", false);
        }



    }

    //设备绑定数量
    public function device_bind_num(){
        $device_id = input('device_id', '');//设备ID
        if (!$device_id) {
            return message("参数错误", false);
        }
        $data=[];
        $data['count']=DeviceBindModel::where('device_id','=',$device_id)->count();
        $data['count1']=DeviceBindModel::where('device_id','=',$device_id)->where('status','=',1)->count();
        $data['count2']=DeviceBindModel::where('device_id','=',$device_id)->where('status','=',2)->count();
        $data['count3']=DeviceBindModel::where('device_id','=',$device_id)->where('status','=',3)->count();
        return message("获取成功", true,$data);

    }




    //我绑定的设备列表
    public function device_list()
    {
        try {
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            $map[] = ['uid', '=', $this->userId];
//            $map[] = ['status', '=', 1];


            $list = DeviceBindModel::with(['device'])->where($map)->order(['bind_time' => 'desc'])->limit(($page - 1) * $num, $num)->select();

            return message("获取成功", true, $list);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //我绑定的设备设置别名
    public function device_alias()
    {
        try {
            $id = input('id', 0, 'intval');//列表ID
            $name = input('name', '');
            if (!$id || !$name) {
                return message("参数错误", false);
            }

            $map = [];
            $map[] = ['id', '=', $id];
            $map[] = ['uid', '=', $this->userId];

            $status = DeviceBindModel::where($map)->update(['name' => $name]);
            if ($status !== false) {
                return message("设置成功", true);
            } else {
                return message("设置失败", false);
            }

        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
    //推荐数据总评
    public function zongping(){
        $device_id = input('device_id', '');//设备ID
        if (!$device_id) {
            return message("参数错误", false);
        }
        $e_time = time();
        $s_time = strtotime(date('Y-m-d 00:0:00', strtotime('-7 days')));
        $map=[];
        $map[]=['device_id','=',$device_id];
        $map[]=['people','=',1];
        $map[] = ['add_time', "between", "$s_time,$e_time"];
        $count=DeviceDataModel::where($map)->count();//总数
        if($count>0){
            //专注数据
            $focus=DeviceDataModel::where($map)->where('focus','=',1)->count();
            $data['focus']=sprintf("%.2f", ($focus/$count));
            if($data['focus']<0.4){
                $data['focus_msg']='专注力还需加强练习，可通过良好体态练习可辅助练习专注力';
            }elseif($data['focus']>0.7){
                $data['focus_msg']='学习状态是非常专注的，点赞。';
            }else{
                $data['focus_msg']=' 专注力保持是不错的，继续保持努力。';
            }
            //标准
            $pgood=DeviceDataModel::where($map)->where('pgood','=',1)->count();
            $pback=DeviceDataModel::where($map)->where('pback','=',1)->count();
            $pleftdev=DeviceDataModel::where($map)->where('pleftdev','=',1)->count();
            $prightdev=DeviceDataModel::where($map)->where('prightdev','=',1)->count();
            $pshake=DeviceDataModel::where($map)->where('pshake','=',1)->count();

            $arr = [$pback, $pleftdev,$prightdev,$pshake];
            $count1=$pgood+$pback+$prightdev+$pleftdev;
            $maxIndex = findMaxIndex($arr);
            if($maxIndex==0){
                $zitai='驼背';
            }elseif ($maxIndex==1){
                $zitai='左倾';
            }elseif ($maxIndex==2){
                $zitai='右倾';
            }elseif ($maxIndex==3){
                $zitai='抖动';
            }
            if($count1>0){
                $data['pgood']=sprintf("%.2f", ($pgood/$count1));
                if($data['pgood']<0.4){
                    $data['pgood_msg']='姿态不是很标准，'.$zitai.'姿态占比比较大，需要注意纠正，请多辅助一下。';
                }elseif($data['pgood']>0.7){
                    $data['pgood_msg']='姿态良好，保持的也非常不错，点赞。';
                }else{
                    $data['pgood_msg']='姿态渐入佳境，继续保持练习，'.$zitai.'姿态占比比较大，需要注意纠正。';
                }
            }else{
                $data['pgood']=0;
                $data['pgood_msg']='暂无数据';
            }

        }else{
            $data['focus']=0;
            $data['focus_msg']='暂无数据';
            $data['pgood']=0;
            $data['pgood_msg']='暂无数据';
        }

        return message("获取成功", true,$data);

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
            zlog('result',$result);
            return message("修改成功", true,$result);

        }else{
            return message("参数已设置", true);
        }

    }

    //近7/28天详细数据
    public function device_data(){
        $device_id = input('device_id', '');//设备ID
        $type= input('type', '1','intval');//1是近7天，2是近四周
        if (!$device_id) {
            return message("参数错误", false);
        }
        if($type==2){
            $e_time = strtotime(date('Y-m-d 23:59:59', strtotime('0 days')));
            $s_time = strtotime(date('Y-m-d 00:00:00', strtotime('-27 days')));
            $map=[];
            $map[]=['people','=',1];
            $map[]=['device_id','=',$device_id];
            $date_list = periodWeek($s_time, $e_time);
            $date_arr=[1,2,3,4];
            $pgood_arr=[];//健康体态
            $all_count=0;//最近30天使用次数
            $focus_arr=[];//专注时长
            $heart_arr=[];//红心数量
            $date_data= [1,2,3,4];
            foreach ($date_list as $value) {
                //日期

                $start = strtotime($value.' 00:00:00');
                $end= $start+86400*7;


                $pgood_count=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->where('pgood','=',1)->count();//健康体态
                $pback_count=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->where('pback','=',1)->count();
                $pleftdev_count=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->where('pleftdev','=',1)->count();
                $prightdev_count=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->where('prightdev','=',1)->count();

                $heart_arr[]=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->sum('heart');//标准条数
                $count=$pgood_count+$pback_count+$pleftdev_count+$prightdev_count;
                $all_count=$all_count+$count;
                if($count){
                    $pgood_arr[]=number_format(sprintf("%.2f", ($pgood_count/$count))*100,0);

                }else{
                    $pgood_arr[]=0;

                }
                $focus_arr[]=0;
            }
            $pgood_arr= array_reverse($pgood_arr);
            $focus_arr= array_reverse($focus_arr);
            $heart_arr= array_reverse($heart_arr);

            //近28天红心总数
            $heart_count = DeviceDataModel::where('device_id','=',$device_id)->where('add_time', "between", "$s_time,$e_time")->where('heart','>',0)->sum('heart');
            //近28天红心兑换总数
            $duihuan_count =DeviceHeartLogModel::where('device_id','=',$device_id)->where('addtime', "between", "$s_time,$e_time")->where('status','>',2)->sum('number');


            //身姿28天累计时长
            $start_time = strtotime(date('Y-m-d 00:00:00', strtotime('-27 days')));
            $end_time=time();
            $where=[];
            $where[]=['people','=',1];
            $where[]=['device_id','=',$device_id];
            $where[]=['add_time', "between", "$start_time,$end_time"];
            $pgood_num=DeviceDataModel::where($where)->where('pgood','=',1)->count();//标准
            $pleftdev_num=DeviceDataModel::where($where)->where('pgood','=',0)->where('pleftdev','=',1)->count();//左倾
            $prightdev_num=DeviceDataModel::where($where)->where('pgood','=',0)->where('prightdev','=',1)->count();//右倾
            $pshake_num=DeviceDataModel::where($where)->where('pgood','=',0)->where('pshake','=',1)->count();//抖动
            $pback_num=DeviceDataModel::where($where)->where('pgood','=',0)->where('pback','=',1)->count();//驼背
            $zitai_x=['标准','左倾','右倾','抖动','驼背'];
            $zitai_y=[floor($pgood_num/6),floor($pleftdev_num/6),floor($prightdev_num/6),floor($pshake_num/6),floor($pback_num/6)];
        }else{

            $e_time = strtotime(date('Y-m-d 23:59:59', strtotime('0 days')));
            $s_time = strtotime(date('Y-m-d 00:00:00', strtotime('-6 days')));
            $map=[];
            $map[]=['people','=',1];
            $map[]=['device_id','=',$device_id];
            $date_list = periodDate($s_time, $e_time);
            $date_arr=[1,2,3,4,5,6,7];
            $pgood_arr=[];//健康体态
            $all_count=0;//最近七天使用次数
            $focus_arr=[];//专注时长
            $heart_arr=[];//红心数量
            $date_data=[];
            foreach ($date_list as $value) {
                //日期
                $date_data[] = date('d', strtotime($value));
                $start = strtotime($value.' 00:00:00');
                $end= strtotime($value.' 23:59:59');

                $count=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->count();
                $all_count=$all_count+$count;
                $pgood_count=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->where('pgood','=',1)->count();//健康体态
                $pback_count=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->where('pback','=',1)->count();
                $pleftdev_count=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->where('pleftdev','=',1)->count();
                $prightdev_count=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->where('prightdev','=',1)->count();
                $heart_arr[]=DeviceDataModel::where($map)->where('add_time', "between", "$start,$end")->sum('heart');//标准条数
                $count=$pgood_count+$pback_count+$pleftdev_count+$prightdev_count;
                $all_count=$all_count+$count;
                if($count){
                    $pgood_arr[]=number_format(sprintf("%.2f", ($pgood_count/$count))*100,0);
                }else{
                    $pgood_arr[]=0;

                }
                $focus_arr[]=0;
            }

            //近七天红心总数
            $heart_count = DeviceDataModel::where('device_id','=',$device_id)->where('add_time', "between", "$s_time,$e_time")->where('heart','>',0)->sum('heart');
            //近七天红心兑换总数
            $duihuan_count =DeviceHeartLogModel::where('device_id','=',$device_id)->where('addtime', "between", "$s_time,$e_time")->where('status','>',2)->sum('number');


            //身姿七天累计时长
            $start_time = strtotime(date('Y-m-d 00:00:00', strtotime('-6 days')));
            $end_time=time();
            $where=[];
            $where[]=['people','=',1];
            $where[]=['device_id','=',$device_id];
            $where[]=['add_time', "between", "$start_time,$end_time"];
            $pgood_num=DeviceDataModel::where($where)->where('pgood','=',1)->count();//标准
            $pleftdev_num=DeviceDataModel::where($where)->where('pgood','=',0)->where('pleftdev','=',1)->count();//左倾
            $prightdev_num=DeviceDataModel::where($where)->where('pgood','=',0)->where('prightdev','=',1)->count();//右倾
            $pshake_num=DeviceDataModel::where($where)->where('pgood','=',0)->where('pshake','=',1)->count();//抖动
            $pback_num=DeviceDataModel::where($where)->where('pgood','=',0)->where('pback','=',1)->count();//驼背
            $zitai_x=['标准','左倾','右倾','抖动','驼背'];
            $zitai_y=[floor($pgood_num/6),floor($pleftdev_num/6),floor($prightdev_num/6),floor($pshake_num/6),floor($pback_num/6)];

        }
        $data['pgood_arr']=$pgood_arr;
        $data['heart_count']=$heart_count;
        $data['duihuan_count']=$duihuan_count;
        $data['focus_arr']=$focus_arr;
        $data['all_count']=$all_count;
        $data['min']= secondsToTime($all_count*10);
        $data['date_list']=$date_list;
        $data['date_data']=$date_data;
        $data['zitai_x']=$zitai_x;
        $data['zitai_y']=$zitai_y;
        $data['heart_arr']=$heart_arr;
        $data['date_arr']=$date_arr;

        return message("获取成功", true,$data);

    }

    //检验版本更新
    public function version(){
        $device_id = input('device_id', '');//设备ID
        if (!$device_id) {
            return message("参数错误", false);
        }
        $info=DeviceModel::where('id','=',$device_id)->find();
        $version=VersionModel::where('is_del','=',1)->order('id','desc')->find();
        if($info['v']<$version['version']){
            $version['icon']=true;
            return message("获取成功", true,$version);
        }else{
            $version['icon']=false;
            return message("已经是最新版本了", true);
        }
    }

    //版本更新
    public function go_version(){
        $device_id = input('device_id', '');//设备ID
        $version= input('version', '');//版本号
        if (!$device_id||!$version) {
            return message("参数错误", false);
        }
        //发送升级指令
//            $http=Config::get('http');
//            $url=$http['url'].'setting/IssueInstructions';
//
//            $data=array(
//                'deviceId'=>$device_id,
//                'v'=>$version,
//            );
//             $data=json_encode($data);
//              $result= phpCurl($url,$data);

        //修改设备版本号

        if(DeviceModel::where('id','=',$device_id)->update(['v'=>$version])){
            return message("升级成功", true);
        }else{
            return message("升级失败", false);
        }

    }




}
