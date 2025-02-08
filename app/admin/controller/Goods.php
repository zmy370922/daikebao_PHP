<?php

namespace app\admin\controller;

use app\common\model\Goods as GoodsModel;


/**
 * 后台-商品管理
 * @author 鱼鱼鱼
 * @since 2023年8月21日10:36:00
 * Class Goods
 * @package app\admin\controller
 */
class Goods extends Backend
{
    //列表
    public function goods_list()
    {
        try {
            $keywords = input('keywords', '');//商品搜索
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($keywords) {
                $map[] = ['id|title', 'like', '%' . $keywords . '%'];
            }
            $map[] = ['is_del', '=', 1];

            $list = GoodsModel::where($map)->order(['sort' => 'desc'])->limit(($page - 1) * $num, $num)->select();

            //总数
            $count = GoodsModel::where($map)->count();
            $data = ['list' => $list, 'total' => $count];

            return message("获取成功", true, $data);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    /**
     * 保存新建或编辑
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        try {
            $id = input('id', 0, 'intval');//id
            $title = input('title', '');
            $cover = input('cover', '');
            $is_show = input('is_show', 1, 'intval');//是否上架
            $sort = input('sort', 0, 'intval');//排序
            $price = input('price', 0, 'intval');//小红心数
            $detail = input('detail', '');//商品详情

            if (!$title || !$cover || !$price || !$detail) {
                return message("参数错误", false);
            }

            $data = [];
            $data['title'] = $title;
            $data['cover'] = $cover;
            $data['is_show'] = $is_show;
            $data['price'] = $price;
            $data['detail'] = $detail;
            $data['sort'] = $sort;
            if (!$id) {
                $data['add_time'] = time();
                if ($new = GoodsModel::create($data)) {
                    action_log($this->userId, 'goods', '商品操作', '添加商品', $new['id']);
                    return message('添加成功', true);
                } else {
                    return message('添加失败', false);
                }
            } else {
                $data['update_time'] = time();
                if ($status = GoodsModel::where(['id' => $id])->update($data)) {
                    action_log($this->userId, 'goods', '商品操作', '编辑商品', $id);
                    return message('编辑成功', true);
                } else {
                    return message('编辑失败', false);
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

            $info = GoodsModel::where(['id' => $id])->find();

            return message("获取成功", true, $info, 200);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }


    //商品设置
    public function opera()
    {
        try {
            $id = input('id', 0, 'intval');
            $is_show = input('is_show', 1, 'intval');//状态 1：上架 2：下架
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
            if ($status = GoodsModel::where(['id' => $id])->update($data)) {
                action_log($this->userId, 'goods', '商品操作', '商品设置', $id);
                return message("操作成功", true);
            } else {
                return message("操作失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }


    //商品删除
    public function del()
    {
        try {
            $id = input('id', 0, 'intval');

            if (!$id) {
                return message("参数错误", false);
            }

            if ($status = GoodsModel::where(['id' => $id])->update(['is_del' => 2])) {
                action_log($this->userId, 'goods', '商品操作', '删除商品', $id);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
