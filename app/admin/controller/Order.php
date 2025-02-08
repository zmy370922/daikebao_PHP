<?php

namespace app\admin\controller;

use app\common\model\Order as OrderModel;
use app\common\model\RechargeOrder as RechargeOrderModel;


/**
 * 后台-订单管理
 * @author 鱼鱼鱼
 * @since 2023年8月21日10:36:00
 * Class Order
 * @package app\admin\controller
 */
class Order extends Backend
{
    //流量充值订单列表
    public function recharge_order()
    {
        try {
            $status = input('status', 0, 'intval');//订单状态
            $keywords = input('keywords', '');//订单编号搜索
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($status) {
                $map[] = ['status', '=', $status];
            }
            if ($keywords) {
                $map[] = ['sn', 'like', '%' . $keywords . '%'];
            }
            $map[] = ['is_del', '=', 1];

            $list = RechargeOrderModel::with(['user'])->where($map)->order(['add_time' => 'desc'])->limit(($page - 1) * $num, $num)->select();

            //总数
            $count = RechargeOrderModel::where($map)->count();
            $data = ['list' => $list, 'total' => $count];

            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //流量充值订单删除
    public function recharge_del()
    {
        try {
            $id = input('id', 0, 'intval');

            if (!$id) {
                return message("参数错误", false);
            }

            if ($status = RechargeOrderModel::where(['id' => $id])->update(['is_del' => 2])) {
                action_log($this->userId, 'recharge_order', '充值订单操作', '删除充值订单', $id);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //红心商品订单列表
    public function goods_order()
    {
        try {
            $status = input('status', 0, 'intval');
            $keywords = input('keywords', '');//商品搜索
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($status) {
                $map[] = ['status', '=', $status];
            }
            if ($keywords) {
                $map[] = ['title|sn', 'like', '%' . $keywords . '%'];
            }
            $map[] = ['is_del', '=', 1];

            $list = OrderModel::with(['user'])->where($map)->order(['add_time' => 'desc'])->limit(($page - 1) * $num, $num)->select();

            //总数
            $count = OrderModel::where($map)->count();
            $data = ['list' => $list, 'total' => $count];

            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //商品订单发货
    public function send()
    {
        try {
            $id = input('id', 0, 'intval');
            $shipping_name = input('shipping_name', '');
            $shipping_sn = input('shipping_sn', '');

            if (!$id || !$shipping_name || !$shipping_sn) {
                return message("参数错误", false);
            }

            $info = OrderModel::where(['id' => $id])->find();
            if ($info['status'] != 20) {
                return message("已发货", false);
            }

            $order = [];
            $order['shipping_name'] = $shipping_name;
            $order['shipping_sn'] = $shipping_sn;
            $order['status'] = 30;
            $order['send_time'] = time();

            if ($status = OrderModel::where(['id' => $id])->update($order)) {
                action_log($this->userId, 'order', '商品订单操作', '商品订单发货', $id);
                return message("发货成功", true);
            } else {
                return message("发货失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }


    //商品订单删除
    public function del()
    {
        try {
            $id = input('id', 0, 'intval');

            if (!$id) {
                return message("参数错误", false);
            }

            if ($status = OrderModel::where(['id' => $id])->update(['is_del' => 2])) {
                action_log($this->userId, 'order', '商品订单操作', '删除商品订单', $id);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
