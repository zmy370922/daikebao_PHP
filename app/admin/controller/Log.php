<?php

namespace app\admin\controller;

use app\common\model\ActionLog as ActionLogModel;

/**
 * 后台-操作日志
 * @author 鱼鱼鱼
 * @since 2023年8月15日11:39:28
 * Class Manage
 * @package app\admin\controller
 */
class Log extends Backend
{
    //列表
    public function log_list()
    {
        try {
            $mid = input('uid', 0, 'intval');//操作人UID
            $order_feild = input('order_feild', '');//排序字段
            $order_desc = input('order_desc', '');//排序 descending：降序 ascending：升序
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($mid) {
                $map[] = ['mid', '=', $mid];
            }

            // 按字段排序
            if ($order_feild) {
                if ($order_desc == 'descending') {
                    $order_by = ['add_time' => 'desc'];
                } else {
                    $order_by = ['add_time' => 'asc'];
                }
            } else {
                $order_by = ['add_time' => 'desc'];
            }

            $map[] = ['is_del', '=', 1];
            $list = ActionLogModel::with(['manage'])->where($map)->order($order_by)->limit(($page - 1) * $num, $num)->select();
            //总数
            $count = ActionLogModel::where($map)->count();
            $data = ['list' => $list, 'total' => $count];

            return message("获取成功", true, $data);
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

            if ($status = ActionLogModel::where(['id' => $id])->delete()) {
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //清空操作日志
    public function clear()
    {
        try {
            $map = [];
            $map[] = ['id', '>', 0];
            $status = ActionLogModel::where($map)->delete();
            if ($status !== false) {
                action_log($this->userId, 'action_log', '日志操作', '清空系统操作日志');
                return message("操作成功", true);
            } else {
                return message("操作失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
