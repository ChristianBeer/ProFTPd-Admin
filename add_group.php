<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 *
 * @todo Change tables to divs and declare colors in style.css
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);
echo $ac->get_header();

if (isset($_REQUEST["new_group_name"]) & isset($_REQUEST["new_group_gid"])) {

    if (!preg_match($cfg['groupname_regex'], $_REQUEST["new_group_name"])) {
        print ("Bad group name, please try again.<br />");
        echo $ac->get_footer();
        die;
    }
    if (!is_numeric($_REQUEST["new_group_gid"])) {
        print ("Bad GID, please try again.<br />");
        echo $ac->get_footer();
        die;
    }

    $groups = $ac->get_groups();
    if (count($groups) > 0) {
        while (list($gid, $name) = each($groups)) {
            if ($name == $_REQUEST["new_group_name"]) {
                print("Duplicate group name, please try again.<br />");
                echo $ac->get_footer();
                die;
            }
            if ($gid == $_REQUEST["new_group_gid"]) {
                print("Duplicate GID, please try again.<br />");
                echo $ac->get_footer();
                die;
            }
        }
    }

    $groupdata = array("new_group_name" => $_REQUEST["new_group_name"], "new_group_gid" => $_REQUEST["new_group_gid"], "new_group_members" => '');
    if ($ac->add_group($groupdata)) {
        print("Created new group: <b>" . $_REQUEST["new_group_name"] . "</b><br />");
    }
}
print("<table><form method=\"post\">" .
      "<tr><td colspan=\"2\"></td></tr>" .
      "<tr bgcolor=\"" . $cfg['tpbgcolor'] . "\"><td><b>Label</b></td><td><b>Input</b></td></tr>" .
      "<tr bgcolor=\"" . $cfg['dwbgcolor1'] . "\"><td>Group name *:</td><td>" .
      "<input type=\"hidden\" size=\"10\" name=\"action\" value=\"newgroup\">" .
      "<input type=\"text\" size=\"20\" name=\"new_group_name\">" .
      "<tr bgcolor=\"" . $cfg['dwbgcolor2'] . "\"><td>GID *:</td><td>" .
      "<input type=\"text\" size=\"20\" name=\"new_group_gid\">" .
      "<br /><tr><td>*<i>required</i></tr></td></td></tr>" .
      "<tr><td colspan=\"2\" align=\"center\">" .
      "<input type=\"submit\" value=\"Create\">" .
      "</td></tr></form></table>");

echo $ac->get_footer();
?>
