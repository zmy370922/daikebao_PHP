<?php

namespace app\admin\controller;

use app\common\model\Website as WebsiteModel;

/**
 * 后台-网站设置
 * @author 鱼鱼鱼
 * @since 2023年8月16日11:37:26
 * Class Website
 * @package app\admin\controller
 */
class Website extends Backend
{

    //信息
    public function info()
    {
        try {
            $map = [];
            $map[] = ['id', '=', 1];
            $info = WebsiteModel::where($map)->find();

            return message("获取成功", true, $info);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //编辑
    public function edit()
    {
        try {
            $weixin = input('weixin', '');
            $email = input('email', '');
            $phone = input('phone', '');
            $times = input('times', 0, 'intval');
            $user_service = input('user_service', '');//隐私政策
            $user_agreement = input('user_agreement', '');//用户服务协议


            $data = [];
            $data['weixin'] = $weixin;
            $data['email'] = $email;
            $data['phone'] = $phone;
            $data['times'] = $times;
            $data['user_service'] = $user_service;
            $data['user_agreement'] = $user_agreement;
            $data['update_time'] = time();
            if ($res = WebsiteModel::where(['id' => 1])->update($data)) {
                action_log($this->userId, 'manage', '网站配置操作', '编辑网站设置', 1);
                return message("编辑成功", true);
            } else {
                return message("编辑失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
