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
?>

  <table>
    <form method="post">
    <tr>
      <td colspan="2"></td>
    </tr>
    <tr bgcolor="<?php print ($cfg['tpbgcolor']) ?>">
      <td><b>Label</b></td>
      <td><b>Input</b></td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor1']) ?>">
      <td class="label">UID *:</td>
      <td>
        <input type="text" size="25" maxlength="20" name="user_uid" value="<?php print($uid) ?>" />
      </td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor2']) ?>">
      <td class="label">Username *:</td>
      <td>
        <input type="text" size="25" maxlength="20" name="userid" value="<?php print(@$userid) ?>" />
      </td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor1']) ?>">
      <td class="label">Password * <?php print(@$blank); ?>:</td>
       <td>
         <input type="text" size="25" maxlength="20" name="passwd" value="<?php print(@$password) ?>" />
       </td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor2']) ?>">
      <td class="label">Real name *:</td>
      <td>
        <input type="text" size="25" maxlength="30" name="name" value="<?php print(@$name) ?>" />
       </td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor1']) ?>">
      <td class="label">E-mail address:</td>
      <td>
        <input type="text" size="25" maxlength="30" name="email" value="<?php print(@$email) ?>" />
      </td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor2']) ?>">
      <td class="label">Home directory * (no trailing slash):</td>
      <td>
        <input type="text" size="25" maxlength="60" name="homedir" value="<?php print($homedir) ?>" />
       </td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor1']) ?>">
      <td class="label">Suspend this user account?</td>
      <td>
        <?php
        if (@$disabled == 1) {
            print("<input type=\"checkbox\" name=\"disabled\" checked />");
        } else {
            print("<input type=\"checkbox\" name=\"disabled\" />");
        }
        ?>
       </td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor2']) ?>">
      <td class="label">Main group *:</td>
      <td>
        <select name="gid">
        <?php
          while (list($gid,$group) = each($groups)){
            if ($group == @$default_group) {
                print("<option value=\"$gid\">$group</option>\n");
            } else {
                print("<option value=\"$gid\" selected=\"selected\">$group</option>\n");
            }
          }
        ?>
      </td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor1']) ?>">
      <td class="label">Additional groups:</td>
      <td>
        <?php
          reset($groups);
          $cnt = 0;
          while (list ($gid, $group) = each ($groups)) {
            if (@$default_group != $group) {
              if ($group == @$uid_groups[$gid]) {
                print("<input type=checkbox name=\"ad_gid[$cnt]\" value=\"$group\" checked=\"checked\" />$group ");
              } else {
                print("<input type=checkbox name=\"ad_gid[$cnt]\" value=\"$group\" />$group ");
              }
              $cnt = $cnt + 1;
            }
          }
        ?>
      </td>
    </tr>
    <tr bgcolor="<?php print ($cfg['dwbgcolor2']) ?>">
      <td class="label" valign="top">Comment/Info:</td>
      <td>
        <textarea name="comment" rows="5" cols="32"><?php print(@$comment) ?></textarea>
      </td>
    </tr>
    <tr>
      <td align="left" >*<i>required</i></td>
    </tr>
    <td>
      <input type="hidden" size="40" maxlength="60" name="shell" value="/sbin/false" />
      <input type="hidden" name="uid" value="<?php print($uid) ?>" />
    </td>
