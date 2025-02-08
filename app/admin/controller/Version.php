<?php

namespace app\admin\controller;

use app\common\model\Version as VersionModel;
use think\facade\Db;

/**
 * 后台-设备升级管理
 * @author 鱼鱼鱼
 * @since 2023年9月19日11:09:20
 * Class Version
 * @package app\admin\controller
 */
class Version extends Backend
{
    //列表
    public function lists()
    {
        try {
            $keywords = input('keywords', '');//商品搜索
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($keywords) {
                $map[] = ['content', 'like', '%' . $keywords . '%'];
            }


            $map[] = ['is_del', '=', 1];


            $list = VersionModel::where($map)->order(['add_time' => 'desc'])->limit(($page - 1) * $num, $num)->select();

            //总数
            $count = VersionModel::where($map)->count();
            $data = ['list' => $list, 'total' => $count];

            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //添加/编辑
    public function save()
    {
        try {
            $id = input('id', 0, 'intval');//ID
            $version = input('version', '');
            $content = input('content', '');
            $page = input('page', '');
            $is_update = input('is_update', 1, 'intval');

            if (!$version || !$page) {
                return message("参数错误", false);
            }

            $data = [];
            $data['version'] = $version;
            $data['content'] = $content;
            $data['page'] = $page;
            $data['is_update'] = $is_update;
            if ($id) {//编辑
                $data['update_time'] = time();
                if ($status = VersionModel::where(['id' => $id])->update($data)) {
                    action_log($this->userId, 'device', '设备更新操作', '编辑设备更新', $id);
                    return message("编辑成功", true);
                } else {
                    return message("编辑失败", false);
                }
            } else {
                $data['add_time'] = time();
                if ($new = VersionModel::create($data)) {
                    action_log($this->userId, 'device', '设备更新操作', '添加设备更新', $new['id']);
                    return message("添加成功", true);
                } else {
                    return message("添加失败", false);
                }
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

            $info = VersionModel::where(['id' => $id])->find();

            return message("获取成功", true, $info, 200);
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

            if ($status = VersionModel::where(['id' => $id])->delete()) {
                action_log($this->userId, 'device', '设备更新操作', '删除设备更新', $id);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
