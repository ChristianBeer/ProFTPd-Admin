<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Ricardo Padilha <ricardo@droboports.com>
 *
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);

$field_gid       = $cfg['field_gid'];
$field_newgid    = "new_".$cfg['field_gid'];
$field_groupname = $cfg['field_groupname'];
$field_members   = $cfg['field_members'];

if (empty($_REQUEST[$field_gid])) {
  header("Location: groups.php");
  die();
}

$gid = $_REQUEST[$field_gid];
if (!$ac->is_valid_id($gid)) {
  $errormsg = 'Invalid GID; must be a positive integer.';
} else {
  $group = $ac->get_group_by_gid($gid);
  if (!is_array($group)) {
    $errormsg = 'Group does not exist; cannot find GID '.$gid.' in the database.';
  } else {
    $groupname = $group[$field_groupname];
    $members = $group[$field_members];
    $users_main = $ac->get_users_by_gid($gid);
    $users_add = $ac->get_add_users_by_gid($gid);
    if ($users_main) {
      $errormsg = 'Group cannot be removed; it is the main group of the user(s) '.implode(", ", $users_main);
    }
    if ($users_add) {
      $warnmsg = 'Group in use; it is an additional group of the user(s): '.implode(", ", $users_add);
    }
  }
}

if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "reallyremove") {
  /* data validation passed */
  if ($ac->delete_group_by_gid($gid)) {
    header('Location: groups.php?info=removeGroup&groupname='.$groupname);
  } else {
    header('Location: groups.php?error=removeGroup&groupname='.$groupname);
  }
  exit();
}

include ("includes/header.php");
include ("includes/messages.php"); ?>

<!-- action: remove -->
<div class="col-xs-12 col-sm-8 col-md-6 center">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Remove group</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <form role="form" class="form-horizontal" method="post">
            <!-- GID -->
            <div class="form-group">
              <div class="col-sm-12">
                <p>Please confirm removal of group "<?php echo $groupname; ?>" with GID <?php echo $gid; ?>.</p>
              </div>
            </div>
            <!-- Actions -->
            <div class="form-group">
              <div class="col-sm-12">
                <input type="hidden" name="<?php echo $field_gid; ?>" value="<?php echo $gid; ?>" />
                <a class="btn btn-default" role="group" href="edit_group.php?action=show&<?php echo $field_gid; ?>=<?php echo $gid; ?>">View group</a>
                <button type="submit" class="btn btn-danger pull-right" role="group" name="action" value="reallyremove" <?php if (isset($errormsg)) { echo 'disabled="disabled"'; } ?>>Remove group</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include ("includes/footer.php"); ?>
