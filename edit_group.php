<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 *
 * @todo some database column names are not generic
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);
echo $ac->get_header();

if (isset($_REQUEST["new_group_gid"]) && $_REQUEST["update"] == "update") {
    if (!is_numeric($_REQUEST["new_group_gid"])) {
        print ("Bad GID, please try again.<br />");
        echo $ac->get_footer();
        die;
    }
    $groups = $ac->get_groups();
    if (count($groups) > 0) {
        while (list($gid, $name) = each($groups)) {
            if ($gid == $_REQUEST["new_group_gid"]) {
                print("Duplicate GID, please try again.<br />");
                echo $ac->get_footer();
                die;
            }
        }
    }

    if ($ac->update_group($_REQUEST["gid"], $_REQUEST["new_group_gid"])) {
        print("Updated group database successfully.<br />");
    }
} else {
    if (!empty($_REQUEST["action"])) {
        switch ($_REQUEST["action"]) {
            case "remove":
                $users_main = $ac->get_users_by_groupid($_REQUEST["gid"]);
                $group = $ac->get_group_by_gid($_REQUEST["gid"]);
                print("<table>");
                if ($users_main) {
                    print("<tr><td align=\"center\"><b>There is a least one user who have ".$group->groupname." as its main group: " . implode(", ", $users_main) . "</b></td></tr>");
                    print("<tr><td align=\"center\">You probably should not delete this group...</td></tr>");
                }
                print("<tr><td align=\"center\">Really remove this group: <b>" . $group->groupname . "</b></td></tr>" .
                        "<tr><td align=\"center\"><form method=\"post\">" .
                        "<input type=\"hidden\" name=\"action\" value=\"reallyremove\">" .
                        "<input type=\"hidden\" name=\"gid\" value=\"" . $_REQUEST["gid"] . "\">" .
                        "<input type=\"submit\" value=\"Delete group\">" .
                        "</td></tr></form></center></table>");
                break;
            case "reallyremove":
                if ($ac->delete_group_by_gid($_REQUEST["gid"])) {
                    print("Group removed.<br />");
                }
                break;
            case "show":
                $groups = $ac->get_group_by_gid($_REQUEST["gid"]);
                if (is_object($groups)) {
                    $groupname = $groups->groupname;
                    $members = $groups->members;
                }else {
                    print("Could not find group with gid=" . $_REQUEST["gid"] . "<br />");
                    echo $ac->get_footer();
                    die;
                }

                print("<table><tr><td></td></tr><tr><td align=\"center\"><b>Users with this group as main group:</b></td></tr>");
                $users_main = $ac->get_users_by_groupid($_REQUEST["gid"]);
                print("<tr><td align=\"center\">");
                if ($users_main) {
                    foreach ($users_main as $id => $userid) {
                        print("<a href=\"edit_user.php?id=" . $id . "&name=" . $userid . "\">" . $userid . "</a>, ");
                    }
                } else {
                    print("Could not find any users with this group as main group.");
                }
                print("</td></tr><tr><td><br /></td></tr>");

                if (strlen($members) > 0) {
                    print("<tr><td align=\"center\"><b>Users with this group as additional group:</b></td></tr>");
                    $userids = explode(",", $members);
                    $count = count($userids);
                    print("<tr><td align=\"center\">");
                    for ($i = 0; $i < $count; ++$i) {
                        $user = $ac->get_user_by_userid($userids[$i]);
                        print("<a href=\"edit_user.php?id=" . $user[$cfg['field_id']] . "&name=" . $user[$cfg['field_userid']] . "\">" . $userids[$i] . "</a>,&nbsp;");
                    }
                    print("</td></tr><tr><td><br /></td></tr>");
                }

                print("<form method=\"post\">" .
                        "<tr><td align=\"center\"><b>GID:</b></td></tr>" .
                        "<tr><td align=\"center\"><input type=\"hidden\" size=\"10\" name=\"update\" value=\"update\">" .
                        "<input type=\"text\" size=\"20\" name=\"new_group_gid\" value=\"" . $_REQUEST["gid"] . "\"></td></tr><tr><td><br /></td></tr>" .
                        "<tr><td align=\"center\"><input type=\"submit\" value=\"Update\"></td></tr></form>" .
                        "<tr><td align=\"center\"><form method=\"post\">" .
                        "<input type=\"hidden\" name=\"action\" value=\"remove\">" .
                        "<input type=\"hidden\" name=\"gid\" value=\"" . $_REQUEST["gid"] . "\">" .
                        "<input type=\"submit\" value=\"Remove\">" .
                        "</td></tr></form></table>");
                break;
        }
    }
}

echo $ac->get_footer();
?>
