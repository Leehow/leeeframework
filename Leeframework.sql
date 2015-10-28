--
-- 数据库: `Leeframework`
--

-- --------------------------------------------------------

--
-- 表的结构 `data`
-- 这个是主要数据存储,所有数据都存在这里面

CREATE TABLE IF NOT EXISTS `data` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `data_upid` int(11) DEFAULT NULL,
  `data_author` int(11) NOT NULL,
  `data_kind` varchar(20) NOT NULL DEFAULT '0',
  `data_content` text NOT NULL,
  `data_time` int(11) NOT NULL COMMENT '最后查看时间',
  `data_createtime` int(11) NOT NULL COMMENT '创建时间（不可改变）',
  `data_changetime` int(11) NOT NULL COMMENT '最近修改的时间',
  PRIMARY KEY (`data_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=468 ;

-- --------------------------------------------------------

--
-- 表的结构 `data_collect`
-- 这里是信息搜集数据库,将用户点击按钮及用户习惯搜集到这里

CREATE TABLE IF NOT EXISTS `data_collect` (
  `dc_id` int(11) NOT NULL AUTO_INCREMENT,
  `dc_cid` int(11) DEFAULT NULL,
  `dc_author` int(11) DEFAULT '0',
  `dc_time` int(11) DEFAULT NULL,
  `dc_kind` int(11) DEFAULT NULL,
  `dc_ckind` int(11) DEFAULT NULL,
  `dc_ext` text,
  PRIMARY KEY (`dc_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=408 ;

-- --------------------------------------------------------

--
-- 表的结构 `log`
-- 一些特殊信息到搜集,包括数据库错误等等

CREATE TABLE IF NOT EXISTS `log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_content` text NOT NULL,
  `log_author` int(11) DEFAULT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `usr`
-- 用户表,只存储用户名密码和用户id

CREATE TABLE IF NOT EXISTS `usr` (
  `usr_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`usr_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=425 ;

