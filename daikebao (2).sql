-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-10-23 11:35:41
-- 服务器版本： 5.7.43-log
-- PHP 版本： 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `daikebao`
--

-- --------------------------------------------------------

--
-- 表的结构 `accept_log`
--

CREATE TABLE `accept_log` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `content` varchar(1000) DEFAULT NULL,
  `md_device_id` int(11) DEFAULT NULL COMMENT '设备ID',
  `generation_time` datetime DEFAULT NULL COMMENT '生成时间',
  `code` varchar(1000) DEFAULT NULL COMMENT '获得16进制得代码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `md_action_log`
--

CREATE TABLE `md_action_log` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '唯一性标识',
  `mid` int(11) NOT NULL DEFAULT '0' COMMENT '操作人UID',
  `method` varchar(36) NOT NULL DEFAULT '' COMMENT '请求类型',
  `module` varchar(36) NOT NULL DEFAULT '' COMMENT '操作模块',
  `model` varchar(64) NOT NULL DEFAULT '' COMMENT '操作表名',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '请求地址',
  `param` text NOT NULL COMMENT '请求参数(JSON格式)',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '日志标题',
  `type` tinyint(4) UNSIGNED DEFAULT '0' COMMENT '操作类型：1登录系统 2注销系统 3操作日志',
  `content` varchar(1024) NOT NULL DEFAULT '' COMMENT '内容',
  `record_id` int(11) NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `ip` varchar(128) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `os` varchar(36) NOT NULL DEFAULT '' COMMENT '操作系统',
  `browser` varchar(255) NOT NULL DEFAULT '' COMMENT '浏览器',
  `user_agent` varchar(512) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '删除状态，1是未删除，2是已删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统行为日志表';

--
-- 转存表中的数据 `md_action_log`
--

INSERT INTO `md_action_log` (`id`, `mid`, `method`, `module`, `model`, `url`, `param`, `title`, `type`, `content`, `record_id`, `ip`, `os`, `browser`, `user_agent`, `add_time`, `is_del`) VALUES
(1, 1, 'POST', 'admin', 'manage', 'https://server.deekbot.com/login/login', '{\"account\":\"admin\",\"password\":\"123456\"}', '登录系统', 1, '', 0, '117.67.217.113', 'Windows', 'Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 1729654193, 1);

-- --------------------------------------------------------

--
-- 表的结构 `md_device`
--

CREATE TABLE `md_device` (
  `id` int(11) NOT NULL,
  `opt` varchar(32) NOT NULL DEFAULT '' COMMENT '设备类型码',
  `client` varchar(64) NOT NULL DEFAULT '' COMMENT '设备唯一ID',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 1：未占用 2：已注册 3：已占用',
  `version` int(11) NOT NULL DEFAULT '0' COMMENT '协议版本号',
  `v` int(11) NOT NULL DEFAULT '0' COMMENT '设备版本号',
  `online` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '在线时长，可通过上报数据统计',
  `red` int(11) NOT NULL DEFAULT '0' COMMENT '红心累计，上报数据后需汇总更新',
  `red_buy` int(11) NOT NULL DEFAULT '0' COMMENT '红心换购，订单汇总更新',
  `register_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `connect_time` int(11) NOT NULL DEFAULT '0' COMMENT '最近连接时间',
  `is_del` tinyint(4) NOT NULL DEFAULT '1' COMMENT '删除状态，1是未删除，2是已删除',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `isonline` char(1) DEFAULT NULL COMMENT '0不在线，1在线',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '设备主绑定人',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '锁定状态，1是未锁定，2是已锁定',
  `nickname` varchar(32) DEFAULT NULL COMMENT '设备名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备表';

-- --------------------------------------------------------

--
-- 表的结构 `md_device_bind`
--

CREATE TABLE `md_device_bind` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `device_id` int(11) NOT NULL DEFAULT '0' COMMENT '设备ID',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '设备自定义名称',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态，1是已绑定，2是已拒绝，3是待审核',
  `bind_time` int(11) NOT NULL DEFAULT '0' COMMENT '绑定时间',
  `unbind_time` int(11) NOT NULL DEFAULT '0' COMMENT '解绑时间',
  `type` int(1) NOT NULL DEFAULT '2' COMMENT '1是默认，2是不默认',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '设备主绑定人'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户绑定设备表';

-- --------------------------------------------------------

--
-- 表的结构 `md_device_data`
--

CREATE TABLE `md_device_data` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL DEFAULT '-1' COMMENT '设备ID',
  `review` tinyint(4) NOT NULL DEFAULT '0' COMMENT '评价，1表示表扬，2表示鼓励，3表示吐槽，0：不评价',
  `people` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否在座位，0表示未在座位，1表示在座位',
  `focus` tinyint(4) NOT NULL DEFAULT '0' COMMENT '专注，1表示专注，0表示不专注',
  `pshake` tinyint(4) NOT NULL DEFAULT '0' COMMENT '晃动，1表示晃动，0表示不动',
  `pgood` tinyint(4) NOT NULL DEFAULT '0' COMMENT '标准，1表示标准，0表示不标准',
  `pback` tinyint(4) NOT NULL DEFAULT '0' COMMENT '驼背，1表示驼背，0表示不驼背',
  `pleftdev` tinyint(4) NOT NULL DEFAULT '0' COMMENT '左倾，1表示左倾，0表示不左倾',
  `prightdev` tinyint(4) NOT NULL DEFAULT '0' COMMENT '右倾，1表示右倾，0表示不右倾',
  `pleftrota` tinyint(4) NOT NULL DEFAULT '0' COMMENT '左旋，1表示左旋，0表示不左旋',
  `prightrota` tinyint(4) NOT NULL DEFAULT '0' COMMENT '右旋，1表示右旋，0表示不右旋',
  `temp` decimal(10,2) DEFAULT NULL COMMENT '温度',
  `humi` decimal(10,2) DEFAULT NULL COMMENT '湿度',
  `heart` tinyint(4) NOT NULL DEFAULT '0' COMMENT '红心，1表示获得红心，0表示未获得红心，2表示获得蓝心',
  `date` varchar(64) NOT NULL DEFAULT '' COMMENT '上报时间日期',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '上报时间',
  `type` int(10) DEFAULT NULL COMMENT '类型 0无 1专注 2晃动 3标准 4驼背 5左倾 6右倾 7左旋 8右旋'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备上报平台数据表';

-- --------------------------------------------------------

--
-- 表的结构 `md_device_error`
--

CREATE TABLE `md_device_error` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL DEFAULT '0' COMMENT '设备ID',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '错误类型',
  `code` int(11) NOT NULL DEFAULT '0' COMMENT '错误码',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '上报时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备故障上报表';

-- --------------------------------------------------------

--
-- 表的结构 `md_device_heart_log`
--

CREATE TABLE `md_device_heart_log` (
  `id` int(10) NOT NULL,
  `device_id` int(10) NOT NULL DEFAULT '0' COMMENT '设备id',
  `number` int(10) NOT NULL DEFAULT '0' COMMENT '红心数',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '1是增加，2是减少',
  `content` text COMMENT '红心描述',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备红心表';

-- --------------------------------------------------------

--
-- 表的结构 `md_device_report`
--

CREATE TABLE `md_device_report` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `device_id` int(11) NOT NULL DEFAULT '0' COMMENT '设备ID',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '报修原因',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备报修表';

-- --------------------------------------------------------

--
-- 表的结构 `md_device_set`
--

CREATE TABLE `md_device_set` (
  `id` int(11) NOT NULL,
  `device_id` int(11) DEFAULT '0' COMMENT '设备ID',
  `review` tinyint(4) DEFAULT '0' COMMENT '评价，1表示表扬，2表示鼓励，3表示吐槽，0：不评价',
  `poseactive` varchar(64) DEFAULT '' COMMENT '标准身姿主动练习00:03:00',
  `pose_active` int(11) DEFAULT '0' COMMENT '标准身姿主动练习时间，单位秒',
  `goodtime` varchar(64) DEFAULT '' COMMENT '标准姿态保持时间00:20:00',
  `good_time` int(11) DEFAULT '0' COMMENT '标准姿态保持时间，单位分钟',
  `badtime` varchar(64) DEFAULT '' COMMENT '错误姿态提示00:00:10',
  `bad_time` int(11) DEFAULT '0' COMMENT '错误姿态提示时间，单位秒',
  `eyetime` varchar(64) DEFAULT '' COMMENT '眼部疲劳时间00:45:00',
  `eye_time` int(11) DEFAULT '0' COMMENT '眼部疲劳时间，单位分钟',
  `eyeactive` varchar(64) DEFAULT '' COMMENT '眼部保健时间00:10:00',
  `eye_active` int(11) DEFAULT '0' COMMENT '眼部保健时间，单位分钟',
  `bodytime` varchar(64) DEFAULT '' COMMENT '身体疲劳时间00:50:00',
  `body_time` int(11) DEFAULT '0' COMMENT '身体疲劳时间，单位分钟',
  `bodyactive` varchar(64) DEFAULT '' COMMENT '身体运动恢复时间00:05:00',
  `body_active` int(11) DEFAULT '0' COMMENT '身体运动恢复时间，单位分钟',
  `type` tinyint(4) DEFAULT '0' COMMENT '类型，1表示平台下发设置信息，2表示设备返回（用户小程序设置）',
  `date` varchar(64) DEFAULT '' COMMENT '上报时间日期',
  `add_time` int(11) DEFAULT '0' COMMENT '上报时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态 0为正在上传，1为上传成功',
  `possensit` int(11) DEFAULT NULL COMMENT '颈环灵敏度3档，范围1-5（档）',
  `goodsensit` int(11) DEFAULT NULL COMMENT '颈环灵敏度3档，范围1-5（档'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备信息表';

-- --------------------------------------------------------

--
-- 表的结构 `md_device_setting`
--

CREATE TABLE `md_device_setting` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT '0' COMMENT '用户ID',
  `device_id` int(11) DEFAULT '0' COMMENT '设备ID',
  `review` tinyint(4) DEFAULT '0' COMMENT '评价，1表示表扬，2表示鼓励，3表示吐槽，0：不评价',
  `poseactive` varchar(64) DEFAULT '' COMMENT '标准身姿主动练习00:03:00',
  `pose_active` int(11) DEFAULT '0' COMMENT '标准身姿主动练习时间，单位秒',
  `goodtime` varchar(64) DEFAULT '' COMMENT '标准姿态保持时间00:20:00',
  `good_time` int(11) DEFAULT '0' COMMENT '标准姿态保持时间，单位分钟',
  `badtime` varchar(64) DEFAULT '' COMMENT '错误姿态提示00:00:10',
  `bad_time` int(11) DEFAULT '0' COMMENT '错误姿态提示时间，单位分钟',
  `eyetime` varchar(64) DEFAULT '' COMMENT '眼部疲劳时间00:45:00',
  `eye_time` int(11) DEFAULT '0' COMMENT '眼部疲劳时间，单位分钟',
  `eyeactive` varchar(64) DEFAULT '' COMMENT '眼部保健时间00:10:00',
  `eye_active` int(11) DEFAULT '0' COMMENT '眼部保健时间，单位分钟',
  `bodytime` varchar(64) DEFAULT '' COMMENT '身体疲劳时间00:50:00',
  `body_time` int(11) DEFAULT '0' COMMENT '身体疲劳时间，单位分钟',
  `bodyactive` varchar(64) DEFAULT '' COMMENT '身体运动恢复时间00:05:00',
  `body_active` int(11) DEFAULT '0' COMMENT '身体运动恢复时间，单位分钟',
  `date` varchar(64) DEFAULT '' COMMENT '上报时间日期',
  `add_time` int(11) DEFAULT '0' COMMENT '上报时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态 0为正在上传，1为上传成功',
  `possensit` int(11) DEFAULT NULL COMMENT '颈环灵敏度3档，范围1-5（档）',
  `goodsensit` int(11) DEFAULT NULL COMMENT '颈环灵敏度3档，范围1-5（档',
  `type` int(1) NOT NULL DEFAULT '2' COMMENT '类型，1表示平台下发设置信息，2表示设备返回（用户小程序设置）;3表示教师或者校长端设置的	'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备信息表';

-- --------------------------------------------------------

--
-- 表的结构 `md_fankui`
--

CREATE TABLE `md_fankui` (
  `id` int(10) NOT NULL,
  `device_id` int(10) NOT NULL DEFAULT '0' COMMENT '设备id',
  `content` text COMMENT '反馈内容',
  `photo` text COMMENT '图片',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户反馈表';

-- --------------------------------------------------------

--
-- 表的结构 `md_goods`
--

CREATE TABLE `md_goods` (
  `id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '商品标题',
  `cover` varchar(1024) NOT NULL DEFAULT '' COMMENT '商品主图',
  `is_show` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态 1：上架 2：下架',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `price` int(11) NOT NULL DEFAULT '0' COMMENT '小红心数',
  `detail` text NOT NULL COMMENT '商品详情',
  `is_del` tinyint(4) NOT NULL DEFAULT '1' COMMENT '删除状态，1是未删除，2是已删除',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品表';

-- --------------------------------------------------------

--
-- 表的结构 `md_head_bind`
--

CREATE TABLE `md_head_bind` (
  `id` int(10) NOT NULL,
  `tid` int(10) NOT NULL COMMENT '校长id',
  `did` int(10) NOT NULL COMMENT '设备id',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '分配状态。1是未分配，2是已分配',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分配给校长设备表';

-- --------------------------------------------------------

--
-- 表的结构 `md_manage`
--

CREATE TABLE `md_manage` (
  `id` int(11) NOT NULL,
  `account` varchar(100) NOT NULL DEFAULT '' COMMENT '登录账号',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `token` char(32) NOT NULL DEFAULT '' COMMENT 'token',
  `avatar` varchar(255) NOT NULL DEFAULT '/static/images/default.png' COMMENT '头像',
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色ID',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态,1为正常，2为禁止',
  `is_del` tinyint(4) NOT NULL DEFAULT '1' COMMENT '删除状态，1是未删除，2是已删除',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表';

--
-- 转存表中的数据 `md_manage`
--

INSERT INTO `md_manage` (`id`, `account`, `password`, `token`, `avatar`, `username`, `mobile`, `role_id`, `status`, `is_del`, `add_time`, `update_time`, `login_time`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'a0cadd9fb8e8efc4d360c7e1de7b55ac', 'https://admindaikebao.mangdin.net/uploads/images/20230913/d8b3318756a37b0c44ec0f76af4420bb.jpg', '超级管理员', '', 1, 1, 1, 1685516923, 1727077426, 1690795600),
(2, 'test', 'e10adc3949ba59abbe56e057f20f883e', '', 'https://admindaikebao.mangdin.net/uploads/images/20231114/a384648f2baa3ed73c3dd427b2a65c9e.png', 'LL', '', 2, 1, 2, 1699928994, 1704166952, 0),
(3, 'test111', 'e10adc3949ba59abbe56e057f20f883e', '', 'https://admindaikebao.mangdin.net/uploads/images/20231114/1642211bc1c41b779a369286663bf526.png', 'qq', '', 3, 1, 2, 1699929016, 1704166965, 0),
(4, 'admin1', 'e10adc3949ba59abbe56e057f20f883e', '63f361bf3e9dbcf1a3e980045e0b6691', 'https://admindaikebao.mangdin.net/uploads/images/20231114/6adbbe4313bcb15b72ca861281da2438.png', 'nichen', '', 1, 1, 1, 1699930599, 1704173613, 0),
(5, 'admin2', 'e10adc3949ba59abbe56e057f20f883e', '', 'https://admindaikebao.mangdin.net/uploads/images/20240102/ddb2392ffe7f94a07f1fdb4e44ac9c95.png', 'qqq111', '', 1, 1, 1, 1704174590, 1704183788, 0),
(6, 'lkr', '879ba602b211d8d26e70ec6cf6e6fd71', '', 'https://admindaikebao.mangdin.net/uploads/images/20231114/a384648f2baa3ed73c3dd427b2a65c9e.png', 'LL', '', 2, 1, 2, 1704849729, 0, 0),
(8, 'liukeran', '806353c24d2e6fc82a992fb11f122a7f', 'ef1c64614d775db24def293b876c78ab', 'https://admindaikebao.mangdin.net/uploads/images/20231114/a384648f2baa3ed73c3dd427b2a65c9e.png', 'liu', '', 2, 1, 1, 1704849879, 0, 0),
(9, 'guojunwen', 'ef5e80ee28a02435268aa4cbf0084fdc', 'fdb6dd23fe0ad42a4a09babbfb2d1142', 'https://admindaikebao.mangdin.net/uploads/images/20231114/a384648f2baa3ed73c3dd427b2a65c9e.png', 'guoguo', '', 2, 1, 1, 1704849913, 0, 0),
(10, 'mangdin', 'e10adc3949ba59abbe56e057f20f883e', '', 'https://server.deekbot.com/uploads/images/20240326/57757c9c478c052dd596b64782ef5d35.png', 'halou', '', 4, 1, 1, 1711418852, 1711420696, 0),
(11, 'Doczhang', '4e1febc361e804187916c623fd115c71', '5d7c91cc8febed4544f35d837ec51904', 'https://server.deekbot.com/uploads/images/20240328/9592cd13801423431935f394f567003c.png', 'Doczhang', '', 4, 1, 2, 1711584803, 1711584824, 0),
(13, 'zhang', '0e2cdf0317770bb67f164672be47c4ad', 'b2509bfc47fef3a83d4bbc1c4c4d4835', 'https://server.deekbot.com/uploads/images/20240328/5623bdb97cd73f9747f5c1574dd60e81.png', 'Doczhang', '', 4, 1, 1, 1711599712, 1711599721, 0),
(14, '13224632188', 'ef84701a20b1d92fb12b1697330487de', '', 'https://server.deekbot.com/uploads/images/20240418/e6938c318c5ca0455ceee2584bec898e.jpg', '蕴琦', '', 3, 1, 1, 1713397996, 1713398018, 0);

-- --------------------------------------------------------

--
-- 表的结构 `md_new_device_data`
--

CREATE TABLE `md_new_device_data` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL DEFAULT '-1' COMMENT '设备ID',
  `task` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启，0代表未开启，1代表开启',
  `tstart` varchar(16) DEFAULT NULL,
  `tover` varchar(16) DEFAULT NULL,
  `tpose` varchar(32) DEFAULT NULL COMMENT '体态练习时长，已进行1小时零3秒',
  `xspose` varchar(16) DEFAULT NULL COMMENT 'X轴标准体态数据',
  `yspose` varchar(16) DEFAULT NULL COMMENT 'Y轴标准体态数据',
  `zspose` varchar(16) DEFAULT NULL COMMENT 'Z轴标准体态数据',
  `xpose` varchar(16) DEFAULT NULL COMMENT 'X轴实时体态数据',
  `ypose` varchar(16) DEFAULT NULL COMMENT 'Y轴实时体态数据',
  `zpose` varchar(16) DEFAULT NULL COMMENT 'Z轴实时体态数据',
  `vxpose` varchar(16) DEFAULT NULL COMMENT 'X角速度',
  `vypose` varchar(16) DEFAULT NULL COMMENT 'Y角速度',
  `vzpose` varchar(16) DEFAULT NULL COMMENT 'Z角速度',
  `battery` varchar(16) DEFAULT NULL COMMENT '电池电量',
  `tsleep` varchar(16) DEFAULT NULL COMMENT '实时睡眠开始',
  `close_clock` varchar(16) DEFAULT NULL COMMENT '实时起床时间   闹钟关闭时间',
  `pgood` int(1) NOT NULL DEFAULT '0' COMMENT '标准，1表示标准，0表示不标准',
  `pback` tinyint(4) NOT NULL DEFAULT '0' COMMENT '驼背，1表示驼背，0表示不驼背',
  `pleftdev` tinyint(4) NOT NULL DEFAULT '0' COMMENT '左倾，1表示左倾，0表示不左倾',
  `prightdev` tinyint(4) NOT NULL DEFAULT '0' COMMENT '右倾，1表示右倾，0表示不右倾',
  `pleftrota` tinyint(4) NOT NULL DEFAULT '0' COMMENT '左旋，1表示左旋，0表示不左旋',
  `prightrota` tinyint(4) NOT NULL DEFAULT '0' COMMENT '右旋，1表示右旋，0表示不右旋',
  `temp` decimal(10,2) DEFAULT NULL COMMENT '温度',
  `humi` decimal(10,2) DEFAULT NULL COMMENT '湿度',
  `to` tinyint(4) NOT NULL DEFAULT '0' COMMENT '番茄，大于0表示获得番茄心，0表示未获得番茄',
  `date` varchar(64) NOT NULL DEFAULT '' COMMENT '上报时间日期',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '上报时间',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '数据类型，1是普通数据，2是睡觉数据3是叫醒数据'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备上报平台数据表';

-- --------------------------------------------------------

--
-- 表的结构 `md_new_device_set`
--

CREATE TABLE `md_new_device_set` (
  `id` int(10) NOT NULL,
  `device_id` int(10) NOT NULL DEFAULT '0' COMMENT '设备id',
  `clock` varchar(32) DEFAULT NULL COMMENT '闹钟1时间7点',
  `bel` int(1) NOT NULL DEFAULT '1' COMMENT '闹铃1铃声',
  `delay_time` varchar(16) DEFAULT NULL COMMENT '闹铃1延时时间',
  `delay_num` int(2) NOT NULL DEFAULT '0' COMMENT '闹铃1延时提醒次数',
  `to_clock` varchar(16) DEFAULT NULL COMMENT '一个番茄时钟的时间',
  `to_bell` int(2) NOT NULL DEFAULT '1' COMMENT '番茄时钟铃声',
  `to_rest` varchar(16) DEFAULT '5' COMMENT '番茄休息时间',
  `to_recover` int(10) DEFAULT '15' COMMENT '/10min-30min能量恢复（累计4个番茄后休息时间）',
  `hp_clock` int(5) NOT NULL DEFAULT '20' COMMENT '驼背报警的角度',
  `lp_clock` int(5) NOT NULL DEFAULT '11' COMMENT '侧倾的角度',
  `ol_clock` int(5) NOT NULL DEFAULT '11' COMMENT '侧旋的角度',
  `pose_delay` varchar(16) DEFAULT '5' COMMENT '延时报警时间，5秒',
  `pose_bell` int(2) NOT NULL DEFAULT '1' COMMENT '体态报警铃声',
  `sleep` varchar(16) DEFAULT NULL COMMENT '自动睡眠时间',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `biaozhun` int(1) NOT NULL DEFAULT '1' COMMENT '是否知道标准坐姿，1是知道，2是不知道',
  `fuan` int(1) NOT NULL DEFAULT '1' COMMENT '伏案程度，1是经常，2是偶尔',
  `qita` varchar(16) DEFAULT NULL COMMENT '其他不良',
  `study_time` varchar(16) DEFAULT NULL COMMENT '每天学习总时长',
  `age` int(3) NOT NULL DEFAULT '0' COMMENT '年龄',
  `dengji` varchar(3) NOT NULL DEFAULT 'B' COMMENT '等级',
  `train_time` varchar(16) DEFAULT '30' COMMENT '每天练习时间',
  `volume` int(1) NOT NULL DEFAULT '4' COMMENT '设备音量调节'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='5';

-- --------------------------------------------------------

--
-- 表的结构 `md_new_device_setting`
--

CREATE TABLE `md_new_device_setting` (
  `id` int(10) NOT NULL,
  `device_id` int(10) NOT NULL DEFAULT '0' COMMENT '设备id',
  `clock` varchar(32) DEFAULT NULL COMMENT '闹钟1时间7点',
  `bel` int(1) NOT NULL DEFAULT '0' COMMENT '闹铃1铃声',
  `delay_time` varchar(16) DEFAULT NULL COMMENT '闹铃1延时时间',
  `delay_num` int(2) NOT NULL DEFAULT '0' COMMENT '闹铃1延时提醒次数',
  `to_clock` varchar(16) DEFAULT NULL COMMENT '一个番茄时钟的时间',
  `to_bell` int(2) NOT NULL DEFAULT '0' COMMENT '番茄时钟铃声',
  `to_rest` varchar(16) DEFAULT NULL COMMENT '番茄休息时间',
  `to_recover` varchar(16) DEFAULT NULL COMMENT '/10min-30min能量恢复（累计4个番茄后休息时间）',
  `hp_clock` int(5) NOT NULL DEFAULT '0' COMMENT '驼背报警的角度',
  `lp_clock` int(5) NOT NULL DEFAULT '0' COMMENT '侧倾的角度',
  `ol_clock` int(5) NOT NULL DEFAULT '0' COMMENT '侧旋的角度',
  `pose_delay` varchar(16) DEFAULT NULL COMMENT '延时报警时间，5秒',
  `pose_bell` int(2) NOT NULL DEFAULT '0' COMMENT '体态报警铃声',
  `sleep` varchar(16) DEFAULT NULL COMMENT '自动睡眠时间',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `biaozhun` int(1) NOT NULL DEFAULT '1' COMMENT '是否知道标准坐姿，1是知道，2是不知道',
  `fuan` int(1) NOT NULL DEFAULT '1' COMMENT '伏案程度，1是经常，2是偶尔',
  `qita` varchar(16) DEFAULT NULL COMMENT '其他不良',
  `study_time` varchar(16) DEFAULT NULL COMMENT '每天学习总时长',
  `age` int(3) NOT NULL DEFAULT '0' COMMENT '年龄',
  `dengji` varchar(3) DEFAULT NULL COMMENT '等级',
  `volume` int(1) NOT NULL DEFAULT '1' COMMENT '设备音量调节',
  `train_time` varchar(16) DEFAULT NULL COMMENT '每天练习时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='5';

-- --------------------------------------------------------

--
-- 表的结构 `md_order`
--

CREATE TABLE `md_order` (
  `id` int(11) NOT NULL COMMENT '订单id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `device_id` int(11) NOT NULL DEFAULT '0' COMMENT '设备ID',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品id',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '商品标题',
  `cover` varchar(1024) NOT NULL DEFAULT '' COMMENT '商品主图',
  `sn` varchar(32) DEFAULT NULL COMMENT '订单编号',
  `quantity` int(11) NOT NULL DEFAULT '0' COMMENT '商品数量',
  `red` int(11) NOT NULL DEFAULT '0' COMMENT '红心数',
  `status` int(11) NOT NULL DEFAULT '10' COMMENT '订单状态 10待支付，20已付款（未发货），30已发货（待收货），40已收货（待评价），50已评价（交易完成），60超时自动取消，70已退款',
  `is_pay` tinyint(1) NOT NULL DEFAULT '3' COMMENT '支付方式 1：微信 2：支付宝 3：红心支付',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `mobile` varchar(60) NOT NULL DEFAULT '' COMMENT '手机',
  `shipping_name` varchar(120) NOT NULL DEFAULT '' COMMENT '物流名称',
  `shipping_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '快递单号',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `pay_time` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `send_time` int(11) NOT NULL DEFAULT '0' COMMENT '发货时间',
  `cancel_time` int(11) NOT NULL DEFAULT '0' COMMENT '取消时间',
  `refund_time` int(11) NOT NULL DEFAULT '0' COMMENT '退款时间',
  `confirm_time` int(11) NOT NULL DEFAULT '0' COMMENT '收货确认时间',
  `user_note` varchar(255) NOT NULL DEFAULT '' COMMENT '用户备注',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '删除状态，1是未删除，2是已删除',
  `refund_note` varchar(256) DEFAULT NULL COMMENT '退款说明(拒绝说明)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品订单表';

-- --------------------------------------------------------

--
-- 表的结构 `md_recharge`
--

CREATE TABLE `md_recharge` (
  `id` int(11) NOT NULL COMMENT 'id',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `integral` int(11) NOT NULL DEFAULT '0' COMMENT '充值流量数量',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1：未删除 2：已删除',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '编辑时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='流量充值表';

-- --------------------------------------------------------

--
-- 表的结构 `md_recharge_order`
--

CREATE TABLE `md_recharge_order` (
  `id` int(11) NOT NULL COMMENT 'id',
  `recharge_id` int(11) NOT NULL DEFAULT '0' COMMENT '充值ID',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `integral` int(11) NOT NULL DEFAULT '0' COMMENT '充值流量数量',
  `give_integral` int(11) NOT NULL DEFAULT '0' COMMENT '到账流量数量',
  `sn` varchar(128) NOT NULL DEFAULT '' COMMENT '订单号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 1：待付款 2：已付款',
  `is_pay` tinyint(1) NOT NULL DEFAULT '1' COMMENT '方式 1：微信支付 2：支付宝支付',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `pay_time` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '删除状态，1是未删除，2是已删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户流量充值订单表';

-- --------------------------------------------------------

--
-- 表的结构 `md_sys_menu`
--

CREATE TABLE `md_sys_menu` (
  `menuId` bigint(20) NOT NULL,
  `menuName` varchar(50) NOT NULL COMMENT '菜单名称',
  `parentId` bigint(20) DEFAULT '0' COMMENT '父菜单ID',
  `orderNum` int(4) DEFAULT '0' COMMENT '显示顺序',
  `path` varchar(200) DEFAULT '' COMMENT '路由地址',
  `component` varchar(255) DEFAULT NULL COMMENT '组件路径',
  `query` varchar(255) DEFAULT NULL COMMENT '路由参数',
  `isFrame` char(1) DEFAULT '1' COMMENT '是否为外链（0是 1否）',
  `isCache` char(1) DEFAULT '0' COMMENT '是否缓存（0缓存 1不缓存）',
  `isAffix` char(1) DEFAULT '0' COMMENT '是否固定',
  `menuType` char(1) DEFAULT '' COMMENT '菜单类型（M目录 C菜单 F按钮）',
  `visible` char(1) DEFAULT '0' COMMENT '菜单状态（0显示 1隐藏）',
  `status` char(1) DEFAULT '1' COMMENT '菜单状态（1正常 2停用）',
  `perms` varchar(100) DEFAULT NULL COMMENT '权限标识',
  `icon` varchar(100) DEFAULT '#' COMMENT '菜单图标',
  `is_del` tinyint(4) NOT NULL DEFAULT '1' COMMENT '删除状态，1是未删除，2是已删除',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单权限表';

--
-- 转存表中的数据 `md_sys_menu`
--

INSERT INTO `md_sys_menu` (`menuId`, `menuName`, `parentId`, `orderNum`, `path`, `component`, `query`, `isFrame`, `isCache`, `isAffix`, `menuType`, `visible`, `status`, `perms`, `icon`, `is_del`, `add_time`, `update_time`) VALUES
(1, '首页', 0, 1, 'home', 'home/index', '', '1', '0', '0', 'C', '0', '1', NULL, 'iconfont icon-shuju', 1, 1691726381, 0),
(2, '系统管理', 0, 2, 'system', 'layout/routerView/parent', '', '1', '0', '0', 'C', '0', '1', NULL, 'iconfont icon-xitongshezhi', 1, 1691734455, 0),
(3, '菜单管理', 2, 1, '/system/menu', 'system/menu/index', '', '1', '0', '0', 'C', '0', '1', NULL, 'iconfont icon-caidan', 1, 1691734494, 0),
(4, '角色管理', 2, 2, '/system/role', 'system/role/index', '', '1', '0', '0', 'C', '0', '1', NULL, 'iconfont icon-neiqianshujuchucun', 1, 1691734527, 1692081070),
(5, '管理员管理', 2, 3, '/system/manage', 'system/manage/index', '', '1', '0', '0', 'C', '0', '1', NULL, 'iconfont icon-shuju', 1, 1691734527, 1692081173),
(10, '操作日志', 2, 5, '/system/log', '/system/log/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-tongzhi3', 1, 1692077680, 1692077688),
(11, '用户管理', 2, 4, '/system/user', '/system/user/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-gerenzhongxin', 1, 1692081046, 0),
(12, '系统设置', 0, 3, 'setting', 'layout/routerView/parent', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-caidan', 1, 1692150959, 0),
(13, '网站设置', 12, 99, '/setting/website', '/setting/website/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-ico_shuju', 1, 1692152097, 0),
(14, '商城管理', 0, 4, 'shop', 'layout/routerView/parent', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-gongju', 2, 1692251218, 0),
(15, '商品分类', 14, 1, '/shop/cate', '/shop/cate/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-fuzhiyemian', 2, 1692251303, 0),
(16, '商品管理', 14, 3, '/shop/goods', '/shop/goods/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-ico_shuju', 2, 1692251303, 1692327647),
(17, '订单管理', 0, 5, 'order', 'layout/routerView/parent', '', '1', '0', '0', 'C', '0', '0', NULL, 'ele-ShoppingTrolley', 1, 1692777742, 0),
(18, '商品订单', 17, 1, '/order/goods', '/order/goods/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-caidan', 1, 1692777800, 0),
(19, '商品规格', 14, 2, '/shop/rule', '/shop/rule/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-zhongyingwenqiehuan', 2, 1692847659, 0),
(20, '商品评论', 14, 4, '/shop/reply', '/shop/reply/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-zhongyingwenqiehuan', 2, 1692847659, 0),
(21, '推荐视频', 12, 1, '/setting/video', '/setting/video/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-step', 1, 1694399643, 0),
(22, '流量充值', 12, 2, '/setting/recharge', '/setting/recharge/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-zaosheng', 1, 1694399643, 1694411862),
(23, '红心商品', 12, 2, '/setting/goods', '/setting/goods/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-shenqingkaiban', 1, 1694399643, 1694418085),
(24, '充值订单', 17, 2, '/order/recharge', '/order/recharge/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-bolangnengshiyanchang', 1, 1692777800, 1694481645),
(25, '设备管理', 0, 4, 'layout/routerView/parent', 'device', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-zidingyibuju', 1, 1695105352, 0),
(26, '设备查看', 25, 1, '/device/device', '/device/device/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-huanjingxingqiu', 1, 1695105488, 0),
(27, '设备升级', 25, 2, '/device/version', '/device/version/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon-shangchuan', 1, 1695176731, 0),
(28, '设备报修', 25, 3, '/device/report', '/device/report/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'iconfont icon--chaifenhang', 1, 1700040203, 0),
(29, '校长管理', 0, 6, 'tearcher', 'layout/routerView/parent', '', '1', '0', '0', 'C', '0', '0', NULL, 'ele-Avatar', 1, 1712112633, 0),
(30, '校长列表', 29, 0, '/setting/tearcher', '/setting/tearcher/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'fa fa-user-o', 1, 1712112723, 0),
(31, '用户反馈', 12, 0, '/setting/fankui', '/setting/fankui/index', '', '1', '0', '0', 'C', '0', '0', NULL, 'ele-WarnTriangleFilled', 1, 1728633409, 0);

-- --------------------------------------------------------

--
-- 表的结构 `md_sys_role`
--

CREATE TABLE `md_sys_role` (
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `role_name` varchar(30) NOT NULL DEFAULT '' COMMENT '角色名称',
  `role_key` varchar(100) NOT NULL DEFAULT '' COMMENT '角色权限字符串',
  `role_sort` int(4) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `data_scope` char(1) DEFAULT '1' COMMENT '数据范围（1：全部数据权限 2：自定数据权限 3：本部门数据权限 4：本部门及以下数据权限）',
  `menu_check_strictly` tinyint(1) DEFAULT '1' COMMENT '菜单树选择项是否关联显示',
  `dept_check_strictly` tinyint(1) DEFAULT '1' COMMENT '部门树选择项是否关联显示',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '角色状态（1正常 2停用）',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '删除标志（1代表存在 2代表删除）',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色信息表';

--
-- 转存表中的数据 `md_sys_role`
--

INSERT INTO `md_sys_role` (`role_id`, `role_name`, `role_key`, `role_sort`, `data_scope`, `menu_check_strictly`, `dept_check_strictly`, `status`, `is_del`, `add_time`, `update_time`, `remark`) VALUES
(1, '超级管理员', 'admin', 1, '1', 1, 1, 1, 1, 1691130616, 1712113079, '超级管理员角色'),
(2, '业务管理员', 'business', 2, '1', 1, 1, 1, 1, 1691747186, 1704166881, '业务权限管理员'),
(3, '生产管理员', 'produce', 3, '1', 1, 1, 1, 1, 1704166932, 1704173512, '生产管理员'),
(4, '运营管理员', 'yunying', 4, '1', 1, 1, 1, 1, 1711416510, 1711417496, '查看数据');

-- --------------------------------------------------------

--
-- 表的结构 `md_sys_role_menu`
--

CREATE TABLE `md_sys_role_menu` (
  `role_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '角色ID',
  `menu_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '菜单ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色和菜单关联表';

--
-- 转存表中的数据 `md_sys_role_menu`
--

INSERT INTO `md_sys_role_menu` (`role_id`, `menu_id`) VALUES
(2, 1),
(2, 12),
(2, 17),
(2, 18),
(2, 21),
(2, 23),
(3, 1),
(3, 25),
(3, 26),
(4, 1),
(4, 25),
(4, 26),
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 17),
(1, 18),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31);

-- --------------------------------------------------------

--
-- 表的结构 `md_tearcher`
--

CREATE TABLE `md_tearcher` (
  `id` int(10) NOT NULL,
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '上级id',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '身份，1是校长，2是老师',
  `cate` int(1) NOT NULL DEFAULT '1' COMMENT '校长等级分类，1-5级，仅适用于校长',
  `nickname` varchar(32) NOT NULL COMMENT '姓名',
  `phone` varchar(11) NOT NULL COMMENT '手机号码',
  `username` varchar(32) DEFAULT NULL COMMENT '账号',
  `password` varchar(32) DEFAULT NULL COMMENT '密码',
  `token` varchar(32) DEFAULT NULL COMMENT 'token',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '禁用状态；1是未禁用，2是已禁用',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='校长老师表';

-- --------------------------------------------------------

--
-- 表的结构 `md_tearcher_bind`
--

CREATE TABLE `md_tearcher_bind` (
  `id` int(10) NOT NULL,
  `tid` int(10) NOT NULL DEFAULT '0' COMMENT '老师id',
  `did` int(10) NOT NULL DEFAULT '0' COMMENT '设备id',
  `nickname` varchar(32) DEFAULT NULL COMMENT '设备名称',
  `cate` int(10) NOT NULL DEFAULT '0' COMMENT '班级id',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='老师设备表';

-- --------------------------------------------------------

--
-- 表的结构 `md_tearcher_cate`
--

CREATE TABLE `md_tearcher_cate` (
  `id` int(10) NOT NULL,
  `title` varchar(32) DEFAULT NULL COMMENT '名称',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `tid` int(10) NOT NULL DEFAULT '0' COMMENT '老师id',
  `date_time` int(10) NOT NULL DEFAULT '0' COMMENT '日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='教师分类表';

-- --------------------------------------------------------

--
-- 表的结构 `md_user`
--

CREATE TABLE `md_user` (
  `id` int(11) NOT NULL,
  `openid` varchar(64) NOT NULL DEFAULT '' COMMENT '用户openid',
  `avatar` varchar(1024) NOT NULL DEFAULT '/static/images/default.png' COMMENT '用户头像',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户账号',
  `nickname` varchar(16) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `token` char(32) NOT NULL DEFAULT '' COMMENT '登录token',
  `desc` varchar(512) NOT NULL DEFAULT '' COMMENT '描述',
  `status` tinyint(4) NOT NULL DEFAULT '2' COMMENT '审核状态，1是待审核，2是审核通过，3是审核失败，4是封禁，5是注销',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '流量余额',
  `reason` varchar(256) NOT NULL DEFAULT '' COMMENT '审核失败原因',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '删除状态，1是未删除，2是已删除',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `verify_time` int(11) NOT NULL DEFAULT '0' COMMENT '审核时间',
  `login_num` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

-- --------------------------------------------------------

--
-- 表的结构 `md_version`
--

CREATE TABLE `md_version` (
  `id` int(11) NOT NULL,
  `version` varchar(64) NOT NULL DEFAULT '' COMMENT '版本',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '更新内容',
  `page` text COMMENT '下载地址',
  `is_update` tinyint(1) NOT NULL DEFAULT '1' COMMENT '重要更新 1：是 2：否',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1：未删除 2：已删除',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '编辑时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='版本管理表';

-- --------------------------------------------------------

--
-- 表的结构 `md_video`
--

CREATE TABLE `md_video` (
  `id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '标题',
  `cover` varchar(1024) NOT NULL DEFAULT '' COMMENT '封面图片',
  `url` varchar(1024) NOT NULL DEFAULT '' COMMENT '视频播放地址',
  `is_show` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态 1：显示 2：隐藏',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(4) NOT NULL DEFAULT '1' COMMENT '删除状态，1是未删除，2是已删除',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐视频表';

-- --------------------------------------------------------

--
-- 表的结构 `md_website`
--

CREATE TABLE `md_website` (
  `id` int(11) NOT NULL,
  `weixin` varchar(32) NOT NULL DEFAULT '' COMMENT '微信',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '邮箱',
  `phone` varchar(32) NOT NULL DEFAULT '' COMMENT '联系电话',
  `times` int(11) NOT NULL DEFAULT '15' COMMENT '简心跳时间间隔，单位秒',
  `user_service` text NOT NULL COMMENT '隐私协议',
  `user_agreement` text NOT NULL COMMENT '服务协议',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站设置表';

--
-- 转存表中的数据 `md_website`
--

INSERT INTO `md_website` (`id`, `weixin`, `email`, `phone`, `times`, `user_service`, `user_agreement`, `update_time`) VALUES
(1, 'deekbot', 'deekbot@163.com', '+86 18563986196', 16, '<p style=\"text-align: center;\"><strong>呆可宝机器人</strong></p><p style=\"text-align: center;\">隐私政策</p><p>我们的隐私政策已于 2023年10月10日更新。请花一些时间熟悉我们的隐私政策，如果您有任何问题，请联系我们。</p><p> </p><p><strong>引言</strong></p><p>呆可宝机器人软件服务是由青岛导行蜂智能科技有限公司所申请的呆可宝商标用于健康生活机器人服务 （以下简称“呆可宝机器人”） 为您提供的智能硬件设备的控制、管理及其衍生服务的平台。我们非常重视您的隐私。本隐私政策是针对呆可宝机器人软件服务做出的隐私方面的陈述与承诺，本隐私政策在制定时充分考虑到您的需求，您全面了解我们的个人信息收集和使用惯例，同时确保您最终能控制提供给我们的个人信息，这一点至关重要。本隐私政策规定我们如何收集、使用、披露、处理和存储您使用呆可宝机器人软件服务时提供给我们的信息。本隐私政策下“个人信息”指通过信息本身或通过关联其他信息后能够识别特定个人的数据。我们将严格遵守本隐私政策来使用这些信息。需要特别提醒您的是，微信客户端的隐私政策也同样适用于呆可宝机器人软件服务，即您使用呆可宝机器人软件服务也代表认同微信客户端的隐私政策。并且当该隐私政策和微信客户端隐私政策有所冲突时，以该隐私政策为准并且呆可宝机器人保留最终的解释权。最后，我们希望为用户带来最好的体验。如果您对本隐私政策中描述的个人信息处理实践有任何疑问，请通过<br>deekbot@163.com 联系我们，以便我们处理您的特殊需求。我们很高兴收到您的反馈。</p><p><strong>一、 &nbsp; &nbsp; &nbsp; &nbsp;我们收集哪些信息以及如何使用信息</strong></p><p><strong>（一） &nbsp;您须授权我们收集和使用您个人信息的情形</strong></p><p>收集个人信息的目的在于向您提供产品和/或服务，并且保证我们遵守适用的相关法律、法规及其他规范性文件。需要特别提醒您的是，大多数信息是微信客户端所必须要求的。您有权自行选择是否提供该信息，但多数情况下，如果您不提供，我们可能无法向您提供相应的服务，也无法回应您遇到的问题。这些功能包括：</p><p>(1） 智能设备连接</p><p>1.登录账号信息：微信相关账号信息（包括但不限于openiD、昵称、头像、性别等）、手机号及其他主动在平台上注册时所填写的相关信息。</p><p>2.手机相关信息：硬件设备标识(包括但不限于IMEl,Mac地址等）、手机型号、系统版本信息、系统语言、手机所设置的国家或地区、手机屏幕尺寸及分辨率等。</p><p>3.智能设备相关信息：智能设备的设备标识(包括但不限于设备序列号，Mac地址，IP地址、设备地理位置等）、蓝牙信息、智能设备的软件和固件版本号信息。</p><p>（2）设备共享</p><p>部分服务产生的信息和数据支持分享给他人，但这些均需要您的主动触发。呆可宝机器人不会在未经您的允许下对第三方进行暴露，支持分享的部分您在使用呆可宝机器人软件服务时都会有明显的页面提示。</p><p>(3）用户使用设备所产生的数据</p><p>用户在使用智能硬件后会产生相应的数据和内容，这些数据和内容用户同意授权我们进行保存并且同意我们在适用的法律情况下进行包括但不限于统计、分析等行为。</p><p>（4） 应用和智能硬件升级：</p><p>为了使您持续享受最新的呆可宝机器人软件服务，我们可能会使用您软件的版本信息、手机型号和账号信息等用于为您提供应用的升级服务。同时，我们可能收集您已连接的智能设备和版本号信息，用于为您提供智能设备的升级功能，以确保您可以使用最新版本的服务（包括固件版本）。</p><p>(5）提供内容支持(例如文章、有声内容播放）：</p><p>为了帮助您更好的使用智能设备，我们会为您提供相应的文章或视频，向您介绍与智能设备相关的内容。</p><p>阅读这些内容时，我们不会收集您的任何信息。</p><p>如果您在平台上连接了支持内容播放的智能设备，您可以在内容支持页面上选择和控制此智能设备播放的音乐或内容。我们可能会收集您账号下绑定的智能设备信息，以为您提供相应智能设备的内容播放和控制功能。</p><p>（6）用户反馈：</p><p>我们可能收集您发给我们的反馈的问题、反馈日志，以及您填写的电话号码或邮箱，这些信息将用于让我们更好的了解您遇到的问题，以及联系您。</p><p><strong>（二） &nbsp;您充分知晓，以下情形中，我们收集、使用个人信息无需征得您的同意：</strong></p><p>1.与国家安全、国防安全有关的；</p><p>2. 与公共安全、公共卫生、重大公共利益有关的；</p><p>3. 与犯罪侦查、起诉、审判和判决执行等有关的；</p><p>4. 出于维护个人信息主体或其他个人的生命、财产等重大合法权益但又很难得到本人同意的；</p><p>5. 所收集的个人信息是个人信息主体自行向社会公众公开的；</p><p>6.从合法公开披露的信息中收集的您的个人信息的，如合法的新闻报道、政府信息公开等渠道；</p><p>7.根据您的要求签订合同所必需的；</p><p>8. 用于维护所提供的产品与/或服务的安全稳定运行所必需的，例如发现、处置产品与/或服务的故障；</p><p>9. 为合法的新闻报道所必需的；</p><p>10. 学术研究机构基于公共利益开展统计或学术研究所必要，且对外提供学术研究或描述的结果时，对结果中所包含的个人信息进行去标识化处理的；</p><p><strong>（三） &nbsp;我们从第三方获得您个人信息或者将您的个人信息发送给第三方的情形</strong></p><p>在基于法律允许的情况下，为了给您提供更完备的服务，我们可能从合作的第三方处获得您的相关信息，也可能将您的相关信息发送给第三方。这种涉及与合作的第三方有个人信息交互的。呆可宝机器人都会与合作的第三方有严格的合同约束来保障数据的隐私，包括但不限于对合作的第三方的约束以及数据传输过程的加密。</p><p><strong>（四） &nbsp;非个人信息</strong></p><p>我们还可能收集其他无法识别到特定个人的信息（即不属于个人信息的信息），例如您使用特定服务时产生的统计类数据，如用户的操作行为（包括点击、页面跳转、浏览时间）。收集此类信息的目的在于改善我们向您提供的服务。所收集信息的类别和数量取决手您如何使用我们产品和/或服务。我们会将此类信息汇总，用于帮助我们向客户提供更有用的信息，了解客户对我们服务中的哪些部分最感兴趣。就本隐私政策而言，汇总数据被视为非个人信息。如果我们将非个人信息与个人信息结合使用，则在结合使用期间，此类信息将被视为个人信息。</p><p><strong>二、 &nbsp; &nbsp; &nbsp; &nbsp;保留政策</strong></p><p>我们基于本隐私政策中所述的信息收集的目的所必需的期问，或者遵守适用的相关法律要求保留个人信息。个人信息在完成收集目的，或在我们确认您的删除或注销申请后，或我们终止运营相应产品或服务后，我们将停止保留，并做删除或匿名化处理。如果是出于公众利益、科学、历史研究或统计的目的，我们将基于适用的法律继续保留相关数据，即使进一步的数据处理与原有的收集目的无关。</p><p><strong>三、 &nbsp; &nbsp; &nbsp; &nbsp;您的权利</strong></p><p><strong>（一）控制设置</strong></p><p>我们承认每个人对隐私权的关注各不相同。因此，我们提供了一些示例，说明平台提供的各种方式，供您选择，以限制收集、使用、披露或处理您的个人信息，并控制您的隐私权设置。</p><p>1.在软件服务中点击“我的一设貴-个人资料”修改您的个人信息，包括昵称、性别等（不同产品所涉及到的信息可能会有所差异）；</p><p>2.开启或关闭微信客户端或小程序所要求的授权信息；</p><p>3.登入或登出平台账户。</p><p>如果您之前因为上述目的同意我们使用您的个人信息，您可以随时通过书面或者向<br> deekbot@163.com 发送邮件的方式联系我们来改变您的决定。</p><p><strong>（二）您对您的个人信息享有的权利</strong></p><p>根据您所适用的国家或地区法律法规，您有权要求访问、更正、删除我们持有的与您相关的任何个人信息（以下简称请求）。与您平台帐号中的个人信息相关的更多详细信息，您可以通过登入各软件帐号来访问和更改。其他信息，请致信或者通过以下电子邮箱地址联系我们。电子邮箱：deekbot@163.com。大多数法律要求个人信息主体提出的请求应遵循特定要求，本隐私政策要求您的请求应当符合以下情形：<br> 1.通过我们专门的请求渠道，并且出于保护您的信息安全的考虑，您的请求应当是书面的(除非当地法律明确承认口头申请）；</p><p>2.提供足够的信息使我们可以验证您的身份，确保请求人是所请求信息主体本人或合法授权人；</p><p>3.一旦我们获得充分信息确认可处理您的请求时，我们将在适用数据保护法律规定的时间内对您的请求做出回应。具体而言：</p><p>4.基于您的要求及适用法律规定，我们可免费提供一份我们已收集并处理的关于您的个人信息记录，如您提出对于相关信息的其他请求，我们可能会基于相关适用法律，并结合实际的管理成本向您收取一笔合理的费用。</p><p>5.如果您认为我们持有的关于您的任何信息是不正确或不完整的，可要求基于使用目的更正或完善个人信息。</p><p>6.根据您适用的法律法规，您可能有权要求我们删除您的个人数据。我们将会根据您的删除请求进行评估，若满足相应规定，我们将会采取包括技术手段在内的相应步骤进行处理。当您或我们协助您删除相关信息后，因为适用的法律和安全技术，我们可能无法立即从备份系统中删除相应的信息，我们将安全地存储您的个人信息并将其与任何进一步处理隔离，直到备份可以清除或实现匿名。例如，根据《中华人民共和国电子商务法》，您的商品和服务信息、交易信息保存时问自交易完成之日起不得少于三年。</p><p>7.我们有权拒绝处理无实质意义/纠缠式重复的请求、损害他人隐私权的请求、极端不现实的请求，要求不相称的技术工作，以及根据当地法律无需给予的请求，已经公之于众的信息，保密条件下给出的信息。如果我们认为删除数据或访问数据的请求的某些方面可能会导致我们无法出于前述反欺诈和安全目的合法使用数据，可能也会予以拒绝。</p><p><strong>（三）撤销同意</strong></p><p>1.您可以通过提交请求撤销同意，包括收集、使用和/或披露我们掌握或控制的您的个人信息。根据您所使用的具体服务，可以通过发送邮件到deekbor@163.com 进行相关操作。我们将会在您做出请求后的合理时间内处理您的请求，并且会根据您的请求，在此后不再收集、使用和/或披露您的个人信息。</p><p>2.请注意，您撤销同意会导致某些法律后果。根据您授权我们处理信息的范围，这可能导致您不能享受呆可宝机器人的产品或服务。但您撤回同意或授权的决定，不会影响此前基于您的授权而开展的个人信息处理。</p><p><strong>（四）注销服务或账号</strong></p><p>如您希望注销具体产品或服务，您可以通过发送邮件至 service @fruitech.cn 进行服务注销。如您希望注销平台账号，由于注销平台账号的操作将使您无法使用呆可宝机器人全线产品和服务，请您谨慎操作。我们为了保护您或他人的合法权益会结合您对呆可宝机器人各产品和服务的使用情况判断是否支持您的注销请求。</p><p><strong>四、 &nbsp; &nbsp; &nbsp; &nbsp;第三方网站和服务</strong></p><p>我们的隐私政策不适用于第三方提供的产品或服务。</p><p>取决于您所使用的平台产品或服务，其中可能包括第三方的产品或服务，其中一些会以第三方网站的链接形式提供，还有一些会以SDK、AP等形式接入。当您使用这些产品或服务时，也可能收集您的信息。因此，我们强烈建议您花时间阅读该第三方的隐私政策，就像阅读我们的政策一样。我们不对第三方如何使用他们向您收集的个人信息负责，也不能控制其使用。</p><p><strong>五、 &nbsp; &nbsp; &nbsp; &nbsp;联系我们</strong></p><p>如果您对本隐私政策有任何意见或问题，或者您对我们收集、使用或披露您的个人信息有任何问题，请通过下方地址联系我们，并提及“隐私政策”。针对您关于个人信息相关的权利请求、问题咨询等时，我们有专业的团队解决你的问题。如果你的问题本身涉及比较重大的事项，我们可能会要求你提供更多信息。如果您对收到的答复不满意，您可以将投诉移交给所在司法辖区的相关监管机构。如果您咨询我们，我们会根据您的实际情况，提供可能适用的相关投诉途径的信息。</p><p><strong>六、 &nbsp; &nbsp; &nbsp; &nbsp;其他</strong></p><p>青岛导行蜂智能科技旗下多款软件等皆适用本协议。用户在使用本软件某一特定服务时，该服务可能会另有单独的隐私政策，您在使用该项服务前请阅读并同用户在使用本软件某一特定服务时，该服务可能会另有单独的隐私政策，您在使用该项服务前请阅读并同意相关的单独的隐私政策。</p><p> </p><p><strong>联系地址：</strong></p><p>青岛市城阳区天安数码城 </p><p>邮箱：deekbot@163.com</p><p><strong>青岛导行蜂智能科技有限公司</strong></p>', '<p style=\"text-align: center;\"><strong>呆可宝机器人</strong></p><p style=\"text-align: center;\">用户协议</p><p><strong>【重要须知】</strong></p><p><strong>【青岛导行蜂智能科技有限公司】</strong>（如下简称“导行蜂智能科技”）</p><p>在此特别提醒用户认真阅读、充分理解本《软件许可及服务协议》（下称“本协议”）。用户应认真阅读、充分理解本协议中各条款，特别涉及免除或者限制导行蜂智能科技责任、争议解决和法律适用的条款。免除或者限制责任的条款将以粗体标识，您需要重点阅读。请您审慎阅读并选择接受或不接受本协议（未成年人应在法定监护人陪同下阅读）。您使用本软件以及账号获取和登录等行为将视为对本协议的接受，并同意接受本协议各项条款的约束。</p><p>导行蜂智能科技有权修订本协议，更新后的协议条款将公布于官网或软件，自公布之日起生效。用户可重新使用本软件或网站查阅最新版协议条款。在导行蜂智能科技修改本协议条款后，如果用户不接受修改后的条款，请立即停止使用导行蜂智能科技提供的“呆可宝机器人”软件和服务，用户继续使用导行蜂智能科技提供的“呆可宝机器人”软件和服务将被视为已接受了修改后的协议。</p><p> </p><p><strong>一、总则</strong></p><p>1.1. 本协议是您（如下也称“用户”） 与导行蜂智能科技及其运营合作单位（如下简称“合作单位”）之间关于用户使用导行蜂智能科技的“呆可宝机器人”软件（下称“本软件”）以及使用导行蜂智能科技相关服务所订立的协议。</p><p>1.2.本软件及服务是导行蜂智能科技提供的基于微信平台安装在包括但不限于移动智能终端设备上的软件和服务，为使用该智能终端的用户提供绑定、操作智能产品等服务等。</p><p>1.3.本软件服务基于微信平台开发提供，同意本协议则默认同意《微信小程序平台服务条款》及《微信公众平台服务协议》。</p><p>1.4.本软件及服务的所有权和运营权均归导行蜂智能科技所有。</p><p> </p><p><strong>二、软件授权范围</strong></p><p>2.1. 导行蜂智能科技就本软件给予用户一项个人的、不可转让、不可转授权以及非独占性的许可。</p><p>2.2. 用户可以为非商业目的在单一台移动终端设备上安装、使用、显示、运行本软件。但用户不得为商业运营目的安装、使用、运行本软件，不可以对本软件或者本软件运行过程中释放到任何终端设备内存中的数据及本软件运行过程中客户端与服务器端的交互数据进行复制、更改、修改、挂接运行或创作任何衍生作品，形式包括但不限于使用插件、外挂或非经授权的第三方工具/服务接入本软件和相关系统。如果需要进行商业性的销售、复制和散发，例如软件预装和捆绑，必须获得导行蜂智能科技的书面授权和许可。</p><p>2.3.用户不得未经导行蜂智能科技许可，将本软件安装在未经导行蜂智能科技明示许可的其他终端设备上，包括但不限于机顶盒、游戏机、电视机、DVD机等。</p><p>2.4. 除本《协议》明示授权外，导行蜂智能科技未授权给用户其他权利，若用户使用其他权利时须另外取得导行蜂智能科技的书面同意。</p><p> </p><p><strong>三、软件的获取、安装、升级</strong></p><p>3.1. 用户应当按照导行蜂智能科技的指定平台或指定方式使用本软件产品。谨防在非指定平台使用本软件，以免移动终端设备感染能破坏用户数据和获取用户隐私信息的恶意程序。如果用户从未经导行蜂智能科技授权的第三方获取本软件或与本软件名称相同的安装程序，导行蜂智能科技无法保证该软件能够正常使用，并对因此给您造成的损失不予负责。</p><p>3.2. 用户必须选择与所安装终端设备相匹配的本软件版本，否则，由于软件与设备型号不相匹配所导致的任何软件问题、设备问题或损害，均由用户自行承担。</p><p>3.3. 为了改善用户体验、完善服务内容，导行蜂智能科技有权不时地为您提供本软件替换、修改、升级版本，也有权为替换、修改或升级收取费用，但将收费提前征得您的同意。本软件为用户默认开通“升级提示”功能，视用户使用的软件版本差异，导行蜂智能科技提供给用户自行选择是否需要开通此功能。软件新版本发布后，导行蜂智能科技不保证1日版本软件的继续可用。</p><p> </p><p><strong>四、使用规范</strong></p><p>4.1. 用户在遵守法律及本《协议》的前提下可依本《协议》使用本软件及服务，用户不得实施如下行为：</p><p>4.1.1. 删除本软件及其他副本上一切关于版权的信息，以及修改、删除或避开本软件为保护知识产权而设置的技术措施；</p><p>4.1.2. 对本软件进行反向工程，如反汇编、反编译或者其他试图获得本软件的源代码；<br> 4.1.3. 通过修改或伪造软件运行中的指令、数据，增加、删减、变动软件的功能或运行效果，或者将用于上述用途的软件、方法进行运营或向公众传播，无论这些行为是否为商业目的<br> 4.1.4. 使用本软件进行任何危害网络安全的行为，包括但不限于：使用未经许可的数据或进入未经许可的服务器/账户；未经允许进入公众网络或者他人操作系统并删除、修改、增加存储信息；未经许可企图探查、扫描、测试本软件的系统或网络的弱点或其它实施破坏网络安全的行为；企图干涉、破坏本软件系统或平台的正常运行，故意传播恶意程序或病毒以及其他破坏干扰正常网络信息服务的行为；伪造TCP/IP数据包名称或部分名称；</p><p>4.1.5. 用户通过非导行蜂智能科技公司开发、授权或认可的第三方兼容软件、系统登录或使用本软件及服务，或制作、发布、传播上述工具；</p><p>4.1.6. 未经导行蜂智能科技书面同意，用户对软件及其中的信息擅自实施包括但不限于下列行为：使用、出租、出借、复制、修改、链接、转载、汇编、发表、出版，建立镜像站点、擅自借助本软件发展与之有关的衍生产品、作品、服务、插件、外挂、兼容、互联等;</p><p>4.1.7.利用本软件发表、传送、传播、储存违反当地法律法规的内容；</p><p>4.1.8. 利用本软件发表、传送、传播、储存侵害他人知识产权、商业秘密等合法权利的内容；</p><p>4.1.9.利用本软件批量发表、传送、传播广告信息及垃圾信息；</p><p>4.1.10. 其他以任何不合法的、为任何不合法的目的、或以任何与本协议许可使用不一致的方式使用本软件和导行蜂智能科技提供的其他服务。</p><p>4.2.信息发布规范</p><p>4.2.1. 您可使用本软件发表属于您原创或您有权发表的双点看法、数据、文字、信息、用戸名、图片、照片、个人信息、音频、视频文件、链接等信息内容。您必须保证，您拥有您所上传信息内容的知识产权或已获得合法授权，您使用本软件及服务的任何行为未侵犯任何第三方之合法权益。</p><p>4.2.2.您在使用本软件时需遵守当地法律法规要求。</p><p>4.2.3. 您在使用本软件时不得利用本软件从事以下行为，包括但不限于：</p><p>4.2.3.1.制作、复制、发布、传播、储存违反当地法律法规的内容；</p><p>4.2.3.2.发布、传送、传播、储存侵害他人名誉权、肖像权、知识产权、商业秘密等合法权利的内容；</p><p>4.2.3.3.虛构事实、隐瞒真相以误导、欺骗他人;</p><p>4.2.3.4.发表、传送、传播广告信息及垃圾信息;</p><p>4.2.3.5.从事其他违反当地法律法规的行为。</p><p>4.2.4. 未经导行蜂智能科技许可，您不得在本软件中进行任何诸如发布广告、销售商品的商业行为。</p><p>4.3.您理解并同意：</p><p>4.3.1. 导行蜂智能科技会对用户是否涉嫌违反上述使用规范做出认定，并根据认定结果中止、终止对您的使用许可或采取其他依本约定可采取的限制措施；</p><p>4.3.2.对于用户使用许可软件时发布的涉嫌违法或涉嫌侵犯他人合法权利或违反本协议的信息，导行蜂智能科技会直接删除；</p><p>4.3.3. 对于用户违反上述使用规范的行为对第三方造成损害的，您需要以自己的名义独立承担法律责任，并应确保导行蜂智能科技免于因此产生损失或增加费用；</p><p>4.3.4. 若用户违反有关法律规定或协议约定，使导行蜂智能科技遭受损失，或受到第三方的索赔，或受到行政管理机关的处罚，用户应当赔偿导行蜂智能科技因此造成的损失和（或）发生的费用，包括合理的律师费、调查取证费用。</p><p> </p><p><strong>五、服务风险及免责声明</strong></p><p>5.1.需用户自行配备移动终端设备的必须自行配备移动终端设备上网和使用电信增值业务所需的设备，自行负担个人移动终端设备上网或第三方<br>（包括但不限于电信或移动通信提供商）收取的通讯费、信息费等有关费用。如涉及电信增值服务的，我们建议您与您的电信增值服务提供商确认相关的费用问题。需导行蜂智能科技提供的配备移动终端设备上网和使用电信增值业务所需的设备导行蜂智能科技提供通讯费、信息费。</p><p>5.2.因第三方如通讯线路故障、技术问题、网络、移动终端设备故障、系统不稳定性及其他各种不可抗力原因而遭受的一切损失，导行蜂智能科技及合作单位不承担责任。</p><p>5.3. 本软件同大多数互联网软件一样，受包括但不限于用户原因、网络服务质量、社会环境等因素的差异影响，可能受到各种安全问题的侵扰，如他人利用用户的资料，造成现实生活中的骚扰；用户下载安装的其它软件或访问的其他网站中含有“特洛伊木马”等病毒，威胁到用户的终端设备信息和数据的安全，继而影响本软件的正常使用等等。用户应加强信息安全及使用者资料的保护意识，要注意加强密码保护，以免遭致损失和骚扰。<br> 5.4. 因用户使用本软件或要求导行蜂智能科技提供特定服务时，本软件可能会调用第三方系统或第三方软件支持用户的使用或访问，使用或访问的结果由该第三方提供，导行蜂智能科技不保证通过第三方系统或第三方软件支持实现的结果的安全性、准确性、有效性及其他不确定的风险，由此若引1发的任何争议及损害，导行蜂智能科技不承担任何责任。</p><p>5.5. 导行蜂智能科技特别提请用户注意，导行蜂智能科技为了保障公司业务发展和调整的自主权，导行蜂智能科技公司拥有随时修改或中断服务而不需通知用户的权利，导行蜂智能科技行使修改或中断服务的权利不需对用户或任何第三方负责。</p><p>5.6. 除法律法规有明确规定外，我们将尽最大努力确保软件及其所涉及的技术及信息安全、有效、准确、可靠，但受限于现有技术，用户理解导行蜂智能科技不能对此进行担保。</p><p>5.7. 由于用户因下述任一情况所引起或与此有关的人身伤害或附带的、间接的经济损害赔偿，包括但不限手利润损失、资料损失、业务中断的损害赔偿或其他商业损害赔偿或损失，需由用户自行承担：</p><p>5.7.1.使用或未能使用许可软件；</p><p>5.7.2.第三方未经许可的使用软件或更改用户的数据；</p><p>5.7.3.用户使用软件进行的行为产生的费用及损失；</p><p>5.7.4.用户对软件的误解；</p><p>5.7.5.非因导行蜂智能科技的原因引起的与软件有关的其他损失。</p><p>5.8. 用户与其他使用软件的用户之间通过软件进行的行为，因您受误导或欺骗而导致或可能导致的任何人身或经济上的伤害或损失，均由过错方依法承担所有责任。</p><p> </p><p><strong>六、服务费用</strong></p><p>6.1. 本服务的任何免费试用或免费功能和服务不应视为导行蜂智能科技放弃后续收费的权利。导行蜂智能科技有权在不以任何形式提前通知的情况下，根据平台业务运营需要对收费标准进行调整，若您继续使用则需按导行蜂智能科技公布的收费标准支付费用。</p><p>6.2.所有费用需通过导行蜂智能科技接受的支付方式事先支付。前述使用费不包含其它任何费用或相关汇款等支出，否则您应补足付款或自行支付该费用。</p><p>6.3. 导行蜂智能科技有权根据实际情况单方调整费用标准及收费方式，并可自行选择是否以公告形式提前通知您，但并不需要获得您的事先同意，如您不同意收费应当立即停止服务的使用，否则使用则视为您已同意并应当缴纳费用。</p><p>6.4. 您应当自行支付使用本服务可能产生的上网费以及其他第三方收取的通讯费、信息费等。<br> 6.5. 您理解并同意，在使用本软件中的服务需要进行语音/视频服务时，导行蜂智能科技方需通过电信运营商提供的网络提供服务，运营商在某些情况下可能向用户收取一定通信费用<br>（如用户处于异地漫游状态时需支付漫游接听费用）。</p><p>6.6. 您理解并知悉，您在使用平台部分服务时，包括音频、视频等服务时，需要消耗您的流量，导行蜂智能科技会对此作出提示，您继续使用服务视为您对消耗流量的同意。</p><p> </p><p><strong>七、第三方内容/服务说明</strong></p><p>7.1. 用户理解并同意，本软件可能包含由导行蜂科技的关联方或第三方提供的内容或服务，导行蜂科技只是为了用户便利操作而在本软件中提供相关功能模块，提供第三方内容或服务的使用入口。</p><p>7.2.不论第三方内容或服务预置于本软件中，还是由用户自行开通或订购，用户均理解并同意，导行蜂科技不对第三方内容或服务提供方或用户行为的合法性、有效性，以及第三方内容或服务的合法性、准确性、有效性、安全性进行任何明示或默示的保证或担保。</p><p>7.3. 导行蜂科技并不监督第三方内容或服务，不对其拥有任何控制权，也不对第三方服务提供任何形式的保证或担保，更不承担任何责任。</p><p>7.4. 用户与第三方内容或服务提供方之间发生的任何争议、纠纷应由用户与第三方服务提供方自行协商解决，导行蜂科技不承担任何责任。</p><p> </p><p><strong>八、知识产杈声明</strong></p><p>8.1. 导行蜂科技科技是本软件的知识产权权利人。本软件的一切著作权、商标权、专利权、商业秘密等知识产权，以及与本软件相关的所有信息内容（包括但不限于文字、图片、音频、视频、图表、界面设计、版面框架、有关数据或电子文档等）均受您所在当地法律法规和相应的国际条约保护，导行蜂科技科技享有上述知识产权。</p><p>8.2 未经导行蜂科技科技书面同意，用户不得为任何商业或非商业目的自行或许可任何第三方实施、利用、转让上述知识产权，导行蜂科技科技保留追究上述行为法律责任的权利。</p><p>8.3 导行蜂科技科技旗下所有软件皆适用本协议。</p><p> </p><p><strong>九、协议变更</strong></p><p>9.1. 导行蜂科技科技有权在必要时修改本协议条款，协议条款一旦发生变动，将会在相关页面上公布修改后的协议条款。如果不同意所改动的内容，用户应主动取消此项服务。如果用户继续使用服务，则视为接受协议条款的变动。</p><p>9.2. 导行蜂科技科技和合作公司有权按需要修改或变更所提供的收费服务、收费标准、收费方式、服务费及服务条款。导行蜂科技科技在提供服务时，可能现在或日后对部分服务的用户开始收取一定的费用如用户拒绝支付该等费用，则不能在收费开始后继续使用相关的服务。导行蜂科技科技和合作公司将尽最大努力通过电邮或其他方式通知用户有关的修改或变更。</p><p> </p><p><strong>十、适用法律及争议解决</strong></p><p>10.1. 本协议条款之效力和解释均适用中华人民共和国大陆地区的法律。如无相关法律规定的，则参照使用国际商业惯例和/或商业惯例。</p><p>10.2.本协议的签订地是青岛市城阳区。</p><p>10.3.用户和导行蜂科技科技一致同意凡因本服务所产生的纠纷双方应协商解决，协商不成任何一方可提交本协议签订地有管辖权的法院诉讼解决。</p><p> </p><p><strong>十一、其他</strong></p><p>11.1. 用户在使用本软件某一特定服务时，该服务可能会另有单独的协议、相关业务规则等（以下统称为“单独协议”），您在使用该项服务前请阅读并同意相关的单独协议。</p><p>11.2 本协议生效时问为2023年12月15日。</p><p>11.3 本协议所有条款的标题仅为阅读方便，本身并无实际涵义，不能作为本协议涵义解释的依据。</p><p>11.4本协议条款无论因何种原因部分无效或不可执行，其余条款仍有效，对双方具有约束力。</p><p> </p><p><strong>导行蜂科技科技</strong></p>', 1702878322);

--
-- 转储表的索引
--

--
-- 表的索引 `accept_log`
--
ALTER TABLE `accept_log`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `md_action_log`
--
ALTER TABLE `md_action_log`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `mid` (`mid`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE;

--
-- 表的索引 `md_device`
--
ALTER TABLE `md_device`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `client` (`client`) USING BTREE;

--
-- 表的索引 `md_device_bind`
--
ALTER TABLE `md_device_bind`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `device_id` (`device_id`) USING BTREE;

--
-- 表的索引 `md_device_data`
--
ALTER TABLE `md_device_data`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `device_id` (`device_id`) USING BTREE;

--
-- 表的索引 `md_device_error`
--
ALTER TABLE `md_device_error`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `device_id` (`device_id`) USING BTREE;

--
-- 表的索引 `md_device_heart_log`
--
ALTER TABLE `md_device_heart_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `md_device_report`
--
ALTER TABLE `md_device_report`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `device_id` (`device_id`) USING BTREE;

--
-- 表的索引 `md_device_set`
--
ALTER TABLE `md_device_set`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `device_id` (`device_id`) USING BTREE;

--
-- 表的索引 `md_device_setting`
--
ALTER TABLE `md_device_setting`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `device_id` (`device_id`) USING BTREE;

--
-- 表的索引 `md_fankui`
--
ALTER TABLE `md_fankui`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `md_goods`
--
ALTER TABLE `md_goods`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `md_head_bind`
--
ALTER TABLE `md_head_bind`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `md_manage`
--
ALTER TABLE `md_manage`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `account` (`account`) USING BTREE;

--
-- 表的索引 `md_new_device_data`
--
ALTER TABLE `md_new_device_data`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `md_new_device_set`
--
ALTER TABLE `md_new_device_set`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_id` (`device_id`);

--
-- 表的索引 `md_new_device_setting`
--
ALTER TABLE `md_new_device_setting`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `md_order`
--
ALTER TABLE `md_order`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `sn` (`sn`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE;

--
-- 表的索引 `md_recharge`
--
ALTER TABLE `md_recharge`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `md_recharge_order`
--
ALTER TABLE `md_recharge_order`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `sn` (`sn`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE;

--
-- 表的索引 `md_sys_menu`
--
ALTER TABLE `md_sys_menu`
  ADD PRIMARY KEY (`menuId`) USING BTREE;

--
-- 表的索引 `md_sys_role`
--
ALTER TABLE `md_sys_role`
  ADD PRIMARY KEY (`role_id`) USING BTREE;

--
-- 表的索引 `md_tearcher`
--
ALTER TABLE `md_tearcher`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `md_tearcher_bind`
--
ALTER TABLE `md_tearcher_bind`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `md_tearcher_cate`
--
ALTER TABLE `md_tearcher_cate`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `md_user`
--
ALTER TABLE `md_user`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `mobile` (`mobile`) USING BTREE,
  ADD KEY `username` (`username`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE;

--
-- 表的索引 `md_version`
--
ALTER TABLE `md_version`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `md_video`
--
ALTER TABLE `md_video`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `md_website`
--
ALTER TABLE `md_website`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `accept_log`
--
ALTER TABLE `accept_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `md_action_log`
--
ALTER TABLE `md_action_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '唯一性标识', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `md_device`
--
ALTER TABLE `md_device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_device_bind`
--
ALTER TABLE `md_device_bind`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_device_data`
--
ALTER TABLE `md_device_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_device_error`
--
ALTER TABLE `md_device_error`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_device_heart_log`
--
ALTER TABLE `md_device_heart_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_device_report`
--
ALTER TABLE `md_device_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_device_set`
--
ALTER TABLE `md_device_set`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_device_setting`
--
ALTER TABLE `md_device_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_fankui`
--
ALTER TABLE `md_fankui`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_goods`
--
ALTER TABLE `md_goods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_head_bind`
--
ALTER TABLE `md_head_bind`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_manage`
--
ALTER TABLE `md_manage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `md_new_device_data`
--
ALTER TABLE `md_new_device_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_new_device_set`
--
ALTER TABLE `md_new_device_set`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_new_device_setting`
--
ALTER TABLE `md_new_device_setting`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_order`
--
ALTER TABLE `md_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单id';

--
-- 使用表AUTO_INCREMENT `md_recharge`
--
ALTER TABLE `md_recharge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id';

--
-- 使用表AUTO_INCREMENT `md_recharge_order`
--
ALTER TABLE `md_recharge_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id';

--
-- 使用表AUTO_INCREMENT `md_sys_menu`
--
ALTER TABLE `md_sys_menu`
  MODIFY `menuId` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 使用表AUTO_INCREMENT `md_sys_role`
--
ALTER TABLE `md_sys_role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色ID', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `md_tearcher`
--
ALTER TABLE `md_tearcher`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_tearcher_bind`
--
ALTER TABLE `md_tearcher_bind`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_tearcher_cate`
--
ALTER TABLE `md_tearcher_cate`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_user`
--
ALTER TABLE `md_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_version`
--
ALTER TABLE `md_version`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_video`
--
ALTER TABLE `md_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `md_website`
--
ALTER TABLE `md_website`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
