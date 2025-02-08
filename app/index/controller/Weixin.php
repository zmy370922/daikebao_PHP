<?php
/**
 * Created by 合肥芒丁数据系统有限责任公司
 * User: Li Yahui
 * Date: 2019/1/18
 * Time: 14:28
 */

namespace app\index\controller;

use app\common\model\RechargeOrder as RechargeOrderModel;
use app\common\model\User as UserModel;
use think\facade\Config;

use app\BaseController;

class Weixin extends BaseController
{
    /**
     * 微信支付
     */
    public function pay()
    {
        $token = input('openid', '');//用户登录openid
        $sn = input('sn', '');//订单号
        $type = input('type', 1);//1是流量充值订单
        if (!$token || !$sn) {
            return message("参数错误", false);
        }

        $user = UserModel::where(['token' => $token])->find();
        if (!$user) {
            return message("登录失效", false, [], 401);
        }
        if ($type == 1) {
            $where = [];
            $where[] = ['uid', '=', $user['id']];
            $where[] = ['sn', '=', $sn];
            $info = RechargeOrderModel::where($where)->find();
            $notify_url = 'https://' . $_SERVER['HTTP_HOST'] . '/weixin/recharge_notpay';//回调的url【自己填写】';
        } else {
            return message("订单类型错误", false);
        }
        if (!$info) {
            return message("订单不存在", false);
        }
        $config = Config::get('weixinpay');
        $appid = $config["APPID"];

        $body = '流量充值';// '商品详细l';//'【自己填写】'
        $mch_id = $config['MCHID'];//'你的商户号【自己填写】'
        $nonce_str = $this->nonce_str();//随机字符串
        $openid = $user['openid'];
        $out_trade_no = $info['sn'];//商户订单号
        $spbill_create_ip = $this->ClientIp();//'服务器的ip【自己填写】';
//        $total_fee =$total * 100;//因为充值金额最小是1 而且单位为分 如果是充值1元所以这里需要*100
        $total_fee = 1;//因为充值金额最小是1 而且单位为分 如果是充值1元所以这里需要*100
        $trade_type = 'JSAPI';//交易类型 默认
        //这里是按照顺序的 因为下面的签名是按照顺序 排序错误 肯定出错
        //(appId=wxd678efh567hg6787&nonceStr=5K8264ILTKCH16CQ2502SI8ZNMTM67VS&package=prepay_id=wx2017033010242291fcfe0db70013231072&signType=MD5&timeStamp=1490840662&key=qazwsxedcrfvtgbyhnujmikolp111111
        $post['appid'] = $appid;
        $post['body'] = $body;
        $post['mch_id'] = $mch_id;
        $post['nonce_str'] = $nonce_str;//随机字符串
        $post['notify_url'] = $notify_url;
        $post['openid'] = $openid;
        $post['out_trade_no'] = $out_trade_no;
        $post['spbill_create_ip'] = $spbill_create_ip;//终端的ip
        $post['total_fee'] = $total_fee;//总金额 最低为一块钱 必须是整数
        $post['trade_type'] = $trade_type;
        $sign = $this->sign($post);//签名
        $post_xml = '<xml>
           <appid>' . $appid . '</appid>
           <body>' . $body . '</body>
           <mch_id>' . $mch_id . '</mch_id>
           <nonce_str>' . $nonce_str . '</nonce_str>
           <notify_url>' . $notify_url . '</notify_url>
           <openid>' . $openid . '</openid>
           <out_trade_no>' . $out_trade_no . '</out_trade_no>
           <spbill_create_ip>' . $spbill_create_ip . '</spbill_create_ip>
           <total_fee>' . $total_fee . '</total_fee>
           <trade_type>' . $trade_type . '</trade_type>
           <sign>' . $sign . '</sign>
        </xml> ';
        //统一接口prepay_id
//        return $post_xml;
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $xml = $this->http_request($url, $post_xml);
        $array = $this->xml($xml);//全要大写
        if ($array['RETURN_CODE'] == 'SUCCESS' && $array['RESULT_CODE'] == 'SUCCESS') {
            $time = time();
            $tmp = array();//临时数组用于签名
            $tmp['appId'] = $appid;
            $tmp['nonceStr'] = $nonce_str;
            $tmp['package'] = 'prepay_id=' . $array['PREPAY_ID'];
            $tmp['signType'] = 'MD5';
            $tmp['timeStamp'] = "$time";
            $data['code'] = 200;
            $data['state'] = 1;
            $data['timeStamp'] = "$time";//时间戳
            $data['nonceStr'] = $nonce_str;//随机字符串
            $data['signType'] = 'MD5';//签名算法，暂支持 MD5
            $data['package'] = 'prepay_id=' . $array['PREPAY_ID'];//统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
            $data['paySign'] = $this->sign($tmp);//签名,具体签名方案参见微信公众号支付帮助文档;
            $data['out_trade_no'] = $out_trade_no;

        } else {
            $data['code'] = 500;
            $data['msg'] = $array;
            $data['state'] = 0;
            $data['text'] = "错误";
            $data['RETURN_CODE'] = $array['RETURN_CODE'];
            $data['RETURN_MSG'] = $array['RETURN_MSG'];
        }
        echo json_encode($data);

    }


    //随机32位字符串
    private function nonce_str()
    {
        $result = '';
        $str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
        for ($i = 0; $i < 32; $i++) {
            $result .= $str[rand(0, 48)];
        }
        return $result;
    }

    function ClientIp()
    {
        $cIP = getenv($_SERVER['REMOTE_ADDR']);
        return $cIP;
    }


    //签名 $data要先排好顺序
    public function sign($data)
    {
        $stringA = '';
        foreach ($data as $key => $value) {
            if (!$value) continue;
            if ($stringA) $stringA .= '&' . $key . "=" . $value;
            else $stringA = $key . "=" . $value;
        }
        $config = \think\facade\Config::get('weixinpay');
        $wx_key = $config['KEY'];//申请支付后有给予一个商户账号和密码，登陆后自己设置key
        $stringSignTemp = $stringA . '&key=' . $wx_key;//申请支付后有给予一个商户账号和密码，登陆后自己设置key
        return strtoupper(md5($stringSignTemp));
    }

    //curl请求啊
    function http_request($url, $data = null, $headers = array())
    {
        $curl = curl_init();
        if (count($headers) >= 1) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    //获取xml
    private function xml($xml)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);
        $data = array();
        foreach ($index as $key => $value) {
            if ($key == 'xml' || $key == 'XML') continue;
            $tag = $vals[$value[0]]['tag'];
            $value = $vals[$value[0]]['value'];
            $data[$tag] = $value;
        }
        return $data;
    }

    /**
     * 流量充值回调
     */
    public function recharge_notpay()
    {
        $postStr = file_get_contents('php://input');
        //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $data = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

        // 保存微信服务器返回的签名sign
        // sign不参与签名算法

        // 判断签名是否正确  判断支付状态
        if (($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS')) {
            $orderid = $data['out_trade_no']; //订单单号

            $info = RechargeOrderModel::where('sn', '=', $orderid)->find();
            if ($info['status'] == 2) {
                $result = true;
            } else {
                RechargeOrderModel::where(['id' => $info['id']])->update(['status' => 2, 'pay_time' => time()]);
                //增加账户流量
                $user = UserModel::where(['id' => $info['uid']])->find();
                $new_balance = $user['balance'] + $info['integral'];
                UserModel::where(['id' => $user['id']])->update(['balance' => $new_balance]);
                $result = true;
            }
        } else {
            $result = false;
        }
        // 返回状态给微信服务器
        if ($result) {
            $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        } else {
            $str = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
    }


    /**
     * 将xml转换为数组
     * @param string $xml :xml文件或字符串
     * @return array
     */
    function xmlToArray($xml)
    {
        //考虑到xml文档中可能会包含<![CDATA[]]>标签，第三个参数设置为LIBXML_NOCDATA
        if (file_exists($xml)) {
            libxml_disable_entity_loader(false);
            $xml_string = simplexml_load_file($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        } else {
            libxml_disable_entity_loader(true);
            $xml_string = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        $result = json_decode(json_encode($xml_string), true);
        return $result;
    }
}
