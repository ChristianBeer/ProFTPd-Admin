<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 *
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);
echo $ac->get_header();

$usrcnt_total = $ac->get_user_count();
$usrcnt_disabled = $ac->get_user_count(true);
$grpcnt_total = $ac->get_group_count();
$grpcnt_empty = $ac->get_group_count(true);

print("Number of users in database: <b>" . $usrcnt_total . "</b><br />".
      "Number of groups in database: <b>" . $grpcnt_total . "</b><br /><br />".
      "Number of deactivated users: <b>" . $usrcnt_disabled . "</b><br />".
      "Number of empty groups: <b>" . $grpcnt_empty . "</b><br />");

echo $ac->get_footer();
?>