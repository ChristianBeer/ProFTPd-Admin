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
$field_groupname = $cfg['field_groupname'];
$field_members   = $cfg['field_members'];

if (!empty($_REQUEST["action"]) && $_REQUEST["action"] == "create") {
  /* group name validation */
  if (empty($_REQUEST[$field_groupname])
      || !preg_match($cfg['groupname_regex'], $_REQUEST[$field_groupname])
      || strlen($_REQUEST[$field_groupname]) > $cfg['max_groupname_length']) {
    $errormsg = 'Invalid group name; group name must contain only letters, numbers, hyphens, and underscores with a maximum of '.$cfg['max_groupname_length'].' characters.';
  }
  /* group name uniqueness validation */
  if (empty($errormsg) && $ac->check_groupname($_REQUEST[$field_groupname])) {
    $errormsg = 'Name already exists; name must be unique.';
  }
  /* gid validation */
  if (empty($errormsg) && (empty($_REQUEST[$field_gid]) || !$ac->is_valid_id($_REQUEST[$field_gid]))) {
    $errormsg = 'Invalid GID; GID must be a positive integer.';
  }
  /* gid uniqueness validation */
  if (empty($errormsg) && $ac->check_gid($_REQUEST[$field_gid])) {
    $errormsg = 'GID already exists; GID must be unique.';
  }
  /* data validation passed */
  if (empty($errormsg)) {
    $groupdata = array($field_groupname => $_REQUEST[$field_groupname],
                       $field_gid       => $_REQUEST[$field_gid],
                       $field_members   => '');
    if ($ac->add_group($groupdata)) {
        $infomsg = 'Group "'.$_REQUEST[$cfg['field_groupname']].'" created successfully.';
    } else {
        $errormsg = 'Group "'.$_REQUEST[$cfg['field_groupname']].'" creation failed; check log files.';
    }
  }
}

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<div class="col-xs-12 col-sm-8 col-md-6 center">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Add group</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <form role="form" class="form-horizontal" method="post" data-toggle="validator">
            <!-- Group name -->
            <div class="form-group">
              <label for="<?= $cfg['field_groupname'] ?>" class="col-sm-4 control-label">Group name</label>
              <div class="controls col-sm-8">
                <input type="text" class="form-control" id="<?= $cfg['field_groupname'] ?>" name="<?= $cfg['field_groupname'] ?>" placeholder="Enter a group name" maxlength="<?= $cfg['max_groupname_length'] ?>" pattern="<?= substr($cfg['groupname_regex'], 2, -3) ?>" required>
                <p class="help-block"><small>Only letters, numbers, hyphens, and underscores. Maximum <?= $cfg['max_groupname_length'] ?> characters.</small></p>
              </div>
            </div>
            <!-- GID -->
            <div class="form-group">
              <label for="<?= $cfg['field_gid'] ?>" class="col-sm-4 control-label">GID</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="<?= $field_gid ?>" name="<?= $field_gid ?>" placeholder="Enter the GID" min="1" required>
                <p class="help-block"><small>Positive integer.</small></p>
              </div>
            </div>
            <!-- Actions -->
            <div class="form-group">
              <div class="col-sm-12">
                <a class="btn btn-default" href="groups.php">&laquo; View groups</a>
                <button type="submit" class="btn btn-primary pull-right" name="action" value="create">Create group</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include ("includes/footer.php"); ?>
