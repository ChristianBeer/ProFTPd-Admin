<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Ricardo Padilha <ricardo@droboports.com>
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 * @copyright Robert Tulke <rt@debian.sh>
 * @copyright Michael Keck <https://github.com/mkkeck>
 *
 * rename to config.php
 */

$cfg = array();


/**
 * Login data
 *
 * Important: Please change this values in
 *            live systems!
 */
$cfg['login'] = array(
  /* Username. Please use any username you want */
  'username' => 'admin',
  /* Password. CHANGE IT and use secure password! */
  'password' => 'password',
  /* Blowfish secret key (22 chars). CHANGE IT! */
  'blowfish' => 'XBu5pjOTa8H7UIwYSzMZxD'
);

/**
 * Force SSL usage
 *
 * Important: You should change this to true on live systems or configure
 *            your webserver to use SSL!
 */
$cfg['force_ssl'] = false;

$cfg['table_users'] = "users";
$cfg['field_userid'] = "userid";
$cfg['field_id'] = "id";
$cfg['field_uid'] = "uid";
$cfg['field_ugid'] = "gid";
$cfg['field_passwd'] = "passwd";
$cfg['field_homedir'] = "homedir";
$cfg['field_shell'] = "shell";
$cfg['field_title'] = "title";
$cfg['field_name'] = "name";
$cfg['field_company'] = "company";
$cfg['field_email'] = "email";
$cfg['field_comment'] = "comment";
$cfg['field_disabled'] = "disabled";
$cfg['field_login_count'] = "login_count";
$cfg['field_last_login'] = "last_login";
$cfg['field_last_modified'] = "last_modified";
$cfg['field_bytes_in_used'] = "bytes_in_used";
$cfg['field_bytes_out_used'] = "bytes_out_used";
$cfg['field_files_in_used'] = "files_in_used";
$cfg['field_files_out_used'] = "files_out_used";

$cfg['table_groups'] = "groups";
$cfg['field_groupname'] = "groupname";
$cfg['field_gid'] = "gid";
$cfg['field_members'] = "members";

$cfg['default_uid'] = "8000"; //if empty next incremental will be default
$cfg['default_homedir'] = "/home/web";
// Use either SHA1 or MD5 or any other supported by your MySQL-Server and ProFTPd
// "pbkdf2" is supported if you are using ProFTPd 1.3.5.
// "crypt" uses the unix crypt() function.
// "OpenSSL:sha1" other digest-names also possible; see: http://www.proftpd.org/docs/directives/configuration_full.html#SQLAUTHTYPES
$cfg['passwd_encryption'] = "crypt";
$cfg['min_passwd_length'] = "4";
$cfg['max_userid_length'] = "32";
$cfg['max_groupname_length'] = "32";
// the expressions used to validate user and groupnames are used in two places
// on the website (HTML5) and on the server (PHP)
// the HTML5 validation doesn't understand the i modifier so you need to specify lowercase and uppercase characters
// for some reason the PHP validation still needs the i modifier so just leave it in
$cfg['userid_regex']    = "/^([a-zA-Z][a-zA-Z0-9_\-]{0,".($cfg['max_userid_length']-1)."})$/i"; //every username must comply with this regex
$cfg['groupname_regex'] = "/^([a-zA-Z][a-zA-Z0-9_\-]{0,".($cfg['max_groupname_length']-1)."})$/i"; //every username must comply with this regex
// Set any of these to -1 to remove the constraint
$cfg['min_uid'] = 8000;
$cfg['max_uid'] = 8999;
$cfg['min_gid'] = 8000;
$cfg['max_gid'] = 8999;
// Uncomment this to read crypt() settings from login.defs.
// $cfg['read_login_defs'] = true;

// next option activates a userid filter on users.php. Usefull if you want to manage a lot of users
// that have a prefix like "pre-username", the first occurence of separator is recognized only!
$cfg['userid_filter_separator'] = ""; // try "-" or "_" as separators

// use this block for a mysql backend
$cfg['db_type'] = "mysqli"; // if unset, 'db_type' defaults to mysqli
$cfg['db_host'] = "localhost";
$cfg['db_name'] = "proftpd";
$cfg['db_user'] = "proftpd";
$cfg['db_pass'] = "yourdbpasswordhere";

// use this block for an sqlite3 backend
//$cfg['db_type'] = "sqlite3";
//$cfg['db_path'] = "configs/";
//$cfg['db_name'] = "auth.sqlite3";
