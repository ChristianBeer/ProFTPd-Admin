<?php

/*
 *****************************************************************************
 * ProFTPd Admin                                                             *
 *                                                                           *
 * (C) 2004 The Netherlands, Lex Brugman <lex_brugman@users.sourceforge.net> *
 * See LICENSE for the License                                               *
 *****************************************************************************
*/

$cfg->tpbgcolor = "#0099FF";
$cfg->dwbgcolor1 = "#FFFFFF";
$cfg->dwbgcolor2 = "#FFFF99";

$cfg->table_users = "ftpuser";
$cfg->field_userid = "userid";
$cfg->field_id = "id";
$cfg->field_uid = "uid";
$cfg->field_gid = "gid";
$cfg->field_passwd= "passwd";
$cfg->field_disabled = "disabled";
$cfg->field_expires = "expires";
$cfg->field_homedir = "homedir";
$cfg->field_name = "name";
$cfg->field_email = "email";
$cfg->field_comment = "comment";
$cfg->field_shell = "shell";
$cfg->field_login_count = "count";
$cfg->field_last_login = "accessed";

$cfg->table_groups = "ftpgroup";
$cfg->field_groupname = "groupname";
$cfg->field_members = "members";

$cfg->table_xfer_stats = "ftpuser";
$cfg->field_bytes_in_used = "bytes_in_used";
$cfg->field_bytes_out_used = "bytes_out_used";
$cfg->field_files_in_used = "files_in_used";
$cfg->field_files_out_used = "files_out_used";

$cfg->default_uid = "1004";
$cfg->default_homedir = "/srv/ftp/MMA";

// Edit from here:
$cfg->db_host = "localhost";
$cfg->db_name = "proftpd";
$cfg->db_user = "proftpd";
$cfg->db_pass = "PuW47eNzV4uXDBaT";

?>
