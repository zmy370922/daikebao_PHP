<?php

namespace app\tearcher\controller;

use app\common\model\Tearcher as TearcherModel;

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
            $map[] = ['username', '=', $account];
            $manage_info = TearcherModel::where($map)->find();
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
            TearcherModel::where(['id' => $manage_info['id']])->update(['token' => $token]);
            $manage_info['token'] = $token;
            $manage_info['photo'] = "https://img2.baidu.com/it/u=2370931438,70387529&fm=253&fmt=auto&app=138&f=JPEG?w=500&h=500";
            return message("登录成功", true, $manage_info);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}