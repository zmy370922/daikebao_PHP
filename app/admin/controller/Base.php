<?php

namespace app\admin\controller;

use app\common\model\SysRole as SysRoleModel;
use think\facade\Filesystem;

/**
 * 后台-基础
 * @author 鱼鱼鱼
 * @since 2023年8月3日14:28:44
 * Class Base
 * @package app\admin\controller
 */
class Base extends Backend
{
    //角色列表
    public function role_list()
    {
        try {
            $map = [];
            $map[] = ['status', '=', 1];
            $map[] = ['is_del', '=', 1];

            $list = SysRoleModel::where($map)->select();

            $data = [];
            $data['role_list'] = $list;

            return message("获取成功", true, $data, 200);
        } catch (\Exception $e) {
            return message('错误：' . $e->getMessage(), false);
        }
    }
}