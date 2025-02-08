<?php

use app\common\model\ActionLog as AdminLogModel;
use app\common\model\ActionLog as ActionLogModel;
use think\facade\Cache;
use think\facade\Config;


// 应用公共文件
if (!function_exists('periodDate')) {
    /**
     * 期间日期
     * @param $startDate
     * @param $endDate
     * @return array
     */
    function periodDate($startTime, $endTime)
    {
        $arr = array();
        while ($startTime <= $endTime) {
            $arr[] = date('Y-m-d', $startTime);
            $startTime = strtotime('+1 day', $startTime);
        }
        return $arr;
    }
}
// 应用公共文件
if (!function_exists('periodWeek')) {
    /**
     * 期间日期
     * @param $startDate
     * @param $endDate
     * @return array
     */
    function periodWeek($startTime, $endTime)
    {
        $arr = array();
        while ($startTime <= $endTime) {
            $arr[] = date('Y-m-d', $startTime);
            $startTime = strtotime('+7 day', $startTime);
        }
        return $arr;
    }
}
if (!function_exists('message')) {
    /**
     * 消息数组函数
     * @param string $msg 提示语
     * @param bool $success 是否成功
     * @param array $data 结果数据
     * @param int $code 错误码
     * @return array 返回消息对象
     * @author 鱼鱼鱼
     * @date 2023/5/29
     */
    function message($msg = "操作成功", $success = true, $data = [], $code = 0)
    {
        $result = ['msg' => $msg, 'data' => $data, 'success' => $success];
        if ($success) {
            // 成功统一返回200
            $result['code'] = 200;
        } else {
            // 失败状态(可配置常用状态码)
            $result['code'] = $code ? $code : 500;
        }
        return json($result);
    }
}


if (!function_exists('get_rand_char')) {
    /**
     * 生成随机字符串
     * @param int $length 生成长度
     * @return string 返回结果
     * @author 鱼鱼鱼
     * @date 2023/5/29
     */
    function get_rand_char($length)
    {
        $str = null;
        $strPol = 'ASDFGHJKLMNBVCXZQWERTYUIOP0123456789asdfghjklmnbvcxzqwertyuiop';
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];
        }

        return $str;
    }
}


if (!function_exists('Zlog')) {
    /**
     * 调试日志
     * @param $name
     * @param $info
     */
    function Zlog($name, $info)
    {
        $dirname = app()->getRootPath() . '/runtime/zlog/';
        if (!file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }

        $logfile = $dirname . $name . '.txt';

        if (is_object($info) || is_array($info)) {
            $info_text = var_export($info, true);
        } elseif (is_bool($info)) {
            $info_text = $info ? 'true' : 'false';
        } else {
            $info_text = $info;
        }
        $info_text = '[' . date('Y-m-d H:i:s', time()) . '] ' . $info_text;
        if (!empty($logfile)) {
            error_log($info_text . "\r\n", 3, $logfile);
        } else {
            error_log($info_text);
        }
    }
}

if (!function_exists('get_last_days')) {
    /**
     * 获取最近30天所有日期
     * @param string $time 时间戳
     * @param string $format 格式化字符串的格式
     * @return array
     */
    function get_last_days($time = '', $format = 'Y-m-d', $days = 30)
    {
        $time = $time != '' ? $time : time();
        //组合数据
        $date = [];
        for ($i = 0; $i <= $days; $i++) {
            $date[$i] = date($format, strtotime('+' . $i - $days . ' days', $time));
        }
        return $date;
    }
}

if (!function_exists('getExcelArrayData')) {
    /**
     * 根据表格文件路径获取表格数据
     * @param string $file_path 文件路径
     * @return array
     */
    function getExcelArrayData($file_path)
    {
        require_once('../extend/phpexcel/PHPExcel.php');
        require_once('../extend/phpexcel/PHPExcel/Writer/Excel2007.php');   // 或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls的

        $extension = pathinfo('.' . $file_path, PATHINFO_EXTENSION);
        if ($extension == 'xls') {
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        } else {
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        }
        $obj_PHPExcel = $objReader->load('.' . $file_path, $encode = 'utf-8');  // 加载文件内容,编码utf-8
        $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   // 转换为数组格式
        array_shift($excel_array);  // 去除标题;
        return $excel_array;
    }
}


if (!function_exists('curl_request')) {
    /**
     * http请求
     * @param $url
     * @param string $method
     * @param null $params
     * @param array $headers
     * @param int $time_out
     * @return bool|mixed
     */
    function curl_request($url, $method = 'GET', $params = null, $headers = [], $type = 'json', $time_out = 0)
    {
        if (is_array($params)) {
            if ($method == 'GET') {
                $requestString = http_build_query($params);
            } else {
                if ($type == "json") {
                    $requestString = json_encode($params, JSON_UNESCAPED_UNICODE);
                } else if ($type == "urlencode") {
                    $requestString = http_build_query($params);
                } else {
                    $requestString = $params;
                }

            }
        } else {
            $requestString = $params ?: '';
        }
        if ($method == 'GET') {
            $url = $url . "?" . $requestString;
        }

        // setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time_out);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // setting the POST FIELD to curl
        switch ($method) {
            case "GET" :
                curl_setopt($ch, CURLOPT_HTTPGET, 1);
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);
                break;
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        if ($errno && $httpCode != '200') {
            return false;
        }
        //close the connection
        curl_close($ch);
        $res = json_decode($response, true);
        //debug log each request
        return $res;
    }
}

if (!function_exists('get_month_days')) {
    /**
     * 获取当前月的所有日期
     * @return array
     */
    function get_month_days($month)
    {
        $monthDays = [];
        $firstDay = date('Y-m-01', strtotime($month));
        $i = 0;
        $lastDay = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
        while (date('Y-m-d', strtotime("$firstDay +$i days")) <= $lastDay) {
            $monthDays[] = date('Y-m-d', strtotime("$firstDay +$i days"));
            $i++;
        }
        return $monthDays;
    }
}


if (!function_exists('get_login_token')) {
    /**
     * 登录生成token
     * @param array $user_info
     * @return string $token
     * @author 鱼鱼鱼
     * @date 2023/5/29
     */
    function get_login_token($info)
    {
        $login_time = time();//登录时间
        $str = get_rand_char(6);
        $token = md5($info['id'] . '_' . $str . '_' . $login_time); //生成token

        return $token;
    }
}

if (!function_exists('adminlog')) {
    /**
     * 记录操作日志
     * @param $uid 操作人ID
     * @param $content 操作内容
     * @param $model 触发行为的表
     * @param $record_id 触发行为的数据id
     * @param $status 操作状态 1为成功，2为失败
     * @param $flag 类型,1为系统后台，2为用户
     */
    function adminlog($uid, $content = '', $model = '', $record_id = 0, $status = 1, $flag = 1)
    {
        $data = [];
        $data['uid'] = $uid;
        $data['content'] = $content;
        $data['ip'] = \think\facade\Request::ip();
        $data['model'] = $model;
        $data['record_id'] = $record_id;
        $data['status'] = $status;
        $data['flag'] = $flag;
        $data['add_time'] = time();
        AdminLogModel::create($data);
    }
}

if (!function_exists('is_time_cross')) {
    /**
     * PHP计算两个时间段是否有交集（边界重叠不算）
     *
     * @param string $beginTime1 开始时间1
     * @param string $endTime1 结束时间1
     * @param string $beginTime2 开始时间2
     * @param string $endTime2 结束时间2
     * @return bool
     */
    function is_time_cross($beginTime1 = '', $endTime1 = '', $beginTime2 = '', $endTime2 = '')
    {
        $status = $beginTime2 - $beginTime1;
        if ($status > 0) {
            $status2 = $beginTime2 - $endTime1;
            if ($status2 >= 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $status2 = $endTime2 - $beginTime1;
            if ($status2 > 0) {
                return false;
            } else {
                return true;
            }
        }
    }
}

if (!function_exists('get_os')) {
    /**
     * 获取操作系统
     * @return string
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function get_os()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $os = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/win/i', $os)) {
                $os = 'Windows';
            } else if (preg_match('/mac/i', $os)) {
                $os = 'MAC';
            } else if (preg_match('/linux/i', $os)) {
                $os = 'Linux';
            } else if (preg_match('/unix/i', $os)) {
                $os = 'Unix';
            } else if (preg_match('/bsd/i', $os)) {
                $os = 'BSD';
            } else {
                $os = 'Other';
            }
            return $os;
        } else {
            return 'unknow';
        }
    }
}

if (!function_exists('get_browse')) {
    /**
     * 获取浏览器信息
     * @return string
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function get_browse()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $br = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i', $br)) {
                $br = 'MSIE';
            } else if (preg_match('/Firefox/i', $br)) {
                $br = 'Firefox';
            } else if (preg_match('/Chrome/i', $br)) {
                $br = 'Chrome';
            } else if (preg_match('/Safari/i', $br)) {
                $br = 'Safari';
            } else if (preg_match('/Opera/i', $br)) {
                $br = 'Opera';
            } else {
                $br = 'Other';
            }
            return $br;
        } else {
            return 'unknow';
        }
    }
}


if (!function_exists('is_empty')) {
    /**
     * 判断是否为空
     * @param $value 参数值
     * @return bool 返回结果true或false
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function is_empty($value)
    {
        // 判断是否存在该值
        if (!isset($value)) {
            return true;
        }

        // 判断是否为empty
        if (empty($value)) {
            return true;
        }

        // 判断是否为null
        if ($value === null) {
            return true;
        }

        // 判断是否为空字符串
        if (trim($value) === '') {
            return true;
        }

        // 默认返回false
        return false;
    }
}

if (!function_exists('is_image')) {
    /**
     * 判断是否为图片格式
     * @param $filename
     * @return bool|false|int
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function is_image($filename)
    {
        $types = '.gif|.GIF|.jpg|.JPG|.jpeg|.JPEG|.png|.PNG|.bmp|.BMP';
        //定义检查的图片类型
        if (file_exists($filename)) {
            $info = getimagesize($filename);
            $ext = image_type_to_extension($info['2']);
            return stripos($types, $ext);
        } else {
            return false;
        }
    }
}

if (!function_exists('mkdirs')) {
    /**
     * 递归创建目录
     * @param string $dir 需要创建的目录路径
     * @param int $mode 权限值
     * @return bool 返回结果true或false
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || mkdir($dir, $mode, true)) {
            return true;
        }
        if (!mkdirs(dirname($dir), $mode)) {
            return false;
        }
        return mkdir($dir, $mode, true);
    }
}

if (!function_exists('rmdirs')) {
    /**
     * 删除文件夹
     * @param string $dir 文件夹路径
     * @param bool $rmself 是否删除本身true或false
     * @return bool 返回删除结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function rmdirs($dir, $rmself = true)
    {
        if (!is_dir($dir)) {
            return false;
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $todo = ($file->isDir() ? 'rmdir' : 'unlink');
            $todo($file->getRealPath());
        }
        if ($rmself) {
            @rmdir($dir);
        }

        return true;
    }
}

if (!function_exists('copydirs')) {
    /**
     * 复制文件夹
     * @param string $source 原文件夹路径
     * @param string $dest 目的文件夹路径
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $sent_dir = $dest . "/" . $iterator->getSubPathName();
                if (!is_dir($sent_dir)) {
                    mkdir($sent_dir, 0755, true);
                }
            } else {
                copy($item, $dest . "/" . $iterator->getSubPathName());
            }
        }
    }
}

if (!function_exists('mbsubstr')) {
    /**
     * 字符串截取，支持中文和其他编码
     * @param string $str 需要转换的字符串
     * @param int $start 开始位置
     * @param int $length 截取长度
     * @param string $encoding 编码格式
     * @param string $suffix 截断显示字符
     * @return false|mixed|string 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function mbsubstr($str, $start = 0, $length = null, $encoding = "utf-8", $suffix = '...')
    {
        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $encoding);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $encoding);
            if (false === $slice) {
                $slice = '';
            }
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$encoding], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice . $suffix : $slice;
    }
}


if (!function_exists('sub_str')) {
    /**
     * 字符串截取
     * @param string $str 需要截取的字符串
     * @param int $start 开始位置
     * @param int $length 截取长度
     * @param bool $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function sub_str($str, $start = 0, $length = 10, $suffix = true, $charset = "utf-8")
    {
        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        $omit = mb_strlen($str) >= $length ? '...' : '';
        return $suffix ? $slice . $omit : $slice;
    }
}

if (!function_exists('array_sort')) {
    /**
     * 二位数组排序
     * @param array $arr 数据源
     * @param string $keys KEY
     * @param bool $desc 排序方式（默认：asc）
     * @return array 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function array_sort($arr, $keys, $desc = false)
    {
        $key_value = $new_array = array();
        foreach ($arr as $k => $v) {
            $key_value[$k] = $v[$keys];
        }
        if ($desc) {
            arsort($key_value);
        } else {
            asort($key_value);
        }
        reset($key_value);
        foreach ($key_value as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }
}


if (!function_exists('array_merge_multiple')) {
    /**
     * 多维数组合并
     * @param array $array1 数组1
     * @param array $array2 数组2
     * @return array 返回合并数组
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function array_merge_multiple($array1, $array2)
    {
        $merge = $array1 + $array2;
        $data = [];
        foreach ($merge as $key => $val) {
            if (isset($array1[$key])
                && is_array($array1[$key])
                && isset($array2[$key])
                && is_array($array2[$key])
            ) {
                $data[$key] = array_merge_multiple($array1[$key], $array2[$key]);
            } else {
                $data[$key] = isset($array2[$key]) ? $array2[$key] : $array1[$key];
            }
        }
        return $data;
    }
}

if (!function_exists('array_key_value')) {
    /**
     * 获取数组中某个字段的所有值
     * @param array $arr 数据源
     * @param string $name 字段名
     * @return array 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function array_key_value($arr, $name = "")
    {
        $result = [];
        if ($arr) {
            foreach ($arr as $key => $val) {
                if ($name) {
                    $result[] = $val[$name];
                } else {
                    $result[] = $key;
                }
            }
        }
        $result = array_unique($result);
        return $result;
    }
}

if (!function_exists('curl_url')) {
    /**
     * 获取当前访问的完整URL
     * @return string 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function curl_url()
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on') {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

}

if (!function_exists('curl_get')) {
    /**
     * curl请求(GET)
     * @param string $url 请求地址
     * @param array $data 请求参数
     * @return bool|string 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function curl_get($url, $data = [])
    {
        // 处理get数据
        if (!empty($data)) {
            $url = $url . '?' . http_build_query($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}

if (!function_exists('curl_post')) {
    /**
     * curl请求(POST)
     * @param string $url 请求地址
     * @param array $data 请求参数
     * @return bool|string 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function curl_post($url, $data = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('curl_request')) {
    /**
     * curl请求(支持get和post)
     * @param $url 请求地址
     * @param array $data 请求参数
     * @param string $type 请求类型(默认：post)
     * @param bool $https 是否https请求true或false
     * @return bool|string 返回请求结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function curl_request($url, $data = [], $type = 'post', $https = false)
    {
        // 初始化
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        // 设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // 是否要求返回数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
            // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 从证书中检查SSL加密算法是否存在
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (strtolower($type) == 'post') {
            // 设置post方式提交
            curl_setopt($ch, CURLOPT_POST, true);
            // 提交的数据
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif (!empty($data) && is_array($data)) {
            // get网络请求
            $url = $url . '?' . http_build_query($data);
        }
        // 设置抓取的url
        curl_setopt($ch, CURLOPT_URL, $url);
        // 执行命令
        $result = curl_exec($ch);
        if ($result === false) {
            return false;
        }
        // 关闭URL请求(释放句柄)
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('datetime')) {
    /**
     * 时间戳转日期格式
     * @param int $time 时间戳
     * @param string $format 转换格式(默认：Y-m-d h:i:s)
     * @return false|string 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        if (empty($time)) {
            return '--';
        }
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }
}


if (!function_exists('decrypt')) {
    /**
     * DES解密
     * @param string $data 解密字符串
     * @param string $key 解密KEY
     * @return mixed
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function decrypt($data, $key = 'p@ssw0rd')
    {
        return openssl_decrypt($data, 'des-ecb', $key);
    }
}

if (!function_exists('encrypt')) {
    /**
     *
     * @param string $data 加密字符串
     * @param string $key 加密KEY
     * @return string
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function encrypt($data, $key = 'p@ssw0rd')
    {
        return openssl_encrypt($data, 'des-ecb', $key);
    }
}

if (!function_exists('export_excel')) {
    /**
     * 数据导出Excel(csv文件)
     * @param string $file_name 文件名称
     * @param array $tile 标题
     * @param array $data 数据源
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function export_excel($file_name, $tile = [], $data = [])
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 0);
        ob_end_clean();
        ob_start();
        header("Content-Type: text/csv");
        header("Content-Disposition:filename=" . $file_name);
        $fp = fopen('php://output', 'w');
        // 转码 防止乱码(比如微信昵称)
        fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($fp, $tile);
        $index = 0;
        foreach ($data as $item) {
            if ($index == 1000) {
                $index = 0;
                ob_flush();
                flush();
            }
            $index++;
            fputcsv($fp, $item);
        }
        ob_flush();
        flush();
        ob_end_clean();
    }
}


if (!function_exists('get_random_str')) {
    /**
     * 生成随机字符串
     * @param int $length 生成长度
     * @param int $type 生成类型：0-小写字母+数字，1-小写字母，2-大写字母，3-数字，4-小写+大写字母，5-小写+大写+数字
     * @return string 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function get_random_str($length = 8, $type = 0)
    {
        $a = 'abcdefghijklmnopqrstuvwxyz';
        $A = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $n = '0123456789';

        switch ($type) {
            case 1:
                $chars = $a;
                break;
            case 2:
                $chars = $A;
                break;
            case 3:
                $chars = $n;
                break;
            case 4:
                $chars = $a . $A;
                break;
            case 5:
                $chars = $a . $A . $n;
                break;
            default:
                $chars = $a . $n;
        }

        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }
}

if (!function_exists('get_random_code')) {
    /**
     * 获取指定位数的随机码
     * @param int $num 随机码长度
     * @return string 返回字符串
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function get_random_code($num = 12)
    {
        $codeSeeds = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeSeeds .= "abcdefghijklmnopqrstuvwxyz";
        $codeSeeds .= "0123456789_";
        $len = strlen($codeSeeds);
        $code = "";
        for ($i = 0; $i < $num; $i++) {
            $rand = rand(0, $len - 1);
            $code .= $codeSeeds[$rand];
        }
        return $code;
    }
}

if (!function_exists('get_server_ip')) {
    /**
     * 获取服务端IP地址
     * @return string 返回IP地址
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function get_server_ip()
    {
        if (isset($_SERVER)) {
            if ($_SERVER['SERVER_ADDR']) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } else {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip;
    }
}

if (!function_exists('get_client_ip')) {
    /**
     * 获取客户端IP地址
     * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv 否进行高级模式获取（有可能被伪装）
     * @return mixed 返回IP
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function get_client_ip($type = 0, $adv = false)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if ($ip !== null) {
            return $ip[$type];
        }
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}


if (!function_exists('get_format_time')) {
    /**
     * 获取格式化显示时间
     * @param int $time 时间戳
     * @return false|string 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function get_format_time($time)
    {
        $time = (int)substr($time, 0, 10);
        $int = time() - $time;
        $str = '';
        if ($int <= 2) {
            $str = sprintf('刚刚', $int);
        } elseif ($int < 60) {
            $str = sprintf('%d秒前', $int);
        } elseif ($int < 3600) {
            $str = sprintf('%d分钟前', floor($int / 60));
        } elseif ($int < 86400) {
            $str = sprintf('%d小时前', floor($int / 3600));
        } elseif ($int < 1728000) {
            $str = sprintf('%d天前', floor($int / 86400));
        } else {
            $str = date('Y年m月d日', $time);
        }
        return $str;
    }
}

if (!function_exists('get_device_type')) {
    /**
     * 获取设备类型(苹果或安卓)
     * @return int 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function get_device_type()
    {
        // 全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $type = 0;
        // 分别进行判断
        if (strpos($agent, 'iphone') !== false || strpos($agent, 'ipad') !== false) {
            $type = 1;
        }
        if (strpos($agent, 'android') !== false) {
            $type = 2;
        }
        return $type;
    }
}

if (!function_exists('get_password')) {
    /**
     * 获取双MD5加密密码
     * @param string $password 加密字符串
     * @return string 返回结果
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function get_password($password)
    {
        return md5(md5($password));
    }
}

if (!function_exists('action_log')) {
    /**
     * 记录系统行为日志
     * @param int $mid 操作人UID
     * @param string $model 操作表名
     * @param string $title 标题
     * @param string $content 内容
     * @param int $record_id 触发行为的数据id
     * @author 鱼鱼鱼
     * @date 2023年8月15日
     */
    function action_log($mid, $model, $title = '登录系统', $content = '', $record_id = 0)
    {
        // 日志数据
        $data = [
            'mid' => $mid,
            'method' => request()->method(),
            'module' => app('http')->getName(),
            'model' => $model,
            'url' => request()->url(true), // 获取完成URL
            'param' => request()->param() ? json_encode(request()->param()) : '',
            'title' => $title,
            'type' => $title == '登录系统' ? 1 : ($title == '注销系统' ? 2 : 3),
            'content' => $content,
            'record_id' => $record_id,
            'ip' => request()->ip(),
            'os' => get_os(),
            'browser' => get_browse(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
            'add_time' => time(),
        ];
        // 日志入库
        ActionLogModel::create($data);
    }
}

if (!function_exists('attr_format')) {
    /**
     * 格式化属性
     * @param $arr
     * @return array
     */
    function attr_format($arr)
    {
        $len = count($arr);
        $title = array_column($arr, 'value');
        $result = [];

        if ($len > 0) {
            if ($len > 1) {
                $result = $arr[0]['detail'];
                for ($i = 0; $i < $len - 1; $i++) {
                    $temp = $result;
                    $result = [];
                    foreach ($temp as $item) {
                        foreach ($arr[$i + 1]['detail'] as $datum) {
                            $result[] = trim($item) . ',' . trim($datum);
                        }
                    }
                }
            } else {
                foreach ($arr[0]['detail'] as $item) {
                    $result[] = trim($item);
                }
            }
        }
        return [$result, $title];
    }
}

if (!function_exists('time_tran')) {
    /**
     * 时间戳人性化转化
     * @param $time
     * @return string
     */
    function time_tran($time)
    {
        $t = time() - $time;
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . '前';
            }
        }
    }
}

if (!function_exists('get_last_days_date')) {
    /**
     * 获取最近30天所有日期
     * @param string $time 时间戳
     * @param string $format 格式化字符串的格式
     * @return array
     */
    function get_last_days_date($time = '', $format = 'Y-m-d')
    {
        $time = $time != '' ? $time : time();
        //组合数据
        $date = [];
        for ($i = 0; $i <= 29; $i++) {
            $date[$i] = date($format, strtotime('+' . $i - 29 . ' days', $time));
        }
        return $date;
    }
}


if (!function_exists('findMaxIndex')) {

    function findMaxIndex($arr) {
        $maxIndex = 0;
        $maxValue = $arr[0];

        for ($i = 1; $i < count($arr); $i++) {
            if ($arr[$i] > $maxValue) {
                $maxValue = $arr[$i];
                $maxIndex = $i;
            }
        }

        return $maxIndex;
    }
}
if (!function_exists('phpCurl')) {

    function phpCurl($url,$data) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}





if(!function_exists('get_nickname')){
    function get_nickname()
    {
        /**
         * 随机昵称 形容词
         */
        $nicheng_tou = ['迷你的', '鲜艳的', '飞快的', '真实的', '清新的', '幸福的', '可耐的', '快乐的', '冷静的', '醉熏的', '潇洒的', '糊涂的', '积极的', '冷酷的', '深情的', '粗暴的',
            '温柔的', '可爱的', '愉快的', '义气的', '认真的', '威武的', '帅气的', '传统的', '潇洒的', '漂亮的', '自然的', '专一的', '听话的', '昏睡的', '狂野的', '等待的', '搞怪的',
            '幽默的', '魁梧的', '活泼的', '开心的', '高兴的', '超帅的', '留胡子的', '坦率的', '直率的', '轻松的', '痴情的', '完美的', '精明的', '无聊的', '有魅力的', '丰富的', '繁荣的',
            '饱满的', '炙热的', '暴躁的', '碧蓝的', '俊逸的', '英勇的', '健忘的', '故意的', '无心的', '土豪的', '朴实的', '兴奋的', '幸福的', '淡定的', '不安的', '阔达的', '孤独的',
            '独特的', '疯狂的', '时尚的', '落后的', '风趣的', '忧伤的', '大胆的', '爱笑的', '矮小的', '健康的', '合适的', '玩命的', '沉默的', '斯文的', '香蕉', '苹果', '鲤鱼', '鳗鱼',
            '任性的', '细心的', '粗心的', '大意的', '甜甜的', '酷酷的', '健壮的', '英俊的', '霸气的', '阳光的', '默默的', '大力的', '孝顺的', '忧虑的', '着急的', '紧张的', '善良的',
            '凶狠的', '害怕的', '重要的', '危机的', '欢喜的', '欣慰的', '满意的', '跳跃的', '诚心的', '称心的', '如意的', '怡然的', '娇气的', '无奈的', '无语的', '激动的', '愤怒的',
            '美好的', '感动的', '激情的', '激昂的', '震动的', '虚拟的', '超级的', '寒冷的', '精明的', '明理的', '犹豫的', '忧郁的', '寂寞的', '奋斗的', '勤奋的', '现代的', '过时的',
            '稳重的', '热情的', '含蓄的', '开放的', '无辜的', '多情的', '纯真的', '拉长的', '热心的', '从容的', '体贴的', '风中的', '曾经的', '追寻的', '儒雅的', '优雅的', '开朗的',
            '外向的', '内向的', '清爽的', '文艺的', '长情的', '平常的', '单身的', '伶俐的', '高大的', '懦弱的', '柔弱的', '爱笑的', '乐观的', '耍酷的', '酷炫的', '神勇的', '年轻的',
            '唠叨的', '瘦瘦的', '无情的', '包容的', '顺心的', '畅快的', '舒适的', '靓丽的', '负责的', '背后的', '简单的', '谦让的', '彩色的', '缥缈的', '欢呼的', '生动的', '复杂的',
            '慈祥的', '仁爱的', '魔幻的', '虚幻的', '淡然的', '受伤的', '雪白的', '高高的', '糟糕的', '顺利的', '闪闪的', '羞涩的', '缓慢的', '迅速的', '优秀的', '聪明的', '含糊的',
            '俏皮的', '淡淡的', '坚强的', '平淡的', '欣喜的', '能干的', '灵巧的', '友好的', '机智的', '机灵的', '正直的', '谨慎的', '俭朴的', '殷勤的', '虚心的', '辛勤的', '自觉的',
            '无私的', '无限的', '踏实的', '老实的', '现实的', '可靠的', '务实的', '拼搏的', '个性的', '粗犷的', '活力的', '成就的', '勤劳的', '单纯的', '落寞的', '朴素的', '悲凉的',
            '忧心的', '洁净的', '清秀的', '自由的', '小巧的', '单薄的', '贪玩的', '刻苦的', '干净的', '壮观的', '和谐的', '文静的', '调皮的', '害羞的', '安详的', '自信的', '端庄的',
            '坚定的', '美满的', '舒心的', '温暖的', '专注的', '勤恳的', '美丽的', '腼腆的', '优美的', '甜美的', '甜蜜的', '整齐的', '动人的', '典雅的', '尊敬的', '舒服的', '妩媚的',
            '秀丽的', '喜悦的', '甜美的', '彪壮的', '强健的', '大方的', '俊秀的', '聪慧的', '迷人的', '陶醉的', '悦耳的', '动听的', '明亮的', '结实的', '魁梧的', '标致的', '清脆的',
            '敏感的', '光亮的', '大气的', '老迟到的', '知性的', '冷傲的', '呆萌的', '野性的', '隐形的', '笑点低的', '微笑的', '笨笨的', '难过的', '沉静的', '火星上的', '失眠的',
            '安静的', '纯情的', '要减肥的', '迷路的', '烂漫的', '哭泣的', '贤惠的', '苗条的', '温婉的', '发嗲的', '会撒娇的', '贪玩的', '执着的', '眯眯眼的', '花痴的', '想人陪的',
            '眼睛大的', '高贵的', '傲娇的', '心灵美的', '爱撒娇的', '细腻的', '天真的', '怕黑的', '感性的', '飘逸的', '怕孤独的', '忐忑的', '高挑的', '傻傻的', '冷艳的', '爱听歌的',
            '还单身的', '怕孤单的', '懵懂的'];
        $nicheng_wei = ['嚓茶', '皮皮虾', '皮卡丘', '马里奥', '小霸王', '凉面', '便当', '毛豆', '花生', '可乐', '灯泡', '哈密瓜', '野狼', '背包', '眼神', '缘分', '雪碧', '人生', '牛排',
            '蚂蚁', '飞鸟', '灰狼', '斑马', '汉堡', '悟空', '巨人', '绿茶', '自行车', '保温杯', '大碗', '墨镜', '魔镜', '煎饼', '月饼', '月亮', '星星', '芝麻', '啤酒', '玫瑰',
            '大叔', '小伙', '哈密瓜，数据线', '太阳', '树叶', '芹菜', '黄蜂', '蜜粉', '蜜蜂', '信封', '西装', '外套', '裙子', '大象', '猫咪', '母鸡', '路灯', '蓝天', '白云',
            '星月', '彩虹', '微笑', '摩托', '板栗', '高山', '大地', '大树', '电灯胆', '砖头', '楼房', '水池', '鸡翅', '蜻蜓', '红牛', '咖啡', '机器猫', '枕头', '大船', '诺言',
            '钢笔', '刺猬', '天空', '飞机', '大炮', '冬天', '洋葱', '春天', '夏天', '秋天', '冬日', '航空', '毛衣', '豌豆', '黑米', '玉米', '眼睛', '老鼠', '白羊', '帅哥', '美女',
            '季节', '鲜花', '服饰', '裙子', '白开水', '秀发', '大山', '火车', '汽车', '歌曲', '舞蹈', '老师', '导师', '方盒', '大米', '麦片', '水杯', '水壶', '手套', '鞋子', '自行车',
            '鼠标', '手机', '电脑', '书本', '奇迹', '身影', '香烟', '夕阳', '台灯', '宝贝', '未来', '皮带', '钥匙', '心锁', '故事', '花瓣', '滑板', '画笔', '画板', '学姐', '店员',
            '电源', '饼干', '宝马', '过客', '大白', '时光', '石头', '钻石', '河马', '犀牛', '西牛', '绿草', '抽屉', '柜子', '往事', '寒风', '路人', '橘子', '耳机', '鸵鸟', '朋友',
            '苗条', '铅笔', '钢笔', '硬币', '热狗', '大侠', '御姐', '萝莉', '毛巾', '期待', '盼望', '白昼', '黑夜', '大门', '黑裤', '钢铁侠', '哑铃', '板凳', '枫叶', '荷花', '乌龟',
            '仙人掌', '衬衫', '大神', '草丛', '早晨', '心情', '茉莉', '流沙', '蜗牛', '战斗机', '冥王星', '猎豹', '棒球', '篮球', '乐曲', '电话', '网络', '世界', '中心', '鱼', '鸡', '狗',
            '老虎', '鸭子', '雨', '羽毛', '翅膀', '外套', '火', '丝袜', '书包', '钢笔', '冷风', '八宝粥', '烤鸡', '大雁', '音响', '招牌', '胡萝卜', '冰棍', '帽子', '菠萝', '蛋挞', '香水',
            '泥猴桃', '吐司', '溪流', '黄豆', '樱桃', '小鸽子', '小蝴蝶', '爆米花', '花卷', '小鸭子', '小海豚', '日记本', '小熊猫', '小懒猪', '小懒虫', '荔枝', '镜子', '曲奇', '金针菇',
            '小松鼠', '小虾米', '酒窝', '紫菜', '金鱼', '柚子', '果汁', '百褶裙', '项链', '帆布鞋', '火龙果', '奇异果', '煎蛋', '唇彩', '小土豆', '高跟鞋', '戒指', '雪糕', '睫毛', '铃铛',
            '手链', '香氛', '红酒', '月光', '酸奶', '银耳汤', '咖啡豆', '小蜜蜂', '小蚂蚁', '蜡烛', '棉花糖', '向日葵', '水蜜桃', '小蝴蝶', '小刺猬', '小丸子', '指甲油', '康乃馨', '糖豆',
            '薯片', '口红', '超短裙', '乌冬面', '冰淇淋', '棒棒糖', '长颈鹿', '豆芽', '发箍', '发卡', '发夹', '发带', '铃铛', '小馒头', '小笼包', '小甜瓜', '冬瓜', '香菇', '小兔子',
            '含羞草', '短靴', '睫毛膏', '小蘑菇', '跳跳糖', '小白菜', '草莓', '柠檬', '月饼', '百合', '纸鹤', '小天鹅', '云朵', '芒果', '面包', '海燕', '小猫咪', '龙猫', '唇膏', '鞋垫',
            '羊', '黑猫', '白猫', '万宝路', '金毛', '山水', '音响', '纸飞机', '烧鹅'];

        $tou_num = rand(0, count($nicheng_tou) - 1);
        $wei_num = rand(0, count($nicheng_wei) - 1);
        $nicheng = $nicheng_tou[$tou_num] . $nicheng_wei[$wei_num];
        return $nicheng;
    }

}

if(!function_exists('secondsToTime')){
    function secondsToTime($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf("%02d时%02d分%02d秒", $hours, $minutes, $seconds);
    }

}
if(!function_exists('toHours')){
    function toHours($minutes) {
        $hours = floor($minutes / 60); // 向下取整得到小时部分
        $remainingMinutes = $minutes % 60; // 求余得到剩余的分钟部分
        if($hours>0){
            return $hours.'小时'.$remainingMinutes.'分钟';
        }else{
            return $remainingMinutes.'分钟';
        }



    }

}

if (!function_exists('send_message')) {
    function send_message($array)
    {

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

        //定义url
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $accessToken;


        $array = json_encode($array);
        $result = api_increment($url, $array);
        $result = json_decode($result, true);
        zlog('MOBAN',$result);
        return $result;
    }
}

if (!function_exists('api_increment')) {
    function api_increment($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return $ch;
        } else {
            curl_close($ch);
            return $tmpInfo;
        }
    }
}
if(!function_exists('timesecond')){
    function timesecond($seconds)//将秒时间转换具体时间
    {
        $seconds = (int)$seconds;
        $days_num = "";
        if ($seconds > 3600) {
            if ($seconds > 24 * 3600) {
                $days = (int)($seconds / 86400);
                $days_num = $days . "天";
                $seconds = $seconds % 86400;//取余
            }
            $hours = intval($seconds / 3600);
            $minutes = $seconds % 3600;//取余下秒数
            $time = $days_num . $hours . "小时" . gmstrftime('%M分钟', $minutes);
        } else {
            $time = gmstrftime('%H小时%M分钟', $seconds);
        }
        return $time;
    }
}





