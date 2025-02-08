<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

//上传图片
Route::post('/upload/img', 'index/upload/img');

//登录  *code
Route::post('/login/login', 'index/login/login');

//流量充值列表
Route::post('/shop/recharge_list', 'index/shop/recharge_list');

//流量充值下单  recharge_id：充值ID
Route::post('/shop/recharge_order', 'index/shop/recharge_order');

//红心商品列表   pageNum：页数，默认1 pageSize：每页数量
Route::post('/shop/goods_list', 'index/shop/goods_list');

//红心商品详情   goods_id：商品ID
Route::post('/shop/goods_detail', 'index/shop/goods_detail');

//红心商品下单  device_id：当前切换在线的设备ID goods_id：商品ID  quantity：数量 name：收货人姓名  address：地址 mobile：手机号码
Route::post('/shop/goods_order', 'index/shop/goods_order');

//红心商品订单列表   status: 订单状态 0：全部订单,20待发货,30待收货 pageNum：页数，默认1 pageSize：每页数量
Route::post('/shop/order_list', 'index/shop/order_list');

//订单详情   order_id：订单ID
Route::post('/shop/order_detail', 'index/shop/order_detail');

//订单收货完成   order_id：订单ID
Route::post('/shop/order_complete', 'index/shop/order_complete');

//推荐视频列表   pageNum：页数，默认1 pageSize：每页数量
Route::post('/user/video_list', 'index/user/video_list');

//推荐视频详情   video_id：ID
Route::post('/user/video_detail', 'index/user/video_detail');

//用户信息
Route::post('/user/info', 'index/user/info');

//用户信息编辑   avatar：头像  nickname：昵称
Route::post('/user/edit', 'index/user/edit');

//平台配置  weixin：微信号 email：邮箱  phone：联系电话  user_service：隐私政策  user_agreement：用户服务协议
Route::post('/index/cfg', 'index/index/cfg');
//设备配置
Route::post('/index/device_cfg', 'index/index/device_cfg');


//微信支付   openid：登录openid sn：订单编号
Route::post('/weixin/pay', 'index/weixin/pay');

//设备扫码：san_pay：扫码参数
Route::post('/device/san_detail', 'index/device/san_detail');
//实时状态 device_id：设备id
Route::post('/device/real_status', 'index/device/real_status');
//实时状态评价 id：id
Route::post('/device/real_opera', 'index/device/real_opera');
//实时统计 device_id：设备id
Route::post('/device/real_data', 'index/device/real_data');
//绑定设备人数 device_id：设备id
Route::post('/device/device_bind_num', 'index/device/device_bind_num');


//设备信息; device_id：设备id
Route::post('/device/info', 'index/device/info');
//设备绑定device_id：设备id
Route::post('/device/bind', 'index/device/bind');
//设备解绑device_id：设备id
Route::post('/device/unbind', 'index/device/unbind');
//机器人报修 device_id：设备id;reason:原因
Route::post('/device/report', 'index/device/report');
//我绑定的设备列表
Route::post('/device/device_list', 'index/device/device_list');
//设备申请列表
Route::post('/device/shenqing', 'index/device/shenqing');
//设备申请处理；status:处理状态；1是成功，2是拒绝；id:绑定的id
Route::post('/device/device_status', 'index/device/device_status');
//我绑定的设备设置别名
Route::post('/device/device_alias', 'index/device/device_alias');

//设备总评 参数：device_id，设备id
Route::post('/device/zongping', 'index/device/zongping');

//设备基础数据 参数：device_id，设备id
Route::post('/device/device_set', 'index/device/device_set');

//设备基础数据修改 参数：device_id，设备id;pose_active:	良好姿态练习，单位分钟;good_time:良好体态巩固，单位分钟;
//bad_time:良好体态巩固延时时间，单位s;eye_time:眼部保健-用眼时长，单位分钟;eye_active:眼部保健-保健时长，单位分钟;
//body_time:运动健康-久坐时长，单位分钟;body_active：运动健康-运动时长，单位分钟;possensit:良好体态练习灵敏度3档，范围1-5（档）;goodsensit:良好体态巩固灵敏度3档，范围1-5（档
Route::post('/device/device_update', 'index/device/device_update');
//近7/28天详细数据 参数：device_id，设备id;type:类别：1是近7天，2是近四周
Route::post('/device/device_data', 'index/device/device_data');

//检验版本更新 device_id:设备id
Route::post('/device/version', 'index/device/version');
//版本更新 device_id:设备id；version：版本号
Route::post('/device/go_version', 'index/device/go_version');


//<-------------------------------------------新的设备接口----------------------------------------------------------------------------->

//获取设备设置的参数 参数：device_id，设备id;
Route::post('/robot/set_info', 'index/robot/set_info');
//设备基础数据修改 参数：device_id，设备id;clock：闹钟1时间7点;bel：闹铃1铃声;delay_time:闹铃1延时时间;delay_num:闹铃1延时提醒次数;to_clock:一个番茄时钟的时间;to_bell:番茄时钟铃声;
//to_rest:番茄休息时间;to_recover:0min-30min能量恢复（累计4个番茄后休息时间）;hp_clock:驼背报警的角度;lp_clock:侧倾的角度;ol_clock:侧旋的角度;pose_delay:延时报警时间，5秒;
//pose_bell:体态报警铃声;	sleep:自动睡眠时间;biaozhun:是否知道标准坐姿，1是知道，2是不知道;fuan:伏案程度，1是经常，2是偶尔;qita:其他不良;study_time:每天学习总时长;age:年龄;train_time:每天练习时间;volume:设备音量调节
Route::post('/robot/save_set', 'index/robot/save_set');

//获取设备数据汇总 参数：device_id，设备id; day最近几天的数据
Route::post('/robot/real_data', 'index/robot/real_data');


//用户反馈 content:内容;device_id:设备id;photo:反馈的图片;
Route::post('/robot/fankui', 'index/robot/fankui');
