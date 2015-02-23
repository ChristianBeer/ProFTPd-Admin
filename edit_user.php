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
 * @todo make needed fields configurable
 */

include_once ("configs/config.php");
include_once ("includes/AdminClass.php");
global $cfg;

$ac = new AdminClass($cfg);

$field_userid   = $cfg['field_userid'];
$field_id       = $cfg['field_id'];
$field_uid      = $cfg['field_uid'];
$field_gid      = $cfg['field_gid'];
$field_ad_gid   = 'ad_gid';
$field_passwd   = $cfg['field_passwd'];
$field_homedir  = $cfg['field_homedir'];
$field_shell    = $cfg['field_shell'];
$field_title    = $cfg['field_title'];
$field_name     = $cfg['field_name'];
$field_company  = $cfg['field_company'];
$field_email    = $cfg['field_email'];
$field_comment  = $cfg['field_comment'];
$field_disabled = $cfg['field_disabled'];

$field_login_count    = $cfg['field_login_count'];
$field_last_login     = $cfg['field_last_login'];
$field_last_modified  = $cfg['field_last_modified'];
$field_bytes_in_used  = $cfg['field_bytes_in_used'];
$field_bytes_out_used = $cfg['field_bytes_out_used'];
$field_files_in_used  = $cfg['field_files_in_used'];
$field_files_out_used = $cfg['field_files_out_used'];

if (empty($_REQUEST[$field_id])) {
  header("Location: users.php");
  die();
}

$groups = $ac->get_groups();

$id = $_REQUEST[$field_id];
if (!$ac->is_valid_id($id)) {
  $errormsg = 'Invalid ID; must be a positive integer.';
} else {
  $user = $ac->get_user_by_id($id);
  if (!is_array($user)) {
    $errormsg = 'User does not exist; cannot find ID '.$id.' in the database.';
  } else {
    $userid = $user[$field_userid];
    $gid = $user[$field_gid];
    $group = $ac->get_group_by_gid($gid);
    if (!$group) {
      $warnmsg = 'Main group does not exist; cannot find GID '.$gid.' in the database.';
    }
    $ad_gid = $ac->parse_groups($userid);
  }
}

if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "update") {
  /* user id validation */
  if (empty($_REQUEST[$field_userid])
      || !preg_match($cfg['userid_regex'], $_REQUEST[$field_userid])
      || strlen($_REQUEST[$field_userid]) > $cfg['max_userid_length']) {
    $errormsg = 'Invalid user name; user name must contain only letters, numbers, hyphens, and underscores with a maximum of '.$cfg['max_userid_length'].' characters.';
  }
  /* uid validation */
  if (empty($errormsg) && (empty($_REQUEST[$field_uid]) || !$ac->is_valid_id($_REQUEST[$field_uid]))) {
    $errormsg = 'Invalid UID; must be a positive integer.';
  }
  /* gid validation */
  if (empty($errormsg) && (empty($_REQUEST[$field_gid]) || !$ac->is_valid_id($_REQUEST[$field_gid]))) {
    $errormsg = 'Invalid main group; GID must be a positive integer.';
  }
  /* password length validation */
  if (empty($errormsg) && strlen($_REQUEST[$field_passwd]) > 0 && strlen($_REQUEST[$field_passwd]) < $cfg['min_passwd_length']) {
    $errormsg = 'Password is too short; minimum length is '.$cfg['min_passwd_length'].' characters.';
  }
  /* home directory validation */
  if (empty($errormsg) && strlen($_REQUEST[$field_homedir]) <= 1) {
    $errormsg = 'Invalid home directory; home directory cannot be empty.';
  }
  /* shell validation */
  if (empty($errormsg) && strlen($_REQUEST[$field_shell]) <= 1) {
    $errormsg = 'Invalid shell; shell cannot be empty.';
  }
  /* user name uniqueness validation */
  if (empty($errormsg) && $userid != $_REQUEST[$field_userid] && $ac->check_username($_REQUEST[$field_userid])) {
    $errormsg = 'User name already exists; name must be unique.';
  }
  /* gid uniqueness validation */
  if (empty($errormsg) && !$ac->check_gid($_REQUEST[$field_gid])) {
    $errormsg = 'Main group does not exist; GID '.$_REQUEST[$field_gid].' cannot be found in the database.';
  }
  /* data validation passed */
  if (empty($errormsg)) {
    /* remove all groups */
    while (list($g_gid, $g_group) = each($groups)) {
      if (!$ac->remove_user_from_group($userid, $g_gid)) {
        $errormsg = 'Cannot remove user "'.$userid.'" from group "'.$g_group.'"; see log files for more information.';
        break;
      }
    }
  }
  if (empty($errormsg)) {
    /* update user */
    $disabled = isset($_REQUEST[$field_disabled]) ? '1':'0';
    $userdata = array($field_id       => $_REQUEST[$field_id],
                      $field_userid   => $_REQUEST[$field_userid],
                      $field_uid      => $_REQUEST[$field_uid],
                      $field_gid      => $_REQUEST[$field_gid],
                      $field_passwd   => $_REQUEST[$field_passwd],
                      $field_homedir  => $_REQUEST[$field_homedir],
                      $field_shell    => $_REQUEST[$field_shell],
                      $field_title    => $_REQUEST[$field_title],
                      $field_name     => $_REQUEST[$field_name],
                      $field_email    => $_REQUEST[$field_email],
                      $field_company  => $_REQUEST[$field_company],
                      $field_comment  => $_REQUEST[$field_comment],
                      $field_disabled => $disabled);
    if (!$ac->update_user($userdata)) {
      $errormsg = 'User "'.$_REQUEST[$field_userid].'" update failed; check log files.';
    } else {
      /* update user data */
      $user = $ac->get_user_by_id($id);
    }
  }
  if (empty($errormsg)) {
    /* add all groups */
    if (isset($_REQUEST[$field_ad_gid])) {
      while (list($g_key, $g_gid) = each($_REQUEST[$field_ad_gid])) {
        if (!$ac->is_valid_id($g_gid)) {
            $warnmsg = 'Adding additional group failed; at least one of the additional groups had an invalid GID.';
          continue;
        }
        // XXX: fix error handling here
        $ac->add_user_to_group($_REQUEST[$field_userid], $g_gid);
      }
    }
    /* update additional groups */
    $ad_gid = $ac->parse_groups($userid);
    $infomsg = 'User "'.$_REQUEST[$field_userid].'" updated successfully.';
  }
}

/* Form values */
if (empty($errormsg)) {
  /* Default values */
  $uid      = $user[$field_uid];
  $gid      = $user[$field_gid];
  $passwd   = '';
  $homedir  = $user[$field_homedir];
  $shell    = $user[$field_shell];
  $title    = $user[$field_title];
  $name     = $user[$field_name];
  $email    = $user[$field_email];
  $company  = $user[$field_company];
  $comment  = $user[$field_comment];
  $disabled = $user[$field_disabled];
} else {
  /* This is a failed attempt */
  $userid   = $_REQUEST[$field_userid];
  $uid      = $_REQUEST[$field_uid];
  $gid      = $_REQUEST[$field_gid];
  $ad_gid   = $_REQUEST[$field_ad_gid];
  $passwd   = $_REQUEST[$field_passwd];
  $homedir  = $_REQUEST[$field_homedir];
  $shell    = $_REQUEST[$field_shell];
  $title    = $_REQUEST[$field_title];
  $name     = $_REQUEST[$field_name];
  $email    = $_REQUEST[$field_email];
  $company  = $_REQUEST[$field_company];
  $comment  = $_REQUEST[$field_comment];
  $disabled = isset($_REQUEST[$field_disabled]) ? '1' : '0';
}

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<?php if (is_array($user)) { ?>
<!-- User metadata panel -->
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">User statistics</h3>
    </div>
    <div class="panel-body">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <form role="form" class="form-horizontal" method="post" data-toggle="validator">
          <!-- Login count (readonly) -->
          <div class="form-group">
            <label for="<?= $field_login_count ?>" class="col-sm-3 control-label">Login count</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_login_count ?>" name="<?= $field_login_count ?>" value="<?= $user[$field_login_count] ?>" readonly />
            </div>
          </div>
          <!-- Last login (readonly) -->
          <div class="form-group">
            <label for="<?= $field_last_login ?>" class="col-sm-3 control-label">Last login</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_last_login ?>" name="<?= $field_last_login ?>" value="<?= $user[$field_last_login] ?>" readonly />
            </div>
          </div>
          <!-- Last modified (readonly) -->
          <div class="form-group">
            <label for="<?= $field_last_modified ?>" class="col-sm-3 control-label">Last modified</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_last_modified ?>" name="<?= $field_last_modified ?>" value="<?= $user[$field_last_modified] ?>" readonly />
            </div>
          </div>
          <!-- Bytes in (readonly) -->
          <div class="form-group">
            <label for="<?= $field_bytes_in_used ?>" class="col-sm-3 control-label">Bytes uploaded</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_bytes_in_used ?>" name="<?= $field_bytes_in_used ?>" value="<?= sprintf("%2.1f", $user[$field_bytes_in_used] / 1048576) ?> MB" readonly />
            </div>
          </div>
          <!-- Bytes out (readonly) -->
          <div class="form-group">
            <label for="<?= $field_bytes_out_used ?>" class="col-sm-3 control-label">Bytes downloaded</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_bytes_out_used ?>" name="<?= $field_bytes_out_used ?>" value="<?= sprintf("%2.1f", $user[$field_bytes_out_used] / 1048576) ?> MB" readonly />
            </div>
          </div>
          <!-- Files in (readonly) -->
          <div class="form-group">
            <label for="<?= $field_files_in_used ?>" class="col-sm-3 control-label">Files uploaded</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_files_in_used ?>" name="<?= $field_files_in_used ?>" value="<?= $user[$field_files_in_used] ?>" readonly />
            </div>
          </div>
          <!-- Files out (readonly) -->
          <div class="form-group">
            <label for="<?= $field_files_out_used ?>" class="col-sm-3 control-label">Files downloaded</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_files_out_used ?>" name="<?= $field_files_out_used ?>" value="<?= $user[$field_files_out_used] ?>" readonly />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Edit panel -->
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">User properties</h3>
    </div>
    <div class="panel-body">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <form role="form" class="form-horizontal" method="post" data-toggle="validator">
          <!-- User name -->
          <div class="form-group">
            <label for="<?= $field_userid ?>" class="col-sm-3 control-label">User name</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_userid ?>" name="<?= $field_userid ?>" value="<?= $userid ?>" placeholder="Enter a user name" maxlength="<?= $cfg['max_userid_length'] ?>" pattern="<?= substr($cfg['userid_regex'], 2, -3) ?>" required />
              <p class="help-block"><small>Only letters, numbers, hyphens, and underscores. Maximum <?= $cfg['max_userid_length'] ?> characters.</small></p>
            </div>
          </div>
          <!-- UID -->
          <div class="form-group">
            <label for="<?= $field_uid ?>" class="col-sm-3 control-label">UID</label>
            <div class="controls col-sm-9">
              <input type="number" class="form-control" id="<?= $field_uid ?>" name="<?= $field_uid ?>" value="<?= $uid ?>" min="1" placeholder="Enter a UID" required />
              <p class="help-block"><small>Positive integer.</small></p>
            </div>
          </div>
          <!-- Main group -->
          <div class="form-group">
            <label for="<?= $field_gid ?>" class="col-sm-3 control-label">Main group</label>
            <div class="controls col-sm-9">
              <select class="form-control multiselect" id="<?= $field_gid ?>" name="<?= $field_gid ?>" required>
                <?php	reset ($groups); while (list($g_gid, $g_group) = each($groups)) { ?>
        				  <option value="<?= $g_gid ?>" <?php if ($gid == $g_gid) { echo 'selected="selected"'; } ?>><?= $g_group ?></option>
        				<?php } ?>
              </select>
            </div>
          </div>
          <!-- Additional groups -->
          <div class="form-group">
            <label for="ad_gid" class="col-sm-3 control-label">Additional groups</label>
            <div class="controls col-sm-9">
              <select class="form-control multiselect" id="<?= $field_ad_gid ?>" name="<?= $field_ad_gid ?>[]" multiple="multiple">
                <?php reset ($groups); while (list($g_gid, $g_group) = each($groups)) { ?>
        				  <option value="<?= $g_gid ?>" <?php if (array_key_exists($g_gid, $ad_gid)) { echo 'selected="selected"'; } ?>><?= $g_group ?></option>
        				<?php } ?>
              </select>
            </div>
          </div>
          <!-- Password -->
          <div class="form-group">
            <label for="<?= $field_passwd ?>" class="col-sm-3 control-label">Password</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_passwd ?>" name="<?= $field_passwd ?>" value="<?= $passwd ?>" placeholder="Change password" />
              <p class="help-block"><small>Minimum length <?= $cfg['min_passwd_length'] ?> characters.</small></p>
            </div>
          </div>
          <!-- Home directory -->
          <div class="form-group">
            <label for="<?= $field_homedir ?>" class="col-sm-3 control-label">Home directory</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_homedir ?>" name="<?= $field_homedir ?>" value="<?= $homedir ?>" placeholder="Enter a home directory" />
            </div>
          </div>
          <!-- Shell -->
          <div class="form-group">
            <label for="<?= $field_shell ?>" class="col-sm-3 control-label">Shell</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_shell ?>" name="<?= $field_shell ?>" value="<?= $shell ?>" placeholder="Enter the user's shell" />
            </div>
          </div>
          <!-- Title -->
          <div class="form-group">
            <label for="<?= $field_title ?>" class="col-sm-3 control-label">Title</label>
            <div class="col-sm-9">
              <select class="form-control" id="<?= $field_title ?>" name="<?= $field_title ?>" required>
        				<option value="m" <?php if ($title == 'm') { echo 'selected="selected"'; } ?>>Mr.</option>
        				<option value="f" <?php if ($title == 'f') { echo 'selected="selected"'; } ?>>Ms.</option>
              </select>
            </div>
          </div>
          <!-- Real name -->
          <div class="form-group">
            <label for="<?= $field_name ?>" class="col-sm-3 control-label">Name</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_name ?>" name="<?= $field_name ?>" value="<?= $name ?>" placeholder="Enter the user's real name" />
            </div>
          </div>
          <!-- Email -->
          <div class="form-group">
            <label for="<?= $field_email ?>" class="col-sm-3 control-label">E-mail</label>
            <div class="controls col-sm-9">
              <input type="email" class="form-control" id="<?= $field_email ?>" name="<?= $field_email ?>" value="<?= $email ?>" placeholder="Enter the user's email" />
            </div>
          </div>
          <!-- Company -->
          <div class="form-group">
            <label for="<?= $field_company ?>" class="col-sm-3 control-label">Company</label>
            <div class="controls col-sm-9">
              <input type="text" class="form-control" id="<?= $field_company ?>" name="<?= $field_company ?>" value="<?= $company ?>" placeholder="Enter a company or department" />
            </div>
          </div>
          <!-- Comment -->
          <div class="form-group">
            <label for="<?= $field_comment ?>" class="col-sm-3 control-label">Comment</label>
            <div class="controls col-sm-9">
              <textarea class="form-control" id="<?= $field_comment ?>" name="<?= $field_comment ?>" rows="3" placeholder="Enter a comment or additional information about the user"><?= $comment ?></textarea>
            </div>
          </div>
          <!-- Suspended -->
          <div class="form-group">
            <label for="<?= $field_disabled ?>" class="col-sm-3 control-label">Status</label>
            <div class="controls col-sm-9">
              <div class="checkbox">
                <label>
                  <input type="checkbox" id="<?= $field_disabled ?>" name="<?= $field_disabled ?>" <?php if ($disabled) { echo 'checked="checked"'; } ?> />Suspended account
                </label>
              </div>
            </div>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <div class="col-sm-12">
              <input type="hidden" name="<?= $field_id ?>" value="<?= $id ?>" />
              <a class="btn btn-danger" href="remove_user.php?action=remove&<?= $field_id ?>=<?= $id ?>">Remove user</a>
              <button type="submit" class="btn btn-primary pull-right" name="action" value="update">Update user</button>
            </div>
          </div>
        </div>
        <div class="hidden-xs col-sm-2 col-md-3 col-lg-3"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>
