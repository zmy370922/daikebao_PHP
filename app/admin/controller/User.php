<?php

namespace app\admin\controller;

use app\common\model\User as UserModel;

class User extends Backend
{
    //列表
    public function user_list()
    {
        try {
            $keywords = input('keywords', '');//用户搜索
            $key = input('key', '');
            $order = input('order', '');
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($keywords) {
                $keywords = trim($keywords);
                $map[] = ['nickname|mobile', 'like', '%' . $keywords . '%'];
            }

            //排序
            if ($key) {
                $order == 'descending' ? $order_by = ['login_num' => 'desc'] : $order_by = ['login_num' => 'asc'];
            } else {
                $order_by = ['add_time' => 'desc'];
            }


            $map[] = ['is_del', '=', 1];
            $list = UserModel::where($map)->order($order_by)->limit(($page - 1) * $num, $num)->select();
            foreach ( $list as $key=>$v){
                $list[$key]['mobile']='*******'.substr($v['mobile'], 7); // ;
            }

            //总数
            $count = UserModel::where($map)->count();
            $data = ['list' => $list, 'total' => $count];

            return message("获取成功", true, $data);
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
            $map[] = ['is_del', '=', 1];
            $info = UserModel::where($map)->find();

            return message("操作成功", true, $info, 200);
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

            $data = [];
            $data['is_del'] = 2;
            if ($status = UserModel::where(['id' => $id])->update($data)) {
                action_log($this->userId, 'user', '用户操作', '删除用户', $id);
                return message("删除成功", true, [], 200);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
