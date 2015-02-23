<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Ricardo Padilha <ricardo@droboports.com>
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 *
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

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
      $errormsg = 'Cannot remove user "'.$userid.'" from group "'.$ggroup.'"; see log files for more information.';
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
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="row">
        <div class="hidden-xs col-sm-3 col-md-3 col-lg-3"></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
          <!-- Actions -->
          <div class="form-group">
            <div class="col-sm-12">
              <a class="btn btn-primary pull-right" href="users.php" role="button">View users &raquo;</a>
            </div>
          </div>
        </div>
        <div class="hidden-xs col-sm-3 col-md-3 col-lg-3"></div>
      </div>
    </div>
  </div>
</div>

<?php } else { ?>
<!-- action: remove -->
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Remove user "<?= $userid ?>"</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="hidden-xs col-sm-3 col-md-3 col-lg-3"></div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
          <form role="form" class="form-horizontal" method="post">
            <!-- GID -->
            <div class="form-group">
              <div class="col-sm-12">
                <p>Please confirm removal of user "<?= $userid ?>" with GID <?= $id ?>.</p>
              </div>
            </div>
            <!-- Actions -->
            <div class="form-group">
              <div class="col-sm-12">
                <input type="hidden" name="<?= $field_id ?>" value="<?= $id ?>" />
                <a class="btn btn-default" role="group" href="edit_user.php?action=show&<?= $field_id ?>=<?= $id ?>">Cancel</a>
                <button type="submit" class="btn btn-danger pull-right" role="group" name="action" value="reallyremove" <?php if (isset($errormsg)) { echo 'disabled="disabled"'; } ?>>Remove user</button>
              </div>
            </div>
          </form>
        </div>
        <div class="hidden-xs col-sm-3 col-md-3 col-lg-3"></div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>
