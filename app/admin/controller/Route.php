<?php

namespace app\admin\controller;

use app\common\model\SysMenu as SysMenuModel;
use app\common\model\SysRoleMenu as SysRoleMenuModel;

/**
 * 后台-动态路由
 * @author 鱼鱼鱼
 * @since 2023年8月3日14:28:44
 * Class Route
 * @package app\admin\controller
 */
class Route extends Backend
{
    //根据菜单动态获取系统菜单
    public function menu_list()
    {
        $userInfo = $this->userInfo;

        $map = [];
        $menu_id = SysRoleMenuModel::where(['role_id' => $userInfo['role_id']])->column('menu_id');
        $map[] = ['menuId', 'in', $menu_id];
        $map[] = ['parentId', '=', 0];
        $map[] = ['is_del', '=', 1];

        $list = SysMenuModel::where($map)->order(['orderNum' => 'asc'])->select();
        $menu_list = [];
        foreach ($list as $value) {
            $menu = [];
            if (in_array($value['path'], ['home', 'personal'])) {
                $menu['name'] = ucwords($value['path']);
                $menu['path'] = '/' . $value['path'];
                $menu['hidden'] = false;
                $menu['component'] = $value['component'];
                $meta = [];
                $meta['title'] = $value['menuName'];
                $meta['icon'] = $value['icon'];
                $meta['noCache'] = false;
                $meta['link'] = null;
                $meta['isLink'] = "";
                $meta['isHide'] = false;
                $meta['isKeepAlive'] = true;
                $meta['isAffix'] = false;
                $meta['isIframe'] = false;
                $meta['roles'] = null;
                $menu['meta'] = $meta;
            } else {
                $menu['name'] = ucwords($value['path']);
                $menu['path'] = '/' . $value['path'];
                $menu['hidden'] = false;
                $menu['redirect'] = "noRedirect";
                $menu['component'] = $value['component'];
                $menu['alwaysShow'] = true;
                $meta = [];
                $meta['title'] = $value['menuName'];
                $meta['icon'] = $value['icon'];
                $meta['noCache'] = false;
                $meta['link'] = null;
                $meta['isLink'] = "";
                $meta['isHide'] = false;
                $meta['isKeepAlive'] = true;
                $meta['isAffix'] = false;
                $meta['isIframe'] = false;
                $meta['roles'] = null;
                $menu['meta'] = $meta;
                //子菜单
                $where = [];
                $where[] = ['parentId', '=', $value['menuId']];
                $where[] = ['is_del', '=', 1];
                $where[] = ['menuId', 'in', $menu_id];
                $children_list = SysMenuModel::where($where)->order(['orderNum' => 'asc'])->select();
                if ($children_list) {
                    $children_menu = [];
                    foreach ($children_list as $val) {
                        $sub_menu = [];
                        $sub_menu['name'] = $val['path'];
                        $sub_menu['path'] = $val['path'];
                        $sub_menu['hidden'] = false;
                        $sub_menu['component'] = $val['component'];
                        $sub_meta = [];
                        $sub_meta['title'] = $val['menuName'];
                        $sub_meta['icon'] = $val['icon'];
                        $sub_meta['noCache'] = false;
                        $sub_meta['link'] = null;
                        $sub_meta['isLink'] = "";
                        $sub_meta['isHide'] = false;
                        $sub_meta['isKeepAlive'] = true;
                        $sub_meta['isAffix'] = false;
                        $sub_meta['isIframe'] = false;
                        $sub_meta['roles'] = null;
                        $sub_menu['meta'] = $sub_meta;
                        $children_menu[] = $sub_menu;
                    }
                    $menu['children'] = $children_menu;
                } else {
                    $menu['children'] = [];
                }
            }
            $menu_list[] = $menu;
        }
        return message("获取成功", true, $menu_list);
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
