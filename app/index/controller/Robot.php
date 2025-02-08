<?php


namespace app\index\controller;
use app\common\model\Device as DeviceModel;
use app\common\model\NewDeviceSet as NewDeviceSetModel;
use app\common\model\NewDeviceData as NewDeviceDataModel;
use app\common\model\NewDeviceSetting as NewDeviceSettingModel;
use app\common\model\DeviceHeartLog as DeviceHeartLogModel;
use app\common\model\Fankui as FankuiModel;


use think\facade\Config;
use think\facade\Db;

class Robot extends Backend
{

    public function set_info(){
        $device_id = input('device_id', '');//设备ID
        if (!$device_id) {
            return message("参数错误", false);
        }

        $map = [];
        $map[] = ['device_id', '=', $device_id];
        $info=NewDeviceSetModel::with('device')->where($map)->find();
        return message("获取成功", true, $info);
    }

    //修改设备参数

    public function save_set(){

        try {
            $device_id = input('device_id', '');//设备ID
            if (!$device_id) {
                return message("参数错误", false);
            }
            $data=input('post.');

            $map = [];
            $map[] = ['device_id', '=', $device_id];
            $info=NewDeviceSetModel::where($map)->find();
            $device=DeviceModel::where('id','=',$device_id)->find();
            if($info){
                $data['update_time']=time();
                if($set_info=NewDeviceSetModel::where('id','=',$info['id'])->update($data)){
                    $arr=NewDeviceSetModel::where('id','=',$info['id'])->find()->toArray();
                    $arr['addtime']=time();
                    $arr['update_time']=time();
                    unset($arr['id']);
                    NewDeviceSettingModel::create($arr);
                    //上报设备

                    //发送设备指令
                    $http=Config::get('http');
                    $url=$http['url'].'setting/sendToDevice';
                    $data1=array(
                        "unixTime"=>time(),
                        "ID"=>$device['client'],
                        "Clock1"=>$arr['clock'],
                        "C1bell"=>$arr['bel'],
                        "C1delayTime"=> date('i:s', $arr['delay_time']*60),
                        "C1delaynum"=>$arr['delay_num'],
                        "TOClock"=> date('i:s', $arr['to_clock']*60),
                        "TObell"=>$arr['to_bell'],
                        "TOrest"=> date('i:s', $arr['to_rest']*60),
                        "TOrecover"=>$arr['to_recover'],
                        "TrainTime"=> date('H:i', $arr['train_time']*60-28800),
                        "HPClock"=>$arr['hp_clock'],
                        "LPClock"=>$arr['lp_clock'],
                        "OLClock"=>$arr['ol_clock'],
                        "Posedelay"=> date('i:s', $arr['pose_delay']),
                        "Posebell"=>$arr['pose_bell'],
                        "SLEEP"=>$arr['sleep'],
                        "Volume"=>$arr['volume'],
                    );

                    $data1=json_encode($data1);
                    zlog('data',$data1);
                    $result= phpCurl($url,$data1);

                    zlog('result',$result);



                    return message("操作成功", true, $data);
                }else{
                    return message("操作失败", false);
                }

            }else{
                $data['addtime']=time();
                $data['update_time']=time();

                if($set_info=NewDeviceSetModel::create($data)){
                    $arr=NewDeviceSetModel::where('id','=',$set_info['id'])->find()->toArray();
                    $arr['addtime']=time();
                    $arr['update_time']=time();
                    unset($arr['id']);
                    NewDeviceSettingModel::create($arr);
                    //上报设备
                    //发送设备指令
                    $http=Config::get('http');
                    $url=$http['url'].'setting/sendToDevice';
                    $data1=array(
                        "unixTime"=>time(),
                        "ID"=>$device['client'],
                        "Clock1"=>$arr['clock'],
                        "C1bell"=>$arr['bel'],
                        "C1delayTime"=> date('i:s', $arr['delay_time']*60),
                        "C1delaynum"=>$arr['delay_num'],
                        "TOClock"=> date('i:s', $arr['to_clock']*60),
                        "TObell"=>$arr['to_bell'],
                        "TOrest"=> date('i:s', $arr['to_rest']*60),
                        "TOrecover"=>$arr['to_recover'],
                        "TrainTime"=> date('H:i', $arr['train_time']*60-28800),
                        "HPClock"=>$arr['hp_clock'],
                        "LPClock"=>$arr['lp_clock'],
                        "OLClock"=>$arr['ol_clock'],
                        "Posedelay"=> date('i:s', $arr['pose_delay']),
                        "Posebell"=>$arr['pose_bell'],
                        "SLEEP"=>$arr['sleep'],
                        "Volume"=>$arr['volume'],
                    );

                    $data1=json_encode($data1);
                    zlog('data',$data1);
                    $result= phpCurl($url,$data1);

                    zlog('result',$result);


                    return message("操作成功", true, $set_info);
                }else{
                    return message("操作失败", false);
                }




            }


        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    public function real_data()
    {
        try {
            $device_id = input('device_id', '');//设备ID
            $day=input('day','1','intval');

            $s_time =strtotime(date("Y-m-d 23:59:59"));

            $w_time =$s_time-86400*$day;
            $map = [];
            $map[] = ['device_id', '=', $device_id];


            //红心总数
            $heart_count = DeviceHeartLogModel::where($map)->where('addtime', "between", "$w_time,$s_time")->where('status','=',1)->sum('number');


            $w_time1 =$s_time-86399;
            //当日红心总数
            $today_heart_count = DeviceHeartLogModel::where($map)->where('addtime', "between", "$w_time1,$s_time")->where('status','=',1)->sum('number');


            $map[] = ['add_time', "between", "$w_time1,$s_time"];
            //总数据
            $count= NewDeviceDataModel::where($map)->count();
            $map[] = ['task', '=', 1];
            $map[] = ['type', '=', 1];
            //标准总数
            $pgood_count = NewDeviceDataModel::where($map)->where(['pgood' => 1])->count();
            //驼背总数
            $pback_count = NewDeviceDataModel::where($map)->where(['pback' => 1])->count();
            //左倾总数
            $pleftdev_count = NewDeviceDataModel::where($map)->where(['pleftdev' => 1])->count();


            //右倾总数
            $prightdev_count = NewDeviceDataModel::where($map)->where(['prightdev' => 1])->count();
            //左旋总数
            $pleftrota_count = NewDeviceDataModel::where($map)->where(['pleftrota' => 1])->count();
            //右旋总数
            $prightrota_count = NewDeviceDataModel::where($map)->where(['prightrota' => 1])->count();

            //各种姿态的总和
            $count2=$pback_count+$pleftdev_count+$prightdev_count+$pleftrota_count+$prightrota_count;
            $data = [];

            $data['heart_count'] = $heart_count;
            $data['today_heart_count'] = $today_heart_count;

            $data['pgood_count'] =floor($pgood_count/60) ;
            $data['pback_count'] = floor($pback_count/60);
            $data['pleftdev_count'] = floor($pleftdev_count/60);
            $data['prightdev_count'] = floor($prightdev_count/60);
            $data['pleftrota_count'] = floor($pleftrota_count/60);
            $data['prightrota_count'] = floor($prightrota_count/60);
            $data['pgood_count_s'] =floor($pgood_count/1) ;
            $data['pback_count_s'] = floor($pback_count/1);
            $data['pleftdev_count_s'] = floor($pleftdev_count/1);
            $data['prightdev_count_s'] = floor($prightdev_count/1);
            $data['pleftrota_count_s'] = floor($pleftrota_count/1);
            $data['prightrota_count_s'] = floor($prightrota_count/1);

            $data['count'] = $count;

            //累计使用时长
            $data['count_min'] =floor($count/60);
            $data['min_to_hours'] =toHours($data['count_min']);
            //标准姿态累计时长
            $data['pgood_min'] =floor($pgood_count/60);
            $data['pgood_min_hours'] =toHours($data['pgood_min']);
            //标准姿态平均时长
            $data['pgood_avg_min'] =floor( $data['pgood_min']/$day);
            $data['pgood_avg_min_hours'] =toHours($data['pgood_avg_min']);
            //各种姿态的总和
            $data['count2'] = $count2;
            //各种姿态累计时长
            $data['count2_min'] =floor($count2/60);
            $data['count2_min_hours'] =toHours($data['count2_min']);

            //最近几天的数据汇总
            $e_time = strtotime(date('Y-m-d 23:59:59', strtotime('0 days')));
            $day=$day-1;
            $s_time = strtotime(date('Y-m-d 00:00:00', strtotime('-'.$day.' days')));
            $where=[];
            $where[]=['task','=',1];
            $where[]=['device_id','=',$device_id];
            $where[] = ['type', '=', 1];
            $date_list = periodDate($s_time, $e_time);

            $date_data=[];
            $pgood_data=[];//标准次数
            $tixing_data=[];//提醒次数

            $zuoziRatio_data=[];//坐姿改善率



            foreach ($date_list as $value) {
                //日期
                $date_data[] = date('d', strtotime($value));
                $start = strtotime($value.' 00:00:00');
                $end= strtotime($value.' 23:59:59');
                //每日标准坐姿
                $to_count=NewDeviceDataModel::where($where)->where('add_time', "between", "$start,$end")->where('pgood','=',1)->count();//健康体态
                if($to_count){
                    $pgood_data[]=floor($to_count/60);
                }else{
                    $pgood_data[]=0;
                }
                //每日提醒次数
                $tixing_count=NewDeviceDataModel::where($where)->where('add_time', "between", "$start,$end")->where('pgood','=',0)->count();//健康体态
                if($tixing_count){
                    $tixing_data[]=floor($tixing_count/60);
                }else{
                    $tixing_data[]=0;
                }


                //坐姿改善率
                //标准总数
                $sit_pgood_count = NewDeviceDataModel::where($where)->where('add_time', "between", "$start,$end")->where('pgood','=',1)->count();
                //驼背总数
                $sit_pback_count = NewDeviceDataModel::where($where)->where('add_time', "between", "$start,$end")->where('pback','=',1)->count();
                //左倾总数
                $sit_pleftdev_count = NewDeviceDataModel::where($where)->where('add_time', "between", "$start,$end")->where('pleftdev','=',1)->count();
                //右倾总数
                $sit_prightdev_count = NewDeviceDataModel::where($where)->where('add_time', "between", "$start,$end")->where('prightdev','=',1)->count();
                //各种姿态的总和
                $sit_count2=$sit_pback_count+$sit_pleftdev_count+$sit_prightdev_count;
                $sit_pgood_count_min = floor($sit_pgood_count/60);
                $sit_count2_min = floor($sit_count2/60);
                $zuoziRatio_num = $sit_pgood_count_min+$sit_count2_min;

                if ($zuoziRatio_num){
                    $zuoziRatio_count =number_format($sit_count2_min/$zuoziRatio_num,2);
                    $zuoziRatio_data[]=floor($zuoziRatio_count*100);
                }else{
                    $zuoziRatio_data[] = 0;
                }


            }
            $data['pgood_data']=  $pgood_data;
            $data['tixing_data']=  $tixing_data;
            $data['date_data']=  $date_data;
            $data['date_list']=  $date_list;
            $maxIndex = findMaxIndex($pgood_data);
            //最大专注时长
            $data['pgood_max_count']=$pgood_data[$maxIndex];
            $data['pgood_max_min_hours'] =toHours($data['pgood_max_count']);

            //坐姿改善率
            $data['zuoziRatio_data']=  $zuoziRatio_data;






            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }


    //用户反馈
    public function fankui(){
        $device_id = input('device_id', '');//设备ID

        $content=input('content','');
        $photo=input('photo');
        if($device_id==0||$content==''){
            return message("参数错误", false);
        }
        if($photo){
            $photo_img=implode('###',$photo);
        }else{
            $photo_img='';
        }
        $arr=array(
            'uid'=>$this->userId,
            'device_id'=>$device_id,
            'content'=>$content,
            'photo'=>$photo_img,
            'addtime'=>time(),
        );

        if(FankuiModel::create($arr)){
            return message("反馈成功", true);
        }else{
            return message("反馈失败", false);
        }

    }
    //设备等级

    public function device_dengji(){
        $device_id = input('device_id', '');//设备ID
        if($device_id==0){
            return message("参数错误", false);
        }

        $dengji_a=array(
            'dengji'=>'A',
            "hp_clock"=> 25,//驼背报警的角度
            "lp_clock"=> 20,//侧倾报警的角度
            "ol_clock"=> 20,//侧旋报警的角度
        );
        $dengji_b=array(
            'dengji'=>'B',
            "hp_clock"=> 20,//驼背报警的角度
            "lp_clock"=> 15,//侧倾报警的角度
            "ol_clock"=> 15,//侧旋报警的角度
        );
        $dengji_c=array(
            'dengji'=>'C',
            "hp_clock"=> 15,//驼背报警的角度
            "lp_clock"=> 10,//侧倾报警的角度
            "ol_clock"=> 10,//侧旋报警的角度
        );
        $dengji_d=array(
            'dengji'=>'D',
            "hp_clock"=> 10,//驼背报警的角度
            "lp_clock"=> 5,//侧倾报警的角度
            "ol_clock"=> 5,//侧旋报警的角度
        );
        $info_a=NewDeviceSettingModel::where('device_id','=',$device_id)->where('dengji','=','A')->order('id','desc')->find();
        if($info_a){
            $dengji_a['dengji']= $info_a['dengji'];
            $dengji_a['hp_clock']= $info_a['hp_clock'];
            $dengji_a['lp_clock']= $info_a['lp_clock'];
            $dengji_a['ol_clock']= $info_a['ol_clock'];
        }
        $info_b=NewDeviceSettingModel::where('device_id','=',$device_id)->where('dengji','=','B')->order('id','desc')->find();
        if($info_b){
            $dengji_b['dengji']= $info_b['dengji'];
            $dengji_b['hp_clock']= $info_b['hp_clock'];
            $dengji_b['lp_clock']= $info_b['lp_clock'];
            $dengji_b['ol_clock']= $info_b['ol_clock'];
        }
        $info_c=NewDeviceSettingModel::where('device_id','=',$device_id)->where('dengji','=','C')->order('id','desc')->find();
        if($info_c){
            $dengji_c['dengji']= $info_c['dengji'];
            $dengji_c['hp_clock']= $info_c['hp_clock'];
            $dengji_c['lp_clock']= $info_c['lp_clock'];
            $dengji_c['ol_clock']= $info_c['ol_clock'];
        }
        $info_d=NewDeviceSettingModel::where('device_id','=',$device_id)->where('dengji','=','D')->order('id','desc')->find();
        if($info_d){
            $dengji_d['dengji']= $info_d['dengji'];
            $dengji_d['hp_clock']= $info_d['hp_clock'];
            $dengji_d['lp_clock']= $info_d['lp_clock'];
            $dengji_d['ol_clock']= $info_d['ol_clock'];
        }
        $data=array(
            '0'=>$dengji_a,
            '1'=>$dengji_b,
            '2'=>$dengji_c,
            '3'=>$dengji_d,
        );

        return message("获取成功", true, $data);

    }




}