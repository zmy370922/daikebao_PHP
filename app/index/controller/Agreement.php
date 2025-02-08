<?php

namespace app\index\controller;

use app\common\model\Website as WebsiteModel;

use app\BaseController;
use think\facade\View;

class Agreement extends BaseController
{
    //用户协议
    public function user_service()
    {
        $webset = WebsiteModel::field('user_service')->where(['id' => 1])->find();
        // 模板输出
        View::assign('webset', $webset);
        return View::fetch();
    }

    //隐私政策
    public function user_agreement()
    {
        $webset = WebsiteModel::field('user_agreement')->where(['id' => 1])->find();
        // 模板输出
        View::assign('webset', $webset);
        return View::fetch();
    }
}
