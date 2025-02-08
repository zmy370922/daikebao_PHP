<?php
/**
 * Created by 合肥芒丁数据系统有限责任公司
 * User: Li Yahui
 * Date: 2021/7/9
 * Time: 18:13
 */
return [
    'APPID' => "wx54b22d40c5b866fa",       //对应小程序APPID
    'APPSECRET' => "a635038ded4b3134caa2524e35d326da",
    'MCHID' => "1645347357",       //微信支付商户号
    'NOTIFY_URL' => "https://" . $_SERVER['HTTP_HOST'] . "/mobile/weixin/notpay",     //微信支付回调地址
    'KEY' => "xingfuyijiayihunlianjiaoyou66688"      //微信支付商户KEY
];