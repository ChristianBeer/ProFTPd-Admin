<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 *
 * @todo some database column names are not generic
 * @todo make needed fields configurable
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);
echo $ac->get_header();

if (isset($_REQUEST["id"]) && isset($_REQUEST["gid"]) && isset($_REQUEST["userid"]) && isset($_REQUEST["name"])) {
    if (!is_numeric($_REQUEST["user_uid"])) {
        print ("Bad UID, please try again.<br />");
        echo $ac->get_footer();
        die;
    }

    if (!preg_match($cfg['userid_regex'], $_REQUEST["userid"])) {
        print "Bad username, please try again.<br />";
        echo $ac->get_footer();
        die;
    }
    if (strlen($_REQUEST["homedir"]) <= 1) {
        print("Incorrect home dir, please try again.<br />");
        echo $ac->get_footer();
        die;
    }
    if (strlen($_REQUEST["shell"]) <= 1) {
        print("Incorrect shell type, please try again.<br />");
        echo $ac->get_footer();
        die;
    }
    /*
    if (strlen($_REQUEST["name"]) <= 1) {
        print("Incorrect name, please try again.<br />");
        echo $ac->get_footer();
        die;
    }

    if (strlen($_REQUEST["email"])<=4) {
      print("Incorrect mail adress, please try again.<br />");
      echo $ac->get_footer();
      die;
      } */
    $pw_len = strlen($_REQUEST["passwd"]);
    if ($pw_len > 0 && $pw_len <= $cfg['min_passwd_length']) {
        print "Password is too short, must be at least ".$cfg['min_passwd_length']." characters. Please try again.<br />";
        echo $ac->get_footer();
        die;
    }

    $disabled = isset($_REQUEST["disabled"])?'1':'0';
    $userdata = array("id" => $_REQUEST["id"], "userid" => $_REQUEST["userid"], "name" => $_REQUEST["name"] , "email" => $_REQUEST["email"] , "comment" => $_REQUEST["comment"] , "gid" => $_REQUEST["gid"] , "user_uid" => $_REQUEST["user_uid"] , "passwd" => $_REQUEST["passwd"] , "homedir" => $_REQUEST["homedir"] , "shell" => $_REQUEST["shell"], "disabled" => $disabled);
    if ($ac->update_user($userdata)) {
        print("Updated user database successfully.<br />");
    }

    $ac->remove_user_from_all_groups($_REQUEST["userid"]);

    if (isset($_REQUEST["ad_gid"])) {
        print("Updating additional groups<p>");
        $array = $_REQUEST["ad_gid"];
        while (list($key, $group) = each($_REQUEST["ad_gid"])) {
            if($ac->add_user_to_group_by_name($_REQUEST["userid"], $group)) {
                print("Successfully added " . $group . "<br />");
            } else {
                print("Failure while adding " . $group . "<br />");
            }
        }
    }

    print("Updated additional groups.<br />");
} else if (isset($_REQUEST["action"]) && isset($_REQUEST["id"]) && isset($_REQUEST["name"])) {
    switch ($_REQUEST["action"]) {
        case "remove":
            print("<table>" .
                    "<tr><td align=\"center\">Really remove <b>" . $_REQUEST["userid"] . "</b>?</td></tr>" .
                    "<tr><td align=\"center\"><form method=\"post\">" .
                    "<input type=\"hidden\" size=\"10\" name=\"action\" value=\"reallyremove\">" .
                    "<input type=\"hidden\" size=\"10\" name=\"id\" value=\"" . $_REQUEST["id"] . "\">" .
                    "<input type=\"hidden\" size=\"10\" name=\"name\" value=\"" . $_REQUEST["name"] . "\">" .
                    "<input type=\"hidden\" size=\"10\" name=\"userid\" value=\"" . $_REQUEST["userid"] . "\">" .
                    "<input type=\"submit\" value=\"Remove\">" .
                    "</td></tr></form></table>");
            break;
        case "reallyremove":
            $ac->remove_user_from_all_groups($_REQUEST["userid"]);
            if ($ac->delete_user_by_userid($_REQUEST["userid"])) {
                print("User has been removed.<br />");
            } else {
                print("There was a failure while deleting the user record. Please check the database!<br />");
            }
            break;
    }
} else if (isset($_REQUEST["id"])) {
    $user = $ac->get_user_by_id($_REQUEST["id"]);

    $uid = $user[$cfg['field_uid']];
    $userid = $user[$cfg['field_userid']];
    $gid = $user[$cfg['field_gid']];
    $password = "";
    $comment = $user[$cfg['field_comment']];
    $disabled = $user[$cfg['field_disabled']];
    $shell = $user[$cfg['field_shell']];
    $homedir = $user[$cfg['field_homedir']];
    $email = $user[$cfg['field_email']];
    $name = $user[$cfg['field_name']];
    $groups_array = $ac->parse_groups();
    $groups = $ac->get_groups();
    $default_group = $groups["$gid"];
    $uid_groups = @$groups_array["$userid"];
    $blank = "(blank = don't change)";
    include("includes/userform.php");
    print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Update\">" .
            "</td></tr></form>" .
            "<tr><td colspan=\"2\" align=\"center\"><form method=\"post\">" .
            "<input type=\"hidden\" name=\"uid\" value=\"" . $uid . "\">" .
            "<input type=\"hidden\" name=\"action\" value=\"remove\">" .
            "<input type=\"hidden\" name=\"userid\" value=\"" . $userid . "\">" .
            "<input type=\"submit\" value=\"Remove\">" .
            "</td></tr></form></table>");
} else {
    print("User not found.");
}

echo $ac->get_footer();
?>
