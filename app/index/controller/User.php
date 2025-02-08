<?php
/**
 * Created by
 * User: 鱼鱼鱼
 * Date: 2023年6月25日
 * Time: 16:02:54
 * desc: 设备相关
 */

namespace app\index\controller;

use app\common\model\DeviceBind as DeviceBindModel;
use app\common\model\User as UserModel;
use app\common\model\Video as VideoModel;

class User extends Backend
{
    //推荐视频列表
    public function video_list()
    {
        try {
            $page = input('pageNum', 1, 'intval');
            $num = input('pageSize', 10, 'intval');

            $map = [];
            $map[] = ['is_show', '=', 1];
            $map[] = ['is_del', '=', 1];
            $list = VideoModel::where($map)->order(['sort' => 'desc'])->limit(($page - 1) * $num, $num)->select();

            return message("获取成功", true, $list);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //推荐视频详情
    public function video_detail()
    {
        try {
            $video_id = input('video_id', 0, 'intval');
            if (!$video_id) {
                return message('参数错误', false);
            }

            $map = [];
            $map[] = ['id', '=', $video_id];
            $map[] = ['is_show', '=', 1];
            $map[] = ['is_del', '=', 1];

            $detail = VideoModel::where($map)->find();
            if (!$detail) {
                return message('视频已下架', false);
            }
            return message('获取成功', true, $detail);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }


    //用户信息
    public function info()
    {
        try {
            $info = $this->userInfo;
            $device=DeviceBindModel::with(['device'])->where('uid','=',$info['id'])->where('status','=',1)->where('type','=',1)->find();
            $info['device']=$device;
            return message("获取成功", true, $info);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //用户信息编辑保存
    public function edit()
    {
        try {
            $avatar = input('avatar', '');//头像
            $nickname = input('nickname', '');//昵称
            if (!$avatar || !$nickname) {
                return message('参数错误', false);
            }

            $data = [];
            $data['avatar'] = $avatar;
            $data['nickname'] = $nickname;
            $data['update_time'] = time();
            $res = UserModel::where(['id' => $this->userId])->update($data);
            if ($res) {
                return message("编辑成功", true);
            } else {
                return message("编辑失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
