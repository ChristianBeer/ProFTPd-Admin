#
# Table structure for table `groups`
#

CREATE TABLE `groups` (
  `groupname` varchar(20) NOT NULL default '',
  `gid` int(10) unsigned NOT NULL auto_increment,
  `members` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`gid`)
) TYPE=InnoDB ;

#
# Table structure for table `users`
#

CREATE TABLE `users` (
  `id` smallint(2) NOT NULL auto_increment,
  `userid` varchar(20) NOT NULL default '',
  `uid` int(10) unsigned NOT NULL default '',
  `gid` int(10) unsigned NOT NULL default '',
  `passwd` varchar(255) NOT NULL default '',
  `homedir` varchar(255) NOT NULL default '',
  `comment` varchar(255) NOT NULL default '',
  `disabled` int(10) unsigned NOT NULL default '0',
  `shell` varchar(20) NOT NULL default '/sbin/nologin',
  `email` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `bytes_in_used` bigint(20) NOT NULL default '0',
  `bytes_out_used` bigint(20) NOT NULL default '0',
  `login_count` bigint(20) NOT NULL default '0',
  `files_in_used` bigint(20) NOT NULL default '0',
  `files_out_used` bigint(20) NOT NULL default '0',
  `last_login` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB ;
