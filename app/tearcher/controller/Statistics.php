<?php
/**
 * Created by
 * User: 鱼鱼鱼
 * Date: 2023年6月21日
 * Time: 10:26:28
 * desc: 首页统计相关
 */

namespace app\tearcher\controller;

use app\common\model\Tearcher as TearcherModel;
use app\common\model\HeadBind as HeadBindModel;

use app\common\model\TearcherBind as TearcherBindModel;
use app\common\model\TearcherCate as TearcherCateModel;
use think\facade\Db;

class Statistics extends Backend
{
    //统计数据
    public function index()
    {
        try {
            $userinfo=$this->userInfo;
            if($userinfo['type']==1){
                //老师数据
                $tearcher=TearcherModel::where('pid','=',$this->userId)->count();
                //设备数
                $device_count=HeadBindModel::where('tid','=',$this->userId)->count();
                //班级数
                $tids=TearcherModel::where('pid','=',$this->userId)->column('id');
                if(count($tids)>0){
                    $grade=TearcherCateModel::where('tid','in',$tids)->count();
                }else{
                    $grade=0;
                }
                //老师数据
                $homeOne = [];
                $homeOne1['num1'] =$tearcher;
                $homeOne1['num2'] = 0;
                $homeOne1['num3'] = "老师数信息";
                $homeOne1['num4'] = "fa fa-meetup";
                $homeOne1['color1'] = "#FF6462";
                $homeOne1['color2'] = "--next-color-primary-lighter";
                $homeOne1['color3'] = "--el-color-primary";
                $homeOne[] = $homeOne1;

                //总设备数

                $homeOne2['num1'] = $device_count;
                $homeOne2['num2'] = 0;
                $homeOne2['num3'] = "总设备数信息";
                $homeOne2['num4'] = "iconfont icon-ditu";
                $homeOne2['color1'] = "#6690F9";
                $homeOne2['color2'] = "--next-color-success-lighter";
                $homeOne2['color3'] = "--el-color-success";
                $homeOne[] = $homeOne2;
                //总班级
                $homeOne3['num1'] = $grade;
                $homeOne3['num2'] = 0;
                $homeOne3['num3'] = "总班级数信息";
                $homeOne3['num4'] = "iconfont icon-zaosheng";
                $homeOne3['color1'] = "#6690F9";
                $homeOne3['color2'] = "--next-color-warning-lighter";
                $homeOne3['color3'] = "--el-color-warning";
                $homeOne[] = $homeOne3;


            }elseif ($userinfo['type']==2){
                //设备数
                $device_count= TearcherBindModel::where('tid','=',$this->userId)->count();
                //班级数
                $grade=TearcherCateModel::where('tid','=',$this->userId)->count();
                //总设备数

                $homeOne2['num1'] = $device_count;
                $homeOne2['num2'] = 0;
                $homeOne2['num3'] = "总设备数信息";
                $homeOne2['num4'] = "iconfont icon-ditu";
                $homeOne2['color1'] = "#6690F9";
                $homeOne2['color2'] = "--next-color-success-lighter";
                $homeOne2['color3'] = "--el-color-success";
                $homeOne[] = $homeOne2;
                //总班级
                $homeOne3['num1'] = $grade;
                $homeOne3['num2'] = 0;
                $homeOne3['num3'] = "总班级数信息";
                $homeOne3['num4'] = "iconfont icon-zaosheng";
                $homeOne3['color1'] = "#6690F9";
                $homeOne3['color2'] = "--next-color-warning-lighter";
                $homeOne3['color3'] = "--el-color-warning";
                $homeOne[] = $homeOne3;
            }

            $data = [];
            $data['homeOne'] = $homeOne;


            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}