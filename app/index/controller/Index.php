<?php
namespace app\index\controller;

use app\BaseController;
use app\common\model\Website as WebsiteModel;
use think\facade\Config;

class Index extends BaseController
{
    public function index()
    {
        echo 'index';
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
    public function device(){
        echo '请用为微信扫一扫，扫描二维码';
    }

    //平台配置
    public function cfg()
    {
        try {
            $webset = WebsiteModel::where(['id' => 1])->find();

            return message("获取成功", true, $webset);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //设备默认数据
    public function device_cfg()
    {
        try {
           $info=Config::get('device');

            return message("获取成功", true, $info);
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    public function test(){

        $array = array(
            'touser' => 'oijuH63zsmo1wSWo-VtU60QYjvWI',
            'template_id' => 'ePjBdASKOq-g0997-GvLP1PmMbZ5TYUpYDC1p3Hqlm8',
            'page' => '/page/index/index',
            'data' => array(
                'thing1' => array('value' => '那迪'),
                'thing3' => array('value' => '有人在聊天窗口向您聊天，快去回复'),
                'time2' => array('value' => '2020-4-29 12:11:20')
            ),
        );
        send_message($array);
    }

    public function test1(){

        $array = array(
            'touser' => 'oijuH63zsmo1wSWo-VtU60QYjvWI',
            'template_id' => 'DIlsPsZf6dPVbbGE2ncqFlwU3VmSSUgSzCiZUmIlCKU',
            'page' => '/page/index/index',
            'data' => array(
                'name3' => array('value' => '那迪'),
                'thing1' => array('value' => '杭州欧卡网络科技有限公司'),
                'thing5' => array('value' => '请及时通过申请'),
                'time2' => array('value' => '2020-4-29 12:11:20')
            ),
        );
        send_message($array);
    }
}
