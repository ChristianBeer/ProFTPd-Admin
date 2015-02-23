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
 * @todo some database column names are not generic
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);

$field_gid       = $cfg['field_gid'];
$field_newgid    = "new_".$cfg['field_gid'];
$field_groupname = $cfg['field_groupname'];
$field_members   = $cfg['field_members'];
$field_id        = $cfg['field_id'];
$field_uid       = $cfg['field_uid'];
$field_disabled  = $cfg['field_disabled'];

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
  }
}

if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "update") {
  /* gid validation */
  if (empty($_REQUEST[$field_newgid])
      || !$ac->is_valid_id($_REQUEST[$field_newgid])) {
    $errormsg = 'Invalid GID; GID must be a positive integer.';
  }
  /* gid uniqueness validation */
  if (empty($errormsg) && $ac->check_gid($_REQUEST[$field_newgid])) {
    $errormsg = 'GID already exists; GID must be unique.';
  }
  if (empty($errormsg)) {
    /* data validation passed */
    $newgid = $_REQUEST[$field_newgid];
    if ($ac->update_group($gid, $newgid)) {
      $infomsg = 'Group database updated successfully.';
      /* update data */
      $gid = $newgid;
      $users_main = $ac->get_users_by_gid($gid);
      $users_add = $ac->get_add_users_by_gid($gid);
    } else {
      $errormsg = 'Group update failed; check log files.';
    }
  }
}

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<?php if (is_array($group)) { ?>
<!-- Users panel -->
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Group membership</h3>
    </div>
    <div class="panel-body">
      <!-- Main users table -->
      <h4>Main users</h4>
      <?php if (!$users_main) { ?>
        <p>Currently there are no users with this group as their main group.</p>
      <?php } else { ?>
        <table class="table table-striped table-condensed sortable">
          <thead>
            <th>UID</th>
            <th><span class="glyphicon glyphicon-user" aria-hidden="true" title="User"></th>
            <th><span class="glyphicon glyphicon-lock" aria-hidden="true" title="Suspended"></th>
            <th data-defaultsort="disabled"></th>
          </thead>
          <tbody>
            <?php reset($users_main); while (list($u_id, $u_userid) = each($users_main)) {
              $user = $ac->get_user_by_id($u_id); ?>
              <tr>
                <td class="pull-middle"><?= $user[$field_uid] ?></td>
                <td class="pull-middle"><?= $u_userid ?></td>
                <td class="pull-middle"><?= ($user[$field_disabled] ? 'Yes' : 'No') ?></td>
                <td class="pull-middle">
                  <div class="btn-toolbar pull-right" role="toolbar">
                    <a class="btn-group" role="group" href="edit_user.php?action=show&<?= $field_id ?>=<?= $u_id ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                    <a class="btn-group" role="group" href="remove_user.php?action=remove&<?= $field_id ?>=<?= $u_id ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } ?>
      <!-- Additional users table -->
      <h4>Additional users</h4>
      <?php if (!$users_add) { ?>
        <p>Currently there are no users with this group in their additional groups.</p>
      <?php } else { ?>
        <table class="table table-striped table-condensed sortable">
          <thead>
            <th>UID</th>
            <th><span class="glyphicon glyphicon-user" aria-hidden="true" title="User"></th>
            <th><span class="glyphicon glyphicon-lock" aria-hidden="true" title="Suspended"></th>
            <th data-defaultsort="disabled"></th>
          </thead>
          <tbody>
            <?php reset($users_add); while (list($u_id, $u_userid) = each($users_add)) {
              $user = $ac->get_user_by_id($u_id); ?>
              <tr>
                <td class="pull-middle"><?= $user[$field_uid] ?></td>
                <td class="pull-middle"><?= $u_userid ?></td>
                <td class="pull-middle"><?= ($user[$field_disabled] ? 'Yes' : 'No') ?></td>
                <td class="pull-middle">
                  <div class="btn-toolbar pull-right" role="toolbar">
                    <a class="btn-group" role="group" href="edit_user.php?action=show&<?= $field_id ?>=<?= $u_id ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                    <a class="btn-group" role="group" href="remove_user.php?action=remove&<?= $field_id ?>=<?= $u_id ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } ?>
    </div>
  </div>
</div>
<!-- Edit panel -->
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Group properties</h3>
    </div>
    <div class="panel-body">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <form role="form" class="form-horizontal" method="post" data-toggle="validator">
          <!-- Group name (readonly) -->
          <div class="form-group">
            <label for="<?= $cfg['field_groupname'] ?>" class="col-sm-3 control-label">Group name</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $cfg['field_groupname'] ?>" name="<?= $cfg['field_groupname'] ?>" value="<?= $groupname ?>" readonly />
            </div>
          </div>
          <!-- GID -->
          <div class="form-group">
            <label for="<?= $cfg['field_gid'] ?>" class="col-sm-3 control-label">New GID</label>
            <div class="col-sm-9">
              <input type="number" class="form-control" id="new_<?= $cfg['field_gid'] ?>" name="new_<?= $cfg['field_gid'] ?>" value="<?= $gid ?>" placeholder="Enter the new GID" min="1" required />
              <p class="help-block"><small>Positive integer.</small></p>
            </div>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <div class="col-sm-12">
              <input type="hidden" name="<?= $field_gid ?>" value="<?= $gid ?>" />
              <a class="btn btn-danger" href="remove_group.php?action=remove&<?= $field_gid ?>=<?= $gid ?>">Remove group</a>
              <button type="submit" class="btn btn-primary pull-right" role="group" name="action" value="update">Update group</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>
