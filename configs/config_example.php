<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 */

$cfg = array();
$cfg['tpbgcolor'] = "#0099FF";
$cfg['dwbgcolor1'] = "#FFFFFF";
$cfg['dwbgcolor2'] = "#FFFF99";

$cfg['table_users'] = "users";
$cfg['field_userid'] = "userid";
$cfg['field_id'] = "id";
$cfg['field_uid'] = "uid";
$cfg['field_gid'] = "gid";
$cfg['field_passwd'] = "passwd";
$cfg['field_disabled'] = "disabled";
$cfg['field_expires'] = "expires";
$cfg['field_homedir'] = "homedir";
$cfg['field_name'] = "name";
$cfg['field_email'] = "email";
$cfg['field_comment'] = "comment";
$cfg['field_shell'] = "shell";
$cfg['field_login_count'] = "login_count";
$cfg['field_last_login'] = "last_login";
$cfg['field_last_modified'] = "last_modified";

$cfg['table_groups'] = "groups";
$cfg['field_groupname'] = "groupname";
$cfg['field_members'] = "members";

$cfg['table_xfer_stats'] = "users";
$cfg['field_bytes_in_used'] = "bytes_in_used";
$cfg['field_bytes_out_used'] = "bytes_out_used";
$cfg['field_files_in_used'] = "files_in_used";
$cfg['field_files_out_used'] = "files_out_used";

$cfg['default_uid'] = ""; //if empty next incremental will be default
$cfg['default_homedir'] = "/srv/ftp";
$cfg['passwd_encryption'] = "MD5"; // either SHA1 or MD5 or any other supported by your MySQL-Server and ProFTPd
$cfg['min_passwd_length'] = "6";
$cfg['userid_regex']    = "/^([a-z][a-z0-9_\-]{0,20})$/i"; //every username must comply with this regex
$cfg['groupname_regex'] = "/^([a-z][a-z0-9_\-]{0,20})$/i"; //every username must comply with this regex

// next option activates a userid filter on users.php. Usefull if you want to manage a lot of users
// that have a prefix like "pre-username", the first occurence of separator is recognized only!
$cfg['userid_filter_separator'] = ""; // try "-" or "_" as separators

$cfg['db_host'] = "localhost";
$cfg['db_name'] = "database";
$cfg['db_user'] = "user";
$cfg['db_pass'] = "password";
?>
