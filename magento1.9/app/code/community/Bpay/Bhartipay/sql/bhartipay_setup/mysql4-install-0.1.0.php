<?php
//script to create new table
$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS `bhartipay`;
CREATE TABLE `bhartipay` (
  `bhartipay_id` bigint(20) unsigned NOT NULL auto_increment,
  `cust_id` varchar(100),
  PRIMARY KEY (`bhartipay_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"
);
$installer->endSetup();
