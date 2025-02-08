<?php

namespace app\admin\controller;

use app\common\model\Recharge as RechargeModel;

/**
 * 后台-流量套餐
 * @author 鱼鱼鱼
 * @since 2023年9月11日13:39:16
 * Class Recharge
 * @package app\admin\controller
 */
class Recharge extends Backend
{
    //列表
    public function recharge_list()
    {
        try {
            $keyword = input('keyword', '');//搜索
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($keyword) {
                $map[] = ['money|integral', 'like', '%' . $keyword . '%'];
            }


            $map[] = ['is_del', '=', 1];
            $list = RechargeModel::where($map)->order(['integral' => 'asc'])->limit(($page - 1) * $num, $num)->select();
            //总数
            $count = RechargeModel::where($map)->count();
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
            $money = input('money', '');
            $integral = input('integral', '');
            $desc = input('desc', '');

            if (!$money || !$integral) {
                return message("参数错误", false);
            }

            $data = [];
            $data['money'] = $money;
            $data['integral'] = $integral;
            $data['desc'] = $desc;
            $data['add_time'] = time();
            if ($new = RechargeModel::create($data)) {
                action_log($this->userId, 'recharge', '流量充值操作', '添加流量充值', $new['id']);
                return message("添加成功", true, $new, 200);
            } else {
                return message("添加失败", false);
            }
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

            $map = [];
            $map[] = ['id', '=', $id];
            $info = RechargeModel::where($map)->find();

            return message("操作成功", true, $info, 200);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //编辑
    public function edit()
    {
        try {
            $id = input('id', 0, 'intval');
            $money = input('money', '');
            $integral = input('integral', '');
            $desc = input('desc', '');

            if (!$id || !$money || !$integral) {
                return message("参数错误", false);
            }

            $data = [];
            $data['money'] = $money;
            $data['integral'] = $integral;
            $data['desc'] = $desc;
            $data['update_time'] = time();
            if ($res = RechargeModel::where(['id' => $id])->update($data)) {
                action_log($this->userId, 'recharge', '流量充值操作', '编辑流量充值', $id);
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

            if ($status = RechargeModel::where(['id' => $id])->update(['is_del' => 2])) {
                action_log($this->userId, 'recharge', '流量充值操作', '删除流量充值', $id);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
