<?php

namespace app\admin\controller;

use app\common\model\Manage as ManageModel;

use app\BaseController;

/**
 * 后台-登录
 * @author 鱼鱼鱼
 * @since 2023年8月3日14:28:12
 * Class Login
 * @package app\admin\controller
 */
class Login extends BaseController
{
    //登录
    public function login()
    {
        try {
            $account = input('account', '');//用户名称
            $password = input('password', '');//密码
            if (!$account || !$password) {
                return message("参数错误", false);
            }

            $map = [];
            $map[] = ['account', '=', $account];
            $map[] = ['is_del', '=', 1];
            $manage_info = ManageModel::where($map)->find();
            if (!$manage_info) {
                return message("账号不存在", false);
            }

            if ($manage_info['password'] != md5($password)) {
                return message("密码输入有误", false);
            }

            if ($manage_info['status'] == 2) {
                return message("账号已关闭", false);
            }

            $token = get_login_token($manage_info); //生成token
            ManageModel::where(['id' => $manage_info['id']])->update(['token' => $token]);
            $manage_info['token'] = $token;
            $manage_info['photo'] = $manage_info['avatar'];
            action_log($manage_info['id'], 'manage');
            return message("登录成功", true, $manage_info);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}