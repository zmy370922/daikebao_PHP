<?php

namespace app\admin\controller;

use app\common\model\Video as VideoModel;

/**
 * 后台-推荐视频
 * @author 鱼鱼鱼
 * @since 2023年9月11日10:44:39
 * Class Video
 * @package app\admin\controller
 */
class Video extends Backend
{
    //列表
    public function video_list()
    {
        try {
            $title = input('title', '');//搜索标题
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($title) {
                $map[] = ['title', 'like', '%' . $title . '%'];
            }


            $map[] = ['is_del', '=', 1];
            $list = VideoModel::where($map)->order(['sort' => 'desc'])->limit(($page - 1) * $num, $num)->select();
            //总数
            $count = VideoModel::where($map)->count();
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
            $title = input('title', '');
            $cover = input('cover', '');
            $url = input('url', '1');
            $sort = input('sort', 0, 'intval');
            $is_show = input('is_show', 0, 'intval');

            if (!$title || !$cover || !$url) {
                return message("参数错误", false);
            }

            $data = [];
            $data['title'] = $title;
            $data['cover'] = $cover;
            $data['url'] = $url;
            $data['sort'] = $sort;
            $data['is_show'] = $is_show;
            $data['add_time'] = time();
            if ($new = VideoModel::create($data)) {
                action_log($this->userId, 'video', '推荐视频操作', '添加推荐视频', $new['id']);
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
            $info = VideoModel::where($map)->find();

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
            $title = input('title', '');
            $cover = input('cover', '');
            $url = input('url', '1');
            $sort = input('sort', 0, 'intval');
            $is_show = input('is_show', 0, 'intval');

            if (!$id || !$title || !$cover || !$url) {
                return message("参数错误", false);
            }

            $data = [];
            $data['title'] = $title;
            $data['cover'] = $cover;
            $data['url'] = $url;
            $data['sort'] = $sort;
            $data['is_show'] = $is_show;
            $data['update_time'] = time();
            if ($res = VideoModel::where(['id' => $id])->update($data)) {
                action_log($this->userId, 'video', '推荐视频操作', '编辑推荐视频', $id);
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

            if ($status = VideoModel::where(['id' => $id])->update(['is_del' => 2])) {
                action_log($this->userId, 'video', '推荐视频操作', '删除推荐视频', $id);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //分类设置
    public function opera()
    {
        try {
            $id = input('id', 0, 'intval');
            $is_show = input('is_show', 1, 'intval');//分类状态 1：显示 2：隐藏
            $sort = input('sort', 0, 'intval');//排序

            if (!$id) {
                return message("参数错误", false);
            }

            $data = [];
            if ($is_show) {
                $data['is_show'] = $is_show;
            }

            if ($sort) {
                $data['sort'] = $sort;
            }
            $data['update_time'] = time();
            if ($status = VideoModel::where(['id' => $id])->update($data)) {
                action_log($this->userId, 'video', '推荐视频操作', '推荐视频设置', $id);
                return message("操作成功", true);
            } else {
                return message("操作失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
