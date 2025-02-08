<?php

namespace app\index\controller;

use app\common\model\User as UserModel;
use app\common\model\DeviceBind as DeviceBindModel;
use app\common\model\NewDeviceSet as NewDeviceSetModel;
use app\BaseController;
use think\facade\Config;
use think\facade\Cache;

class Login extends BaseController
{
    //登录
    public function login()
    {
        try {
            $code = input('code', '');
            if (!$code) {
                return message("参数错误", false);
            }

            $config = Config::get('weixinpay');
            $appid = $config['APPID'];
            $secret = $config['APPSECRET'];
            $get_token_url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $get_token_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $res = curl_exec($ch);
            curl_close($ch);
            $json_obj = json_decode($res, true);
            //获取openid
            $openid = $json_obj['openid'];

            $user = UserModel::where(['openid' => $openid, 'is_del' => 1])->find();
            if ($openid) {
                if ($user) {
                    $device=DeviceBindModel::with(['device'])->where('uid','=',$user['id'])->where('type','=',1)->find();
                    $user['device']=$device;
                    if($device['device']){
                        $set= NewDeviceSetModel::where('device_id','=',$device['device']['id'])->find();
                        if($set){
                            $user['is_set']=1;
                        }else{
                            $user['is_set']=0;
                        }
                    }else{
                        $user['is_set']=0;
                    }
                    UserModel::where('id','=',$user['id'])->inc('login_num')->update();
                    return message("登录成功", true, $user);
                } else {
                    $user_data = [];
                    $user_data['openid'] = $openid;
                    $user_data['nickname'] = get_nickname();
                    $user_data['add_time'] = time();
                    $user_data['login_num'] =1;
                    if ($new = UserModel::create($user_data)) {
                        $new['device']='';
                        $new['is_set']=0;
                        return message("登录成功", true, $new);
                    } else {
                        return message("登录失败", false);
                    }
                }
            } else {
                return message("获取失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //openid用户注册
    public function reg()
    {
        try {
            $openid = input('openid', '');
            $avatar = input('avatar', '');
            $nickname = input('nickname', '');
            if(!$nickname){
                $nickname=get_nickname();
            }
            if (!$openid) {
                return message("参数错误", false);
            }

            $user = UserModel::where(['openid' => $openid, 'is_del' => 1])->find();
            if ($user) {
                $user_data = [];
                $user_data['avatar'] = $avatar;
                $user_data['nickname'] = $nickname;
                UserModel::where(['id' => $user['id']])->update($user_data);
                $user_info = UserModel::where(['id' => $user['id']])->find();
                return message("登录成功", true, $user_info);
            } else {
                $user_data = [];
                $user_data['openid'] = $openid;
                $user_data['avatar'] = $avatar;
                $user_data['nickname'] = $nickname;
                $user_data['status'] = 2;
                $user_data['add_time'] = time();
                if ($new = UserModel::create($user_data)) {
                    return message("注册成功", true, $new);
                } else {
                    return message("注册失败", false);
                }
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }

    //code用户注册
    public function register()
    {
        try {
            $code = input('code', '');
            $openid = input('openid', '');
            $avatar = input('avatar', '');
            $nickname = input('nickname', '');
            if (!$code || !$openid) {
                return message("参数错误", false);
            }

            $token = Cache::get('weixin_token');
            if (isset($token['token']) && $token['token'] != '' && isset($token['expire_time']) && $token['expire_time'] > time()) {
                $accessToken = $token['token'];
            } else {
                $config = Config::get('weixinpay');
                $appid = $config['APPID'];
                $secret = $config['APPSECRET'];
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
                $user_obj = json_decode(file_get_contents($url), true);
                $accessToken = $user_obj['access_token'];
                $data = array(
                    'token' => $accessToken,
                    'create_time' => time(),
                    'expire_time' => time() + 3600,

                );
                Cache::set("weixin_token", $data);
            }

            $get_token_url = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=' . $accessToken;
            $header = array();
            $header[] = 'Content-Type:application/json';

            $json_data = [];
            $json_data['code'] = $code;

            //初始化
            $curl = curl_init();
            //设置抓取的url
            curl_setopt($curl, CURLOPT_URL, $get_token_url);
            //设置头文件的信息作为数据流输出
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            //设置获取的信息以文件流的形式返回，而不是直接输出。
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            //设置post方式提交
            curl_setopt($curl, CURLOPT_POST, 1);
            //设置post数据
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($json_data));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            //执行命令
            $json = curl_exec($curl);

            //关闭URL请求
            curl_close($curl);

            $result = json_decode($json, true);

            if (isset($result['errcode']) && $result['errcode'] == 0) {
                $user = UserModel::where(['openid' => $openid, 'is_del' => 1])->find();
                $phone_info = $result['phone_info'];
                $purePhoneNumber = $phone_info['purePhoneNumber'];
                if ($user) {
                    $user_data = [];
                    $user_data['mobile'] = $purePhoneNumber;
                    $user_data['avatar'] = $avatar;
                    $user_data['nickname'] = $nickname;
                    $user_data['login_num'] = $user['login_num']+1;
                    UserModel::where(['id' => $user['id']])->update($user_data);
                    $user_info = UserModel::where(['id' => $user['id']])->find();
                    $device=DeviceBindModel::with(['device'])->where('uid','=',$user_info['id'])->where('status','=',1)->where('type','=',1)->find();
                    $user_info['device']=$device;
                    if($device['device']){
                        $set= NewDeviceSetModel::where('device_id','=',$device['device']['id'])->find();
                        if($set){
                            $user_info['is_set']=1;
                        }else{
                            $user_info['is_set']=0;
                        }
                    }else{
                        $user_info['is_set']=0;
                    }



                    return message("登录成功", true, $user_info);
                } else {
                    $user_data = [];
                    $user_data['openid'] = $openid;
                    $user_data['mobile'] = $purePhoneNumber;
                    $user_data['avatar'] = $avatar;
                    $user_data['nickname'] = $nickname;
                    $user_data['add_time'] = time();
                    $user_data['login_num'] =1;
                    if ($new = UserModel::create($user_data)) {
                        $new['device']='';
                        $new['is_set']=0;
                        return message("注册成功", true, $new);
                    } else {
                        return message("注册失败", false);
                    }
                }
            } else {
                return message("获取失败", false);
            }
        } catch (\Exception $e) {
            return message("错误：" . $e->getMessage(), false);
        }
    }
}
