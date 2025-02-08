<?php

namespace app\admin\middleware;

use app\common\model\Manage as ManageModel;
use think\Response;

/**
 * 登录校验中间件
 * @author 鱼鱼鱼
 * @since 2023年8月3日14:24:07
 * Class CheckLogin
 * @package app\admin\middleware
 */
class CheckLogin
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 登录校验
        if (!in_array(request()->controller(), ['Login'])) {
            // 获取Token
            $token = request()->header("Authorization");
            if ($token) {
                $user = ManageModel::where(['token' => $token])->find();
                if (!$user) {
                    // token解析失败跳转登录页面
                    return message("登录失效~", false, null, 401);
                }
            } else {
                // 跳转至登录页面
                return message("登录失效，请登录", false, null, 401);
            }
        }
        return $next($request);
    }
}
