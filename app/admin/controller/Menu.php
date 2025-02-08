<?php

namespace app\admin\controller;

use app\common\model\SysMenu as SysMenuModel;

/**
 * 后台-菜单管理
 * @author 鱼鱼鱼
 * @since 2023年8月4日14:21:57
 * Class Menu
 * @package app\admin\controller
 */
class Menu extends Backend
{
    //菜单列表
    public function menu_list()
    {
        try {
            //菜单名称
            $menu_name = input('name', '');
            $list = $this->get_list($menu_name);

            return message("获取成功", true, $list, 200);
        } catch (\Exception $e) {
            return message('错误：' . $e->getMessage(), false);
        }
    }

    //菜单添加
    public function add()
    {
        try {
            $parentId = input('parentId', 0, 'intval');//上级菜单
            $menuName = input('menuName', '');//菜单名称
            $path = input('path', '');//路由地址
            $component = input('component', '');//组件地址
            $query = input('query', '');//路由参数
            $icon = input('icon', '');//菜单图标
            $orderNum = input('orderNum', 1, 'intval');//菜单排序
            $menuType = input('menuType', '');//菜单类型
            $isFrame = input('isFrame', 1, 'intval');//外链
            $isCache = input('isCache', 0, 'intval');//缓存
            $isAffix = input('isAffix', 0, 'intval');//固定
            $visible = input('visible', 0, 'intval');//显示
            $status = input('status', 1, 'intval');//菜单状态

            $data = [];
            $data['menuName'] = $menuName;
            $data['parentId'] = $parentId;
            $data['path'] = $path;
            $data['component'] = $component;
            $data['query'] = $query;
            $data['icon'] = $icon;
            $data['orderNum'] = $orderNum;
            $data['menuType'] = $menuType;
            $data['isFrame'] = $isFrame;
            $data['isCache'] = $isCache;
            $data['isAffix'] = $isAffix;
            $data['visible'] = $visible;
            $data['status'] = $status;
            $data['add_time'] = time();
            if ($new = SysMenuModel::create($data)) {
                action_log($this->userId, 'sys_menu', '菜单操作', '添加系统菜单', $new['id']);
                return message("添加成功", true, $new);
            } else {
                return message("添加失败", false);
            }
        } catch (\Exception $e) {
            return message('错误：' . $e->getMessage(), false);
        }
    }

    //信息
    public function info()
    {
        try {
            $menuId = input('menuId', 0, 'intval');

            if (!$menuId) {
                return message("参数错误", false);
            }

            $map = [];
            $map[] = ['menuId', '=', $menuId];
            $info = SysMenuModel::where($map)->find();
            //上级菜单
            if ($info['parentId']) {
                $menuSuperior = SysMenuModel::where(['menuId' => $info['parentId']])->value('menuName');
                $info['menuSuperior'] = [$menuSuperior];
            } else {
                $info['menuSuperior'] = [];
            }

            $result = [];
            $result['menu_list'] = $this->get_list('');
            $result['info'] = $info;

            return message("操作成功", true, $result);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //编辑
    public function edit()
    {
        try {
            $menuId = input('menuId', 0, 'intval');
            $parentId = input('parentId', 0, 'intval');//上级菜单
            $menuName = input('menuName', '');//菜单名称
            $path = input('path', '');//路由地址
            $component = input('component', '');//组件地址
            $query = input('query', '');//路由参数
            $icon = input('icon', '');//菜单图标
            $orderNum = input('orderNum', 1, 'intval');//菜单排序
            $menuType = input('menuType', '');//菜单类型
            $isFrame = input('isFrame', 1, 'intval');//外链
            $isCache = input('isCache', 0, 'intval');//缓存
            $isAffix = input('isAffix', 0, 'intval');//固定
            $visible = input('visible', 0, 'intval');//显示
            $status = input('status', 1, 'intval');//菜单状态

            if (!$menuId) {
                return message("参数错误", false);
            }

            $data = [];
            $data['parentId'] = $parentId;
            $data['menuName'] = $menuName;
            $data['path'] = $path;
            $data['component'] = $component;
            $data['query'] = $query;
            $data['icon'] = $icon;
            $data['orderNum'] = $orderNum;
            $data['menuType'] = $menuType;
            $data['isFrame'] = $isFrame;
            $data['isCache'] = $isCache;
            $data['isAffix'] = $isAffix;
            $data['visible'] = $visible;
            $data['status'] = $status;
            $data['update_time'] = time();
            if ($res = SysMenuModel::where(['menuId' => $menuId])->update($data)) {
                action_log($this->userId, 'sys_menu', '菜单操作', '编辑系统菜单', $menuId);
                return message("编辑成功", true);
            } else {
                return message("编辑失败", false);
            }
        } catch (\Exception $e) {
            return message('错误：' . $e->getMessage(), false);
        }
    }

    //删除
    public function del()
    {
        try {
            $menuId = input('id', 0, 'intval');

            if (!$menuId) {
                return message("参数错误", false);
            }

            $data = [];
            $data['is_del'] = 2;
            if ($status = SysMenuModel::where(['menuId' => $menuId])->update($data)) {
                action_log($this->userId, 'sys_menu', '菜单操作', '删除系统菜单', $menuId);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    private function get_list($menu_name)
    {
        //菜单名称
        $map = [];
        if ($menu_name) {
            $menu_name = trim($menu_name);
            $map[] = ['menuName', 'like', '%' . $menu_name . '%'];
        }
        $map[] = ['parentId', '=', 0];
        $map[] = ['is_del', '=', 1];

        $list = SysMenuModel::where($map)->order(['orderNum' => 'asc'])->select();
        foreach ($list as &$value) {
            $value['name'] = $value['path'];
            $value['meta'] = ['isKeepAlive' => true];
            //子菜单
            $where = [];
            $where[] = ['parentId', '=', $value['menuId']];
            $where[] = ['is_del', '=', 1];
            $children_list = SysMenuModel::where($where)->order(['orderNum' => 'asc'])->select();
            if ($children_list) {
                $children_menu = [];
                foreach ($children_list as &$val) {
                    $val['name'] = $val['path'];
                    if ($val['parentId']) {
                        $menuSuperior = [];
                        $menuSuperior[] = $val['parentId'];
                        $val['menuSuperior'] = $menuSuperior;
                    } else {
                        $val['menuSuperior'] = [];
                    }
                    $meta = [];
                    $meta['title'] = '';
                    $meta['isLink'] = '';
                    $meta['isHide'] = false;
                    $meta['isKeepAlive'] = false;
                    $meta['isIframe'] = false;
                    $meta['roles'] = ['admin', 'common'];
                    $meta['icon'] = $val['icon'];
                    $val['meta'] = $meta;
                    $children_menu[] = $val;
                }
                $value['children'] = $children_menu;
            } else {
                $value['children'] = [];
            }
        }
        return $list;
    }
}
