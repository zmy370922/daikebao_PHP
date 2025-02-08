<?php

namespace app\tearcher\controller;


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


        $menu_list = [];


        //首页
        $menu['name'] = "Home";
        $menu['path'] = '/home';
        $menu['hidden'] = false;
        $menu['component'] ='home/index';
        $meta = [];
        $meta['title'] = '首页';
        $meta['icon'] = "iconfont icon-shuju";
        $meta['noCache'] = false;
        $meta['link'] = null;
        $meta['isLink'] = "";
        $meta['isHide'] = false;
        $meta['isKeepAlive'] = true;
        $meta['isAffix'] = false;
        $meta['isIframe'] = false;
        $meta['roles'] = null;
        $menu['meta'] = $meta;
        $menu_list[]=$menu;
        if($userInfo['type']==1){
            //教师管理端
            $tearcher['name'] = "Tearcher";
            $tearcher['path'] = '/tearcher';
            $tearcher['hidden'] = false;
            $tearcher['component'] ='tearcher/index';
            $tearcher_meta = [];
            $tearcher_meta['title'] = '老师列表';
            $tearcher_meta['icon'] = "iconfont icon-gerenzhongxin";
            $tearcher_meta['noCache'] = false;
            $tearcher_meta['link'] = null;
            $tearcher_meta['isLink'] = "";
            $tearcher_meta['isHide'] = false;
            $tearcher_meta['isKeepAlive'] = true;
            $tearcher_meta['isAffix'] = false;
            $tearcher_meta['isIframe'] = false;
            $tearcher_meta['roles'] = null;
            $tearcher['meta'] = $tearcher_meta;
            $menu_list[]=$tearcher;

            //设备列表
            $device['name'] = "Device";
            $device['path'] = '/device';
            $device['hidden'] = false;
            $device['component'] ='tearcher/device';
            $device_meta = [];
            $device_meta['title'] = '设备列表';
            $device_meta['icon'] = "iconfont icon-gerenzhongxin";
            $device_meta['noCache'] = false;
            $device_meta['link'] = null;
            $device_meta['isLink'] = "";
            $device_meta['isHide'] = false;
            $device_meta['isKeepAlive'] = true;
            $device_meta['isAffix'] = false;
            $device_meta['isIframe'] = false;
            $device_meta['roles'] = null;
            $device['meta'] = $device_meta;
            $menu_list[]=$device;

        }


        //班级列表
        $grade['name'] = "Grade";
        $grade['path'] = '/grade';
        $grade['hidden'] = false;
        $grade['component'] ='grade/index';
        $grade_meta = [];
        $grade_meta['title'] = '班级列表';
        $grade_meta['icon'] = "iconfont icon-gerenzhongxin";
        $grade_meta['noCache'] = false;
        $grade_meta['link'] = null;
        $grade_meta['isLink'] = "";
        $grade_meta['isHide'] = false;
        $grade_meta['isKeepAlive'] = true;
        $grade_meta['isAffix'] = false;
        $grade_meta['isIframe'] = false;
        $grade_meta['roles'] = null;
        $grade['meta'] = $grade_meta;
        $menu_list[]=$grade;




        return message("获取成功", true, $menu_list, 200);
    }





}
