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

global $cfg;

include_once ("configs/config.php");
include_once ("includes/Session.php");
include_once ("includes/AdminClass.php");

$ac = new AdminClass($cfg);

$field_userid   = $cfg['field_userid'];
$field_id       = $cfg['field_id'];

if (empty($_REQUEST[$field_id])) {
  header("Location: users.php");
  die();
}

$id = $_REQUEST[$field_id];
if (!$ac->is_valid_id($id)) {
  $errormsg = 'Invalid ID; must be a positive integer.';
} else {
  $user = $ac->get_user_by_id($id);
  if (!is_array($user)) {
    $errormsg = 'User does not exist; cannot find ID '.$id.' in the database.';
  } else {
    $userid = $user[$field_userid];
  }
}

if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "reallyremove") {
  $groups = $ac->get_groups();
  while (list($g_gid, $g_group) = each($groups)) {
    if (!$ac->remove_user_from_group($userid, $g_gid)) {
      $errormsg = 'Cannot remove user "'.$userid.'" from group "'.$g_group.'"; see log files for more information.';
      break;
    }
  }
  if (empty($errormsg)) {
    if ($ac->remove_user_by_id($id)) {
      $infomsg = 'User "'.$userid.'" removed successfully.';
    } else {
      $errormsg = 'User "'.$userid.'" removal failed; see log files for more information.';
    }
  }
}

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<?php if (!empty($_REQUEST["action"]) && $_REQUEST["action"] == "reallyremove") { ?>
<!-- action: reallyremove -->
<div class="col-xs-12 col-sm-8 col-md-6 center">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <!-- Actions -->
          <div class="form-group">
            <div class="col-sm-12">
              <a class="btn btn-primary pull-right" href="users.php" role="button">View users &raquo;</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php } else { ?>
<!-- action: remove -->
<div class="col-xs-12 col-sm-8 col-md-6 center">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Remove user</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <form role="form" class="form-horizontal" method="post">
            <!-- GID -->
            <div class="form-group">
              <div class="col-sm-12">
                <p>Please confirm removal of user "<?php echo $userid; ?>" with ID <?php echo $id; ?>.</p>
              </div>
            </div>
            <!-- Actions -->
            <div class="form-group">
              <div class="col-sm-12">
                <input type="hidden" name="<?php echo $field_id; ?>" value="<?php echo $id; ?>" />
                <a class="btn btn-default" role="group" href="edit_user.php?action=show&<?php echo $field_id; ?>=<?php echo $id; ?>">Cancel</a>
                <button type="submit" class="btn btn-danger pull-right" role="group" name="action" value="reallyremove" <?php if (isset($errormsg)) { echo 'disabled="disabled"'; } ?>>Remove user</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>
