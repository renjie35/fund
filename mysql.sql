CREATE DATABASE local;

USE local;

CREATE TABLE `basic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cash` float(8,0) unsigned NOT NULL DEFAULT '0' COMMENT '税前工资',
  `percent` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '住房公积金缴纳比例',
  `type` tinyint(4) unsigned NOT NULL COMMENT '类型',
  `industry` tinyint(4) unsigned NOT NULL COMMENT '单位行业',
  `func` tinyint(4) unsigned NOT NULL COMMENT '职能',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `ip` char(20) DEFAULT NULL COMMENT 'ip',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;