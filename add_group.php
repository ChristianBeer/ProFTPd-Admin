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

$field_gid       = $cfg['field_gid'];
$field_groupname = $cfg['field_groupname'];
$field_members   = $cfg['field_members'];
$errors          = array();

if (!empty($_REQUEST["action"]) && $_REQUEST["action"] == "create") {
  /* group name validation */
  if (empty($_REQUEST[$field_groupname])
      || !preg_match($cfg['groupname_regex'], $_REQUEST[$field_groupname])
      || strlen($_REQUEST[$field_groupname]) > $cfg['max_groupname_length']) {
    array_push($errors, 'Invalid group name; group name must contain only letters, numbers, hyphens, and underscores with a maximum of '.$cfg['max_groupname_length'].' characters.');
  }
  /* group name uniqueness validation */
  if ($ac->check_groupname($_REQUEST[$field_groupname])) {
    array_push($errors, 'Name already exists; name must be unique.');
  }
  /* gid validation */
  if (empty($_REQUEST[$field_gid]) || !$ac->is_valid_id($_REQUEST[$field_gid])) {
    array_push($errors, 'Invalid GID; GID must be a positive integer.');
  }
  if ($cfg['max_gid'] != -1 && $cfg['min_gid'] != -1) {
    if ($_REQUEST[$field_gid] > $cfg['max_gid'] || $_REQUEST[$field_gid] < $cfg['min_gid']) {
      array_push($errors, 'Invalid GID; GID must be between ' . $cfg['min_gid'] . ' and ' . $cfg['max_gid'] . '.');
    }
  }  else if ($cfg['max_gid'] != -1 && $_REQUEST[$field_gid] > $cfg['max_gid']) {
    array_push($errors, 'Invalid GID; GID must be at most ' . $cfg['max_gid'] . '.');
  }  else if ($cfg['min_gid'] != -1 && $_REQUEST[$field_gid] < $cfg['min_gid']) {
    array_push($errors, 'Invalid GID; GID must be at least ' . $cfg['min_gid'] . '.');
  }
  /* gid uniqueness validation */
  if ($ac->check_gid($_REQUEST[$field_gid])) {
    array_push($errors, 'GID already exists; GID must be unique.');
  }
  /* data validation passed */
  if (count($errors) == 0) {
    $groupdata = array($field_groupname => $_REQUEST[$field_groupname],
                       $field_gid       => $_REQUEST[$field_gid],
                       $field_members   => '');
    if ($ac->add_group($groupdata)) {
        $infomsg = 'Group "'.$_REQUEST[$cfg['field_groupname']].'" created successfully.';
    } else {
        $errormsg = 'Group "'.$_REQUEST[$cfg['field_groupname']].'" creation failed; check log files.';
    }
  }  else {
    $errormsg = implode($errors, "<br />\n");
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
              <label for="<?php echo $cfg['field_groupname']; ?>" class="col-sm-4 control-label">Group name</label>
              <div class="controls col-sm-8">
                <input type="text" class="form-control" id="<?php echo $cfg['field_groupname']; ?>" name="<?php echo $cfg['field_groupname']; ?>" placeholder="Enter a group name" maxlength="<?php echo $cfg['max_groupname_length']; ?>" pattern="<?php echo substr($cfg['groupname_regex'], 2, -3); ?>" required>
                <p class="help-block"><small>Only letters, numbers, hyphens, and underscores. Maximum <?php echo $cfg['max_groupname_length']; ?> characters.</small></p>
              </div>
            </div>
            <!-- GID -->
            <div class="form-group">
              <label for="<?php echo $cfg['field_gid']; ?>" class="col-sm-4 control-label">GID</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="<?php echo $field_gid; ?>" name="<?php echo $field_gid; ?>" placeholder="Enter the GID" min="1" required>
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
