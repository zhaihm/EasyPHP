/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50159
 Source Host           : 127.0.0.1
 Source Database       : ep_user

 Target Server Type    : MySQL
 Target Server Version : 50159
 File Encoding         : utf-8

 Date: 11/09/2015 17:12:24 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `tb_account`
-- ----------------------------
DROP TABLE IF EXISTS `tb_account`;
CREATE TABLE `tb_account` (
  `userid` bigint(20) NOT NULL,
  `account` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `tb_account`
-- ----------------------------
BEGIN;
INSERT INTO `tb_account` VALUES ('0', 'test', '49ba59abbe56e057', '1', '2015-11-09 16:00:47');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
