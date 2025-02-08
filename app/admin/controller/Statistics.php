<?php
/**
 * Created by
 * User: 鱼鱼鱼
 * Date: 2023年6月21日
 * Time: 10:26:28
 * desc: 首页统计相关
 */

namespace app\admin\controller;

use app\common\model\User as UserModel;
use app\common\model\RechargeOrder as RechargeOrderModel;
use app\common\model\Order as OrderModel;
use think\facade\Db;

class Statistics extends Backend
{
    //统计数据
    public function index()
    {
        try {
            //今日新增用户数据
            $today_stime = strtotime(date('Y-m-d 00:00:00', time()));
            $today_etime = strtotime(date('Y-m-d 23:59:59', time()));
            $today_where = [];
            $today_where[] = ['status', '=', 1];
            $today_where[] = ['is_del', '=', 1];
            $today_where[] = ['add_time', "between", "$today_stime,$today_etime"];
            $homeOne = [];
            $homeOne1['num1'] = UserModel::where($today_where)->count();
            $homeOne1['num2'] = 0;
            $homeOne1['num3'] = "今日新增用户信息";
            $homeOne1['num4'] = "fa fa-meetup";
            $homeOne1['color1'] = "#FF6462";
            $homeOne1['color2'] = "--next-color-primary-lighter";
            $homeOne1['color3'] = "--el-color-primary";
            $homeOne[] = $homeOne1;

            //总用户数
            $total_where = [];
            $total_where[] = ['status', '=', 1];
            $total_where[] = ['is_del', '=', 1];
            $user_count = UserModel::where($total_where)->count();//总用户总数
            $homeOne2['num1'] = $user_count;
            $homeOne2['num2'] = 0;
            $homeOne2['num3'] = "总用户数信息";
            $homeOne2['num4'] = "iconfont icon-ditu";
            $homeOne2['color1'] = "#6690F9";
            $homeOne2['color2'] = "--next-color-success-lighter";
            $homeOne2['color3'] = "--el-color-success";
            $homeOne[] = $homeOne2;

            //今日流量充值订单金额
            $today_recharge_where = [];
            $today_recharge_where[] = ['status', '=', 2];
            $today_recharge_where[] = ['is_del', '=', 1];
            $today_recharge_where[] = ['pay_time', "between", "$today_stime,$today_etime"];
            $homeOne3['num1'] = RechargeOrderModel::where($today_recharge_where)->sum('money');
            $homeOne3['num2'] = 0;
            $homeOne3['num3'] = "今日充值订单总额信息";
            $homeOne3['num4'] = "iconfont icon-zaosheng";
            $homeOne3['color1'] = "#6690F9";
            $homeOne3['color2'] = "--next-color-warning-lighter";
            $homeOne3['color3'] = "--el-color-warning";
            $homeOne[] = $homeOne3;

            //订单待发货
            $order_where = [];
            $order_where[] = ['status', '=', 20];
            $order_where[] = ['is_del', '=', 1];
            $homeOne4['num1'] = OrderModel::where($order_where)->count();
            $homeOne4['num2'] = 0;
            $homeOne4['num3'] = "订单待发货数信息";
            $homeOne4['num4'] = "fa fa-github-alt";
            $homeOne4['color1'] = "#FF6462";
            $homeOne4['color2'] = "--next-color-danger-lighter";
            $homeOne4['color3'] = "--el-color-danger";
            $homeOne[] = $homeOne4;

            //最近30天用户注册数
            $days = get_last_days_date();
            $month_stime = strtotime(date('Y-m-d 00:00:00', strtotime('-29 day')));
            $month_etime = strtotime(date('Y-m-d 23:59:59', time()));
            $sql = "SELECT COUNT(*) AS incr,FROM_UNIXTIME(add_time, '%Y-%m-%d') AS time FROM `md_user` FORCE INDEX (add_time) WHERE add_time BETWEEN {$month_stime} AND {$month_etime} AND is_del = 1 GROUP BY time";
            $incr_user_list = Db::query($sql);
            if ($incr_user_list) {
                $incr_user_list = array_column($incr_user_list, NULL, 'time');
                $user_line_data = [];
                foreach ($days as $val) {
                    if (isset($incr_user_list[$val])) {
                        $user_line_data[] = $incr_user_list[$val]['incr'];
                    } else {
                        $user_line_data[] = 0;
                    }
                }
            } else {
                $user_line_data = [];
                for ($i = 30; $i > 0; $i--) {
                    $user_line_data[] = 0;
                }
            }


            $data = [];
            $data['homeOne'] = $homeOne;
            $data['lineDate'] = $days;
            $data['months_user_line'] = $user_line_data;

            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}