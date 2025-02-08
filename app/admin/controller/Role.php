<?php

namespace app\admin\controller;

use app\common\model\SysMenu as SysMenuModel;
use app\common\model\SysRole as SysRoleModel;
use app\common\model\SysRoleMenu as SysRoleMenuModel;
use think\facade\Db;

/**
 * 后台-角色管理
 * @author 鱼鱼鱼
 * @since 2023年8月4日14:22:18
 * Class Role
 * @package app\admin\controller
 */
class Role extends Backend
{
    //根据角色获取管理员菜单
    public function admin_menu()
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

        return message("获取成功", true, $menu_list, 200);
    }

    //列表
    public function role_list()
    {
        try {
            //角色名称
            $role_name = input('search', '');
            $page = input('pageNum', 0, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($role_name) {
                $role_name = trim($role_name);
                $map[] = ['role_name', 'like', '%' . $role_name . '%'];
            }
            $map[] = ['is_del', '=', 1];

            $list = SysRoleModel::where($map)->order(['role_sort' => 'asc'])->limit(($page - 1) * $num, $num)->select();

            return message("获取成功", true, $list, 200);
        } catch (\Exception $e) {
            return message('错误：' . $e->getMessage(), false);
        }
    }

    //添加
    public function add()
    {
        try {
            $role_name = input('role_name', '');
            $role_key = input('role_key', '');
            $role_sort = input('role_sort', 0, 'intval');
            $status = input('status', 1, 'intval');
            $remark = input('remark', '');
            $menuIds = input('menuIds', []);
            if (!$role_name || !$menuIds) {
                return message("参数错误", false);
            }

            sort($menuIds);

            Db::startTrans();
            $data = [];
            $data['role_name'] = $role_name;
            $data['role_key'] = $role_key;
            $data['role_sort'] = $role_sort;
            $data['status'] = $status;
            $data['remark'] = $remark;
            $data['add_time'] = time();
            $role_id = Db::name('sys_role')->insertGetId($data);

            $role_menu = [];
            foreach ($menuIds as $menu) {
                $arr['role_id'] = $role_id;
                $arr['menu_id'] = $menu;
                $role_menu[] = $arr;
            }

            $add_status = Db::name('sys_role_menu')->insertAll($role_menu);

            if ($role_id && $add_status) {
                // 提交事务
                Db::commit();
                action_log($this->userId, 'sys_role', '角色操作', '添加系统角色', $role_id);
                return message("添加成功", true);
            } else {
                // 回滚事务
                Db::rollback();
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
            $role_id = input('role_id', 0, 'intval');
            $menu_ids = SysRoleMenuModel::where(['role_id' => $role_id])->column('menu_id');

            return message("操作成功", true, $menu_ids, 200);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //编辑
    public function edit()
    {
        try {
            $role_id = input('role_id', 0, 'intval');
            $role_name = input('role_name', '');
            $role_key = input('role_key', '');
            $role_sort = input('role_sort', 0, 'intval');
            $status = input('status', 1, 'intval');
            $remark = input('remark', '');
            $menuIds = input('menuIds', []);
            if (!$role_id || !$role_name || !$menuIds) {
                return message("参数错误", false);
            }

            sort($menuIds);

            Db::startTrans();
            $data = [];
            $data['role_name'] = $role_name;
            $data['role_key'] = $role_key;
            $data['role_sort'] = $role_sort;
            $data['status'] = $status;
            $data['remark'] = $remark;
            $data['update_time'] = time();
            $update_status = Db::name('sys_role')->where(['role_id' => $role_id])->update($data);

            $delete_status = Db::name('sys_role_menu')->where(['role_id' => $role_id])->delete();

            $role_menu = [];
            foreach ($menuIds as $menu) {
                $arr['role_id'] = $role_id;
                $arr['menu_id'] = $menu;
                $role_menu[] = $arr;
            }

            $add_status = Db::name('sys_role_menu')->insertAll($role_menu);

            if ($update_status && $delete_status !== false && $add_status) {
                // 提交事务
                Db::commit();
                action_log($this->userId, 'sys_role', '角色操作', '编辑系统角色', $role_id);
                return message("编辑成功", true);
            } else {
                // 回滚事务
                Db::rollback();
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
            $role_id = input('id', 0, 'intval');

            if (!$role_id) {
                return message("参数错误", false);
            }

            $data = [];
            $data['is_del'] = 2;
            if ($status = SysRoleModel::where(['role_id' => $role_id])->update($data)) {
                action_log($this->userId, 'sys_role', '角色操作', '删除系统角色', $role_id);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //菜单列表
    public function menu_list()
    {
        $map[] = ['parentId', '=', 0];
        $map[] = ['is_del', '=', 1];

        $list = SysMenuModel::where($map)->order(['orderNum' => 'asc'])->select();
        $menu_list = [];
        foreach ($list as $value) {
            $menu = [];
            $menu['id'] = $value['menuId'];
            $menu['label'] = $value['menuName'];

            //子菜单
            $where = [];
            $where[] = ['parentId', '=', $value['menuId']];
            $where[] = ['is_del', '=', 1];
            $children_list = SysMenuModel::where($where)->order(['orderNum' => 'asc'])->select();
            if ($children_list) {
                $children_menu = [];
                foreach ($children_list as $val) {
                    $sub_menu = [];
                    $sub_menu['id'] = $val['menuId'];
                    $sub_menu['label'] = $val['menuName'];
                    $children_menu[] = $sub_menu;
                }
                $menu['children'] = $children_menu;
            } else {
                $menu['children'] = [];
            }
            $menu_list[] = $menu;
        }
        return message("获取成功", true, $menu_list, 200);
    }
}
