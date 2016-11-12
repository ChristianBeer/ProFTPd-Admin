CREATE TABLE `groups` (
  `groupname` VARCHAR(32) UNIQUE NOT NULL default '',
  `gid` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  `members` VARCHAR(255) NOT NULL default ''
);
CREATE UNIQUE INDEX `groupname` ON groups (`groupname`);

CREATE TABLE `users` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  `userid` VARCHAR(32) UNIQUE NOT NULL default '',
  `uid` UNSIGNED SMALLINT(6) default NULL,
  `gid` UNSIGNED SMALLINT(6) default NULL,
  `passwd` VARCHAR(265) NOT NULL default '',
  `homedir` VARCHAR(255) NOT NULL default '',
  `comment` VARCHAR(255) NOT NULL default '',
  `disabled` UNSIGNED SMALLINT(2) NOT NULL default '0',
  `shell` VARCHAR(32) NOT NULL default '/bin/false',
  `email` VARCHAR(255) NOT NULL default '',
  `name` VARCHAR(255) NOT NULL default '',
  `title` VARCHAR(5) NOT NULL default '',
  `company` VARCHAR(255) NOT NULL default '',
  `bytes_in_used` UNSIGNED BIGINT(20) NOT NULL default '0',
  `bytes_out_used` UNSIGNED BIGINT(20) NOT NULL default '0',
  `files_in_used` UNSIGNED BIGINT(20) NOT NULL default '0',
  `files_out_used` UNSIGNED BIGINT(20) NOT NULL default '0',
  `login_count` UNSIGNED INT(11) NOT NULL default '0',
  `last_login` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `last_modified` DATETIME NOT NULL default '0000-00-00 00:00:00'
);
CREATE UNIQUE INDEX `userid` ON users (`userid`);
