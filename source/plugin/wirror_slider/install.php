<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: install.php 8889 2010-04-23 07:48:22Z monkey $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$tablepre = $_G['config']['db'][1]['tablepre'];

$sql = <<<EOF

DROP TABLE IF EXISTS {$tablepre}wirror_slider_pics;
CREATE TABLE {$tablepre}wirror_slider_pics (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `description` text,
  `isupload` tinyint(4) NOT NULL default '1',
  `pic` varchar(200) NOT NULL,
  `link` varchar(200) NOT NULL default '#',
  `seq` int(11) NOT NULL default '0',
  `enabled` tinyint(4) NOT NULL default '1',
  `deleted` tinyint(4) NOT NULL default '0',
  `operator` varchar(50) NOT NULL,
  `inserttime` datetime NOT NULL default '0000-00-00 00:00:00',
  `updatetime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET={CHARSET};

EOF;

runquery($sql);

$finish = TRUE;

?>