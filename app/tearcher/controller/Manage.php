<?php

namespace app\tearcher\controller;

use app\common\model\Tearcher as TearcherModel;
use app\common\model\User as UserModel;

/**
 * 后台-管理员
 * @author 鱼鱼鱼
 * @since 2023年8月3日14:28:44
 * Class Manage
 * @package app\admin\controller
 */
class Manage extends Backend
{
    //退出登录
    public function logout()
    {
        try {
            $status = TearcherModel::where(['id' => $this->userId])->update(['token' => '']);

            if ($status) {
                return message("操作成功", true);
            } else {
                return message("操作失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //编辑密码
    public function edit_pwd()
    {
        try {
            $old_pwd = input('old_pwd', '');
            $new_pwd = input('new_pwd', '');
            $confirm_pwd = input('confirm_pwd', '');
            if (!$old_pwd || !$new_pwd || !$confirm_pwd) {
                return message("参数错误", false);
            }

            $userInfo = $this->userInfo;
            if ($userInfo['password'] != md5($old_pwd)) {
                return message("旧密码不正确", false);
            }

            if ($new_pwd !== $confirm_pwd) {
                return message("两次密码不一致，请确认", false);
            }

            $data = [];
            $data['password'] = md5($new_pwd);
            $status = TearcherModel::where(['id' => $this->userId])->update($data);
            if ($status !== false) {

                return message("修改成功", true);
            } else {
                return message("修改失败", false);
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
            $info = TearcherModel::where($map)->find();

            return message("获取成功", true, $info);
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

            if ($status = TearcherModel::where(['id' => $id])->delete()) {
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
