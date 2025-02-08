<?php

namespace app\tearcher\controller;

use app\BaseController;
use think\facade\Filesystem;

class Upload extends BaseController
{
    //系统上传图片文件
    public function img()
    {
        // 捕获异常
        try {
            $file = request()->file('file');
            //此时可能会报错 比如:上传的文件过大,超出了配置文件中限制的大小
            validate(['imgFile' => [
                'fileSize' => 410241024,
                'fileExt' => 'jpg,jpeg,png,bmp,gif',
                'fileMime' => 'image/jpeg,image/png,image/gif', //这个一定要加上很重要！
            ]])->check(['imgFile' => $file]);

            //修改上传路径：config/filesystem.php
            //use think\facade\Filesystem;
            $savename = Filesystem::disk('public')->putFile('/uploads/images', $file);
            $savename = '/' . $savename;
            $data = [];
            $data['msg'] = 'success';
            $data['path'] = $savename;
            $data['full_path'] = 'https://' . $_SERVER['HTTP_HOST'] . $savename;
            return message("上传成功", true, $data, 200);
        } catch (\Exception $e) {
            $data = [];
            $data['msg'] = $e->getMessage();
            $data['path'] = '';
            return message("上传失败", false, $data, 500);
        }
    }

    //系统上传视频文件
    public function video()
    {
        // 捕获异常
        try {
            $file = request()->file('file');
            //此时可能会报错 比如:上传的文件过大,超出了配置文件中限制的大小
//            validate(['imgFile' => [
//                'fileSize' => 410241024,
//                'fileExt' => 'jpg,jpeg,png,bmp,gif',
//                'fileMime' => 'image/jpeg,image/png,image/gif', //这个一定要加上很重要！
//            ]])->check(['imgFile' => $file]);

            //修改上传路径：config/filesystem.php
            //use think\facade\Filesystem;
            $savename = Filesystem::disk('public')->putFile('/uploads/video', $file);
            $savename = '/' . $savename;
            $data = [];
            $data['msg'] = 'success';
            $data['path'] = $savename;
            $data['full_path'] = 'https://' . $_SERVER['HTTP_HOST'] . $savename;
            return message("上传成功", true, $data, 200);
        } catch (\Exception $e) {
            $data = [];
            $data['msg'] = $e->getMessage();
            $data['path'] = '';
            return message("上传失败", false, $data, 500);
        }
    }
    public function annex()
    {
        // 捕获异常
        try {
            $file = request()->file('file');
            //此时可能会报错 比如:上传的文件过大,超出了配置文件中限制的大小

            //修改上传路径：config/filesystem.php
            //use think\facade\Filesystem;
            $savename = Filesystem::disk('public')->putFileAs('/uploads/annex', $file,$file->getOriginalName());
            $savename = '/' . $savename;
            $data = [];
            $data['msg'] = 'success';
            $data['path'] = $savename;
            $data['full_path'] = 'https://' . $_SERVER['HTTP_HOST'] . $savename;
            return message("上传成功", true, $data, 200);
        } catch (\Exception $e) {
            $data = [];
            $data['msg'] = $e->getMessage();
            $data['path'] = '';
            return message("上传失败", false, $data, 500);
        }
    }

}