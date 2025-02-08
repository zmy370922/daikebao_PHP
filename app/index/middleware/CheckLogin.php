<?php

namespace app\index\middleware;

use app\common\model\User as UserModel;
use think\Response;

/**
 * 登录校验中间件
 * @author 鱼鱼鱼
 * @since 2023年6月16日10:34:29
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
//        if (!in_array(request()->controller(), ['Login'])) {
//            // 获取Token
//            $token = request()->header("Authorization");
//            if ($token) {
//                $user = UserModel::where(['openid' => $token, 'is_del' => 1])->find();
//                if (!$user) {
//                    // token解析失败跳转登录页面
//                    return message("登录状态失效", false, [], 401);
//                }
//
//                if ($user['status'] == 4) {
//                    return message("禁止登录", false);
//                }
//
//                if ($user['status'] == 5) {
//                    return message("账号已注销", false);
//                }
//            } else {
//                // 跳转至登录页面
//                return message("请先登录", false, [], 401);
//            }
//        }
        return $next($request);
    }
}
