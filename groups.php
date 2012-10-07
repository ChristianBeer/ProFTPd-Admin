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
 * @todo Change tables to divs and declare colors in style.css
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);
echo $ac->get_header();

$groups = $ac->get_groups();
$nof_columns = 2;
print("<table><tr><td colspan=\"" . $nof_columns . "\">" .
        "</td></tr>" .
        "<tr bgcolor=\"" . $cfg['tpbgcolor'] . "\">" .
        "<td><b>Groupname</b></td>" .
        "<td><b>GID</b></td>" .
        "</tr>");

if (isset($groups)) {
    $counter = 0;
    while (list($gid, $group) = each($groups)) {
        if ($counter % 2 == 0) {
            print("<tr bgcolor=\"" . $cfg['dwbgcolor1'] . "\">");
        } else {
            print("<tr bgcolor=\"" . $cfg['dwbgcolor2'] . "\">");
        }
        print("<td><a href=\"edit_group.php?action=show&gid=" . $gid . "\">" . $group . "</a></td>" .
                "<td>$gid</td></tr>");
        $counter = $counter + 1;
    }
}
print("<tr><td colspan=\"" . $nof_columns . "\"><i>To edit a group: click on the groupname</i></td</tr></table>");

echo $ac->get_footer();
?>
