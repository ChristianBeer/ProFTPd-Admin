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
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);
echo $ac->get_header();

if (@isset($_REQUEST["p_new"])) {
    ob_start();
}

if (isset($_REQUEST["p_new"])) {
    if (!is_numeric($_REQUEST["user_uid"])) {
        ob_get_contents();
        print("Bad UID, please try again.");
        echo $ac->get_footer();
        ob_end_flush();
        die;
    }

    if (!preg_match($cfg['userid_regex'], $_REQUEST["userid"])) {
        ob_get_contents();
        print("Bad username, please try again.");
        echo $ac->get_footer();
        ob_end_flush();
        die;
    }

    if ($ac->check_username($_REQUEST["userid"])) {
        print("Username is already in use, please try again.");
        echo $ac->get_footer();
        ob_end_flush();
        die;
    }

    $pw_len = strlen($_REQUEST["passwd"]);
    if ($pw_len < $cfg['min_passwd_length']) {
        print "Password is too short, must be at least ".$cfg['min_passwd_length']." characters. Please try again.<br />";
        echo $ac->get_footer();
        die;
    }

    if (strlen($_REQUEST["homedir"]) <= 1) {
        print("Incorrect home dir, please try again.");
        include ("includes/footer.php.inc");
        ob_end_flush();
        die;
    }

    if (strlen($_REQUEST["gid"]) <= 0) {
        print("Incorrect main group, please try again.");
        include ("includes/footer.php.inc");
        ob_end_flush();
        die;
    }

    if (strlen($_REQUEST["shell"]) <= 1) {
        print("Incorrect shell type, please try again.");
        include ("includes/footer.php.inc");
        ob_end_flush();
        die;
    }
/*
    if (strlen($_REQUEST["name"]) <= 1) {
        print("Incorrect name, please try again.");
        include ("includes/footer.php.inc");
        ob_end_flush();
        die;
    }

    if (strlen($_REQUEST["email"]) <= 4) {
        print("Incorrect mail adress, please try again.");
        include ("includes/footer.php.inc");
        ob_end_flush();
        die;
    }
*/
    $disabled = isset($_REQUEST["disabled"])?'1':'0';
    $userdata = array("userid" => $_REQUEST["userid"], "name" => $_REQUEST["name"] , "email" => $_REQUEST["email"] , "title" => $_REQUEST["title"] , "company" => $_REQUEST["company"] , "comment" => $_REQUEST["comment"] , "gid" => $_REQUEST["gid"] , "user_uid" => $_REQUEST["user_uid"] , "passwd" => $_REQUEST["passwd"] , "homedir" => $_REQUEST["homedir"] , "shell" => $_REQUEST["shell"], "disabled" => $disabled);
    if(!$ac->add_user($userdata)) {
        print("An error occured while creating <b>" . $_REQUEST["userid"] . "</b> please check database consistency");
        echo $ac->get_footer();
        die;
    }
    $groups = $ac->get_groups();

    if (isset($_REQUEST["ad_gid"])) {
        while (list($key, $group) = each($_REQUEST["ad_gid"])) {
            $ac->add_user_to_group_by_name($_REQUEST["userid"], $group);
        }
    }
    print("Added user: <b>" . $_REQUEST["userid"] . "</b>");
    echo $ac->get_footer();
    die;
}

$groups = $ac->get_groups();
if (!is_array($groups)) {
    print("<strong>No groups available, please create at least one group!</strong>");
    echo $ac->get_footer();
    die;
}

$random_password_length = 6;
$rnd_password = crypt(uniqid(rand(), 1));
$rnd_password = strip_tags(stripslashes($rnd_password));
$rnd_password = str_replace(".", "", $rnd_password);
$rnd_password = strrev(str_replace("/", "", $rnd_password));
$password = substr($rnd_password, 0, $random_password_length);

if (empty($cfg['default_uid'])) {
    $uid = $ac->get_last_user_index() + 1;
} else {
    $uid = $cfg['default_uid'];
}

$homedir = $cfg['default_homedir'];

include ("includes/userform.php");
print("<tr><td colspan=\"2\" align=\"center\">" .
        "<input type=\"submit\" name=\"p_new\" value=\"Create\">" .
        "</td></tr></form></table>");

echo $ac->get_footer();
?>
