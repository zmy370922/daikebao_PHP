<?php

namespace app\index\controller;

use app\common\model\Goods as GoodsModel;
use app\common\model\Recharge as RechargeModel;
use app\common\model\RechargeOrder as RechargeOrderModel;
use app\common\model\Order as OrderModel;
use app\common\model\User as UserModel;
use app\common\model\Device as DeviceModel;
use app\common\model\DeviceBind as DeviceBindModel;
use app\common\model\DeviceHeartLog as DeviceHeartLogModel;

class Shop extends Backend
{
    //流量充值列表
    public function recharge_list()
    {
        try {
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 20, 'intval');

            $map = [];
            $map[] = ['is_del', '=', 1];
            $list = RechargeModel::where($map)->order(['integral' => 'asc'])->limit(($page - 1) * $num, $num)->select();

            return message("获取成功", true, $list);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //流量充值下单
    public function recharge_order()
    {
        try {
            $recharge_id = input('recharge_id', 0, 'intval');
            if (!$recharge_id) {
                return message("参数错误", false);
            }

            $recharge_info = RechargeModel::where(['id' => $recharge_id])->find();
            if (!$recharge_info) {
                return message("数据不存在", false);
            }

            $order = [];
            $order['recharge_id'] = $recharge_id;
            $order['uid'] = $this->userId;
            $order['money'] = $recharge_info['money'];
            $order['integral'] = $recharge_info['integral'];
            $order['give_integral'] = $recharge_info['give_integral'];
            $order['sn'] = date('YmdHis', time()) . rand(11111, 99999);
            $order['status'] = 1;
            $order['add_time'] = time();
            if ($new = RechargeOrderModel::create($order)) {
                return message("下单成功", true, $new, 200);
            } else {
                return message("下单失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //红心商品列表
    public function goods_list()
    {
        try {
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            $map[] = ['is_show', '=', 1];
            $map[] = ['is_del', '=', 1];

            $list = GoodsModel::where($map)->order(['sort' => 'desc'])->limit(($page - 1) * $num, $num)->select();


            return message("获取成功", true, $list);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //红心商品详情
    public function goods_detail()
    {
        try {
            $goods_id = input('goods_id', 0, 'intval');
            if (!$goods_id) {
                return message("参数错误", false);
            }


            $map = [];
            $map[] = ['id', '=', $goods_id];
            $map[] = ['is_show', '=', 1];
            $map[] = ['is_del', '=', 1];

            $info = GoodsModel::where($map)->find();
            if (!$info) {
                return message("商品已下架", false);
            }

            return message("获取成功", true, $info);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //红心商品下单
    public function goods_order()
    {
        try {
            $device_id = input('device_id', 0, 'intval');//当前切换在线的设备ID
            $goods_id = input('goods_id', 0, 'intval');
            $quantity = input('quantity', 0, 'intval');
            $name = input('name', '');
            $address = input('address', '');
            $mobile = input('mobile', '');
            if (!$device_id || !$goods_id || !$quantity || !$name || !$address || !$mobile) {
                return message("参数错误", false);
            }

            $bind_info = DeviceBindModel::where(['uid' => $this->userId, 'device_id' => $device_id])->find();
            if (!$bind_info) {
                return message("绑定设备信息不存在", false);
            }

            $map = [];
            $map[] = ['id', '=', $goods_id];
            $map[] = ['is_show', '=', 1];
            $map[] = ['is_del', '=', 1];

            $detail = GoodsModel::where($map)->find();
            if (!$detail) {
                return message("商品已下架", false);
            }

            $total_red = $detail['price'] * $quantity;//支付红心总数

            $device_info = DeviceModel::where(['id' => $device_id])->find();
            if ($device_info['red'] < $total_red) {
                return message("设备红心数不足", false);
            }

            $order = [];
            $order['uid'] = $this->userId;
            $order['device_id'] = $device_id;
            $order['goods_id'] = $detail['id'];
            $order['title'] = $detail['title'];
            $order['cover'] = $detail['cover'];
            $order['sn'] = date('YmdHis', time()) . rand(11111, 99999);
            $order['quantity'] = $quantity;
            $order['red'] = $total_red;
            $order['status'] = 20;
            $order['is_pay'] = 3;
            $order['name'] = $name;
            $order['address'] = $address;
            $order['mobile'] = $mobile;
            $order['add_time'] = time();
            $order['pay_time'] = time();
            if ($new = OrderModel::create($order)) {
                $new_data = [];
                $new_red = $device_info['red'] - $total_red;
                $new_data['red'] = $new_red;
                $new_red_buy = $device_info['red_buy'] + $total_red;
                $new_data['red_buy'] = $new_red_buy;
                DeviceModel::where(['id' => $device_info['id']])->update($new_data);
                //记录红心日志
                $new_arr=array(
                    'device_id'=>$device_id,
                    'number'=>$total_red,
                    'status'=>2,
                    'content'=>'兑换红心商品，设备红心扣除'.$total_red.'颗',
                    'addtime'=>time(),
                );

                DeviceHeartLogModel::create($new_arr);
                return message("下单成功，等待平台发货", true, $new, 200);
            } else {
                return message("下单失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //红心商品订单列表
    public function order_list()
    {
        try {
            //订单状态 0：全部订单,20待发货,30待收货
            $status = input('status', 0, 'intval');
            $page = input('page', 0, 'intval');
            $num = input('num', 10, 'intval');

            $map = [];
            $map[] = ['uid', '=', $this->userId];
            if ($status) {
                $map[] = ['status', '=', $status];
            }
            $map[] = ['is_del', '=', 1];

            $list = OrderModel::with(['goods'])->where($map)->order(['add_time' => 'desc'])->limit($page * $num, $num)->select();
            return message("获取成功", true, $list);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //订单详情
    public function order_detail()
    {
        try {
            $order_id = input('order_id', 0, 'intval');
            if (!$order_id) {
                return message('参数错误', false);
            }

            $map = [];
            $map[] = ['id', '=', $order_id];
            $map[] = ['uid', '=', $this->userId];

            $detail = OrderModel::with(['goods'])->where($map)->find();
            $detail['bind'] =DeviceBindModel::where('uid','=',$this->userId)->where('device_id','=',$detail['device_id'])->find();


            return message('获取成功', true, $detail);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //订单收货完成
    public function order_complete()
    {
        try {
            $order_id = input('order_id', 0, 'intval');
            if (!$order_id) {
                return message('参数错误', false);
            }

            $map = [];
            $map[] = ['id', '=', $order_id];
            $map[] = ['uid', '=', $this->userId];
            $order_info = OrderModel::where($map)->find();
            if (!$order_info) {
                return message('订单不存在', false);
            }

            if ($order_info['status'] == 50) {
                return message('订单已完成', false);
            }

            $data = [];
            $data['status'] = 50;
            $data['confirm_time'] = time();
            $status = OrderModel::where(['id' => $order_info['id']])->update($data);
            if ($status) {
                return message('操作成功', true);
            } else {
                return message('操作失败', false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
