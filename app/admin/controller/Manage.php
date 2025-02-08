<?php

namespace app\admin\controller;

use app\common\model\Manage as ManageModel;
use app\common\model\User as UserModel;

/**
 * 后台-管理员
 * @author 鱼鱼鱼
 * @since 2023年8月3日14:28:44
 * Class Manage
 * @package app\admin\controller
 */
class Manage extends Backend
{
    //退出登录
    public function logout()
    {
        try {
            $status = ManageModel::where(['id' => $this->userId])->update(['token' => '']);

            if ($status) {
                action_log($this->userId, 'manage', '管理员操作', '管理员退出登录', $this->userId);
                return message("操作成功", true);
            } else {
                return message("操作失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //编辑密码
    public function edit_pwd()
    {
        try {
            $old_pwd = input('old_pwd', '');
            $new_pwd = input('new_pwd', '');
            $confirm_pwd = input('confirm_pwd', '');
            if (!$old_pwd || !$new_pwd || !$confirm_pwd) {
                return message("参数错误", false);
            }

            $userInfo = $this->userInfo;
            if ($userInfo['password'] != md5($old_pwd)) {
                return message("旧密码不正确", false);
            }

            if ($new_pwd !== $confirm_pwd) {
                return message("两次密码不一致，请确认", false);
            }

            $data = [];
            $data['password'] = md5($new_pwd);
            $status = ManageModel::where(['id' => $this->userId])->update($data);
            if ($status !== false) {
                action_log($this->userId, 'manage', '管理员操作', '管理员修改密码', $this->userId);
                return message("修改成功", true);
            } else {
                return message("修改失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //列表
    public function manage_list()
    {
        try {
            $account = input('account', '');//账号
            $username = input('username', '');//昵称
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            if ($account) {
                $account = trim($account);
                $map[] = ['account', 'like', '%' . $account . '%'];
            }

            if ($username) {
                $username = trim($username);
                $map[] = ['username', 'like', '%' . $username . '%'];
            }

            $map[] = ['is_del', '=', 1];
            $list = ManageModel::with(['role'])->where($map)->order(['add_time' => 'asc'])->limit(($page - 1) * $num, $num)->select();
            foreach ($list as &$value) {
                if ($value['avatar']) {
                    $arr['name'] = '头像';
                    if (strpos($value['avatar'], 'http') !== false) {
                        $arr['url'] = $value['avatar'];
                    } else {
                        $arr['url'] = 'http://' . $_SERVER['HTTP_HOST'] . $value['avatar'];
                    }
                    $avatarlist = [];
                    $avatarlist[] = $arr;
                    $value['avatar_list'] = $avatarlist;
                } else {
                    $value['avatar_list'] = [];
                }
            }
            //总数
            $count = ManageModel::where($map)->count();
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
            $account = input('account', '');
            $password = input('password', '');
            $avatar = input('avatar', '');
            $username = input('username', '');
            $status = input('status', 1, 'intval');
            $role_id = input('role_id', 0, 'intval');

            if (!$account || !$password || !$role_id) {
                return message("参数错误", false);
            }

            $info = ManageModel::where(['account' => $account, 'is_del' => 1])->find();
            if ($info) {
                return message("账号已存在", false);
            }

            $data = [];
            $data['account'] = $account;
            $data['password'] = md5($password);
            $data['avatar'] = $avatar;
            $data['username'] = $username;
            $data['role_id'] = $role_id;
            $data['status'] = $status;
            $data['add_time'] = time();
            if ($new = ManageModel::create($data)) {
                action_log($this->userId, 'manage', '管理员操作', '添加管理员账号', $new['id']);
                return message("添加成功", true, $new);
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
            $map[] = ['is_del', '=', 1];
            $info = ManageModel::where($map)->find();

            return message("获取成功", true, $info);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //编辑
    public function edit()
    {
        try {
            $id = input('id', 0, 'intval');
            $account = input('account', '');
            $password = input('password', '');
            $avatar = input('avatar', '');
            $username = input('username', '');
            $status = input('status', 1, 'intval');
            $role_id = input('role_id', 0, 'intval');

            if (!$id || !$account || !$role_id) {
                return message("参数错误", false);
            }

            $info = ManageModel::where(['account' => $account, 'is_del' => 1])->find();
            if ($info && $info['id'] != $id) {
                return message("账号已存在", false);
            }

            $data = [];
            $data['account'] = $account;
            if ($password) {
                $data['password'] = md5($password);
            }
            $data['avatar'] = $avatar;
            $data['username'] = $username;
            $data['role_id'] = $role_id;
            $data['status'] = $status;
            $data['update_time'] = time();
            if ($res = ManageModel::where(['id' => $id])->update($data)) {
                action_log($this->userId, 'manage', '管理员操作', '编辑管理员账号', $id);
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

            $data = [];
            $data['is_del'] = 2;
            if ($status = ManageModel::where(['id' => $id])->update($data)) {
                action_log($this->userId, 'manage', '管理员操作', '删除管理员账号', $id);
                return message("删除成功", true);
            } else {
                return message("删除失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
