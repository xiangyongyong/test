-- 2017/4/11 下午12:22 by 李刚
ALTER TABLE `tab_gateway` ADD `location` VARCHAR(64) NULL DEFAULT NULL COMMENT '高德经纬度' AFTER `group_id`, ADD `is_correct` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '位置是否经过校正' AFTER `location`;

ALTER TABLE `tab_gateway` ADD `address` VARCHAR(255) NULL DEFAULT NULL COMMENT '地理位置' AFTER `is_correct`;
ALTER TABLE `tab_gateway` ADD `pole` VARCHAR(64) NULL DEFAULT NULL COMMENT '电线杆编号' AFTER `address`;

ALTER TABLE `tab_gateway` ADD `longitude` DOUBLE(9,6) NOT NULL COMMENT '经度' AFTER `location`, ADD `latitude` DOUBLE(9,6) NOT NULL COMMENT '纬度' AFTER `longitude`;

-- 2017/4/19 下午4:15 by ligang
ALTER TABLE `tab_gateway` CHANGE `gateway_desc` `gateway_desc` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述';

ALTER TABLE `tab_gateway` ADD `state` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '网关状态；0正常 1维护 2异常' AFTER `ip`;
ALTER TABLE `tab_gateway` ADD INDEX(`state`);

ALTER TABLE `tab_gateway` ADD `data_update_at` INT NOT NULL DEFAULT '0' COMMENT '报文更新时间；' AFTER `pole`, ADD INDEX (`data_update_at`);