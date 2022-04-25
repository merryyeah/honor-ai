/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : honor_ai

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2022-04-14 22:20:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for auto_check
-- ----------------------------
DROP TABLE IF EXISTS `auto_check`;
CREATE TABLE `auto_check` (
  `auto_check_id` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `begin` varchar(255) DEFAULT NULL,
  `end` varchar(255) DEFAULT NULL,
  `times` int(11) DEFAULT NULL,
  `camera_list` varchar(255) DEFAULT NULL,
  `gmt_exec` datetime DEFAULT NULL,
  `gmt_create` datetime DEFAULT NULL,
  `gmt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`auto_check_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auto_check
-- ----------------------------
INSERT INTO `auto_check` VALUES ('1', null, 'open', '10:11', '12:11', '3', '1', null, '2022-04-14 22:06:08', '2022-04-14 22:06:23');

-- ----------------------------
-- Table structure for camera
-- ----------------------------
DROP TABLE IF EXISTS `camera`;
CREATE TABLE `camera` (
  `camera_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `camera_name` varchar(100) DEFAULT NULL,
  `ip` varchar(32) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `coordinate` varchar(255) DEFAULT NULL,
  `opeator` int(11) DEFAULT NULL,
  `gmt_create` datetime DEFAULT NULL,
  `gmt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`camera_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of camera
-- ----------------------------
INSERT INTO `camera` VALUES ('1', '1', '1', '1', '127.0.0.2', '1', '1', '1', null, '2022-04-11 19:24:29', '2022-04-11 19:24:33');
INSERT INTO `camera` VALUES ('2', '1', '1', 'testCam', '127.0.0.1', 'test', '123456', '132,111', null, '2022-04-13 20:12:47', '2022-04-13 20:12:47');

-- ----------------------------
-- Table structure for dept
-- ----------------------------
DROP TABLE IF EXISTS `dept`;
CREATE TABLE `dept` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_name` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `operator` int(11) DEFAULT NULL,
  `gmt_create` datetime DEFAULT NULL,
  `gmt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`dept_id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dept
-- ----------------------------
INSERT INTO `dept` VALUES ('1', '1test', '0', '1', null, '2022-04-13 20:25:10');
INSERT INTO `dept` VALUES ('2', '测试部门1子部门', '1', null, null, null);
INSERT INTO `dept` VALUES ('3', 'test33', '1', '1', '2022-04-14 19:59:36', '2022-04-14 19:59:36');

-- ----------------------------
-- Table structure for detection_log
-- ----------------------------
DROP TABLE IF EXISTS `detection_log`;
CREATE TABLE `detection_log` (
  `detection_log_id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `addr` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `result` varchar(255) DEFAULT NULL,
  `score` varchar(255) DEFAULT NULL,
  `camera_ip` varchar(255) DEFAULT NULL,
  `gmt_detection` datetime DEFAULT NULL,
  `gmt_create` datetime DEFAULT NULL,
  `gmt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`detection_log_id`),
  KEY `idx_action` (`action`),
  KEY `idx_gmt_detection` (`gmt_detection`),
  KEY `idx_target_user_id` (`target_user_id`),
  KEY `idx_addr` (`addr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of detection_log
-- ----------------------------
INSERT INTO `detection_log` VALUES ('1', 'NG', '抽烟', '在办公室', 'http://127.0.0.1/image_url.jpg', 'http://127.0.0.1/video_url.mp3', null, null, null, '127.0.0.1', '2022-04-14 21:35:58', '2022-04-14 21:35:58', '2022-04-14 21:35:58');
INSERT INTO `detection_log` VALUES ('2', 'NG', '喝酒', '在路边', 'http://127.0.0.1/image_url.jpg', 'http://127.0.0.1/video_url.mp3', null, null, null, '127.0.0.1', '2022-04-14 21:35:58', '2022-04-14 21:35:58', '2022-04-14 21:35:58');
INSERT INTO `detection_log` VALUES ('3', 'NG', '打游戏', '在办公室', 'http://127.0.0.1/image_url.jpg', 'http://127.0.0.1/video_url.mp3', null, null, null, '127.0.0.1', '2022-04-14 21:35:58', '2022-04-14 21:35:58', '2022-04-14 21:35:58');
INSERT INTO `detection_log` VALUES ('4', 'NG', '不在工位', '在街道口', 'http://127.0.0.1/image_url.jpg', 'http://127.0.0.1/video_url.mp3', null, null, null, '127.0.0.1', '2022-04-14 21:35:58', '2022-04-14 21:35:58', '2022-04-14 21:35:58');
INSERT INTO `detection_log` VALUES ('5', 'NG', '聊天', '在办公室', 'http://127.0.0.1/image_url.jpg', 'http://127.0.0.1/video_url.mp3', null, null, null, '127.0.0.1', '2022-04-14 21:35:58', '2022-04-14 21:35:58', '2022-04-14 21:35:58');
INSERT INTO `detection_log` VALUES ('6', 'OK', '工作', '在办公室', 'http://127.0.0.1/image_url.jpg', 'http://127.0.0.1/video_url.mp3', null, null, null, '127.0.0.1', '2022-04-14 21:35:58', '2022-04-14 21:35:58', '2022-04-14 21:35:58');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gmt_create` datetime DEFAULT NULL,
  `gmt_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `idx_name_password` (`name`,`password`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '3', 'test', '123456', '15806026996', '2022-04-09 18:46:47', '2022-04-14 20:00:06');
INSERT INTO `user` VALUES ('2', '1', 'testCam', '127.0.0.1', '12315151212', '2022-04-13 20:32:14', '2022-04-13 20:32:14');
