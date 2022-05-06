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

$field_userid   = $cfg['field_userid'];
$field_id       = $cfg['field_id'];
$field_uid      = $cfg['field_uid'];
$field_ugid     = $cfg['field_ugid'];
$field_ad_gid   = 'ad_gid';
$field_passwd   = $cfg['field_passwd'];
$field_passwd2  = $cfg['field_passwd2'];
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

/* find the right message for uid */
$uidMessage = $ac->get_uid_message();

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
    $ugid = $user[$field_ugid];
    $group = $ac->get_group_by_gid($ugid);
    if (!$group) {
      $warnmsg = 'Main group does not exist; cannot find GID '.$ugid.' in the database.';
    }
    $ad_gid = $ac->parse_groups($userid);
  }
}

if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "update") {
  $errors = array();
  /* user id validation */
  if (empty($_REQUEST[$field_userid])
      || !preg_match($cfg['userid_regex'], $_REQUEST[$field_userid])
      || strlen($_REQUEST[$field_userid]) > $cfg['max_userid_length']) {
    array_push($errors, 'Invalid user name; user name must contain only letters, numbers, hyphens, and underscores with a maximum of '.$cfg['max_userid_length'].' characters.');
  }
  /* uid validation */
  if (empty($_REQUEST[$field_uid]) || !$ac->is_valid_id($_REQUEST[$field_uid])) {
    array_push($errors, 'Invalid UID; must be a positive integer.');
  }
  if (($cfg['max_uid'] != -1 && $_REQUEST[$field_uid] > $cfg['max_uid']) or ($cfg['min_uid'] != -1 && $_REQUEST[$field_uid] < $cfg['min_uid'])) {
    array_push($errors, 'Invalid UID; '.$uidMessage );
  }
  /* gid validation */
  if (empty($_REQUEST[$field_ugid]) || !$ac->is_valid_id($_REQUEST[$field_ugid])) {
    array_push($errors, 'Invalid main group; GID must be a positive integer.');
  }
  /* password length validation */
  if (strlen($_REQUEST[$field_passwd]) > 0 && strlen($_REQUEST[$field_passwd]) < $cfg['min_passwd_length']) {
    array_push($errors, 'Password is too short; minimum length is '.$cfg['min_passwd_length'].' characters.');
  }
  /* password confirmation validation */
  if ($_REQUEST[$field_passwd] != $_REQUEST[$field_passwd2]) {
    array_push($errors, 'Passwords are not matching');
}

  /* home directory validation */
  if (strlen($_REQUEST[$field_homedir]) <= 1) {
    array_push($errors, 'Invalid home directory; home directory cannot be empty.');
  }
  /* shell validation */
  if (strlen($_REQUEST[$field_shell]) <= 1) {
    array_push($errors, 'Invalid shell; shell cannot be empty.');
  }
  /* user name uniqueness validation */
  if ($userid != $_REQUEST[$field_userid] && $ac->check_username($_REQUEST[$field_userid])) {
    array_push($errors, 'User name already exists; name must be unique.');
  }
  /* gid existance validation */
  if (!$ac->check_gid($_REQUEST[$field_ugid])) {
    array_push($errors, 'Main group does not exist; GID '.$_REQUEST[$field_ugid].' cannot be found in the database.');
  }
  /* data validation passed */
  if (count($errors) == 0) {
    /* remove all groups */
    while (list($g_gid, $g_group) = each($groups)) {
      if (!$ac->remove_user_from_group($userid, $g_gid)) {
        array_push($errors, 'Cannot remove user "'.$userid.'" from group "'.$g_group.'"; see log files for more information.');
        break;
      }
    }
  }
  if (count($errors) == 0) {
    /* update user */
    $disabled = isset($_REQUEST[$field_disabled]) ? '1':'0';
    $userdata = array($field_id       => $_REQUEST[$field_id],
                      $field_userid   => $_REQUEST[$field_userid],
                      $field_uid      => $_REQUEST[$field_uid],
                      $field_ugid     => $_REQUEST[$field_ugid],
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
  } else {
    $errormsg = implode($errors, "<br />\n");
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
  $ugid     = $user[$field_ugid];
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
  $ugid     = $_REQUEST[$field_ugid];
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
<div class="col-xs-12 col-sm-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        <a data-toggle="collapse" href="#userstats" aria-expanded="true" aria-controls="userstats">User statistics</a>
      </h3>
    </div>
    <div class="panel-body collapse in" id="userstats" aria-expanded="true">
      <div class="col-sm-12">
        <form role="form" class="form-horizontal" method="post" data-toggle="validator">
          <!-- Login count (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_login_count; ?>" class="col-sm-4 control-label">Login count</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_login_count; ?>" name="<?php echo $field_login_count; ?>" value="<?php echo $user[$field_login_count]; ?>" readonly />
            </div>
          </div>
          <!-- Last login (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_last_login; ?>" class="col-sm-4 control-label">Last login</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_last_login; ?>" name="<?php echo $field_last_login; ?>" value="<?php echo $user[$field_last_login]; ?>" readonly />
            </div>
          </div>
          <!-- Last modified (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_last_modified; ?>" class="col-sm-4 control-label">Last modified</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_last_modified; ?>" name="<?php echo $field_last_modified; ?>" value="<?php echo $user[$field_last_modified]; ?>" readonly />
            </div>
          </div>
          <!-- Bytes in (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_bytes_in_used; ?>" class="col-sm-4 control-label">Bytes uploaded</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_bytes_in_used; ?>" name="<?php echo $field_bytes_in_used; ?>" value="<?php echo sprintf("%2.1f", $user[$field_bytes_in_used] / 1048576); ?> MB" readonly />
            </div>
          </div>
          <!-- Bytes out (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_bytes_out_used; ?>" class="col-sm-4 control-label">Bytes downloaded</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_bytes_out_used; ?>" name="<?php echo $field_bytes_out_used; ?>" value="<?php echo sprintf("%2.1f", $user[$field_bytes_out_used] / 1048576); ?> MB" readonly />
            </div>
          </div>
          <!-- Files in (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_files_in_used; ?>" class="col-sm-4 control-label">Files uploaded</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_files_in_used; ?>" name="<?php echo $field_files_in_used; ?>" value="<?php echo $user[$field_files_in_used]; ?>" readonly />
            </div>
          </div>
          <!-- Files out (readonly) -->
          <div class="form-group">
            <label for="<?php echo $field_files_out_used; ?>" class="col-sm-4 control-label">Files downloaded</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_files_out_used; ?>" name="<?php echo $field_files_out_used; ?>" value="<?php echo $user[$field_files_out_used]; ?>" readonly />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Edit panel -->
<div class="col-xs-12 col-sm-6">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">
        <a data-toggle="collapse" href="#userprops" aria-expanded="true" aria-controls="userprops">User properties</a>
      </h3>
    </div>
    <div class="panel-body collapse in" id="userprops" aria-expanded="true">
      <div class="col-sm-12">
        <form role="form" class="form-horizontal" method="post" data-toggle="validator">
          <!-- User name -->
          <div class="form-group">
            <label for="<?php echo $field_userid; ?>" class="col-sm-4 control-label">User name</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_userid; ?>" name="<?php echo $field_userid; ?>" value="<?php echo $userid; ?>" placeholder="Enter a user name" maxlength="<?php echo $cfg['max_userid_length']; ?>" pattern="<?php echo substr($cfg['userid_regex'], 2, -3); ?>" required />
              <p class="help-block"><small>Only letters, numbers, hyphens, and underscores. Maximum <?php echo $cfg['max_userid_length']; ?> characters.</small></p>
            </div>
          </div>
          <!-- UID -->
          <div class="form-group">
            <label for="<?php echo $field_uid; ?>" class="col-sm-4 control-label">UID</label>
            <div class="controls col-sm-8">
              <input type="number" class="form-control" id="<?php echo $field_uid; ?>" name="<?php echo $field_uid; ?>" value="<?php echo $uid; ?>" min="1" placeholder="Enter a UID" required />
              <p class="help-block"><small><?php echo $uidMessage; ?></small></p>
            </div>
          </div>
          <!-- Main group -->
          <div class="form-group">
            <label for="<?php echo $field_ugid; ?>" class="col-sm-4 control-label">Main group</label>
            <div class="controls col-sm-8">
              <select class="form-control multiselect" id="<?php echo $field_ugid; ?>" name="<?php echo $field_ugid; ?>" required>
              <?php reset ($groups); while (list($g_gid, $g_group) = each($groups)) { ?>
                <option value="<?php echo $g_gid; ?>" <?php if ($ugid == $g_gid) { echo 'selected="selected"'; } ?>><?php echo $g_group; ?></option>
              <?php } ?>
              </select>
            </div>
          </div>
          <!-- Additional groups -->
          <div class="form-group">
            <label for="<?php echo $field_ad_gid; ?>" class="col-sm-4 control-label">Additional groups</label>
            <div class="controls col-sm-8">
              <select class="form-control multiselect" id="<?php echo $field_ad_gid; ?>" name="<?php echo $field_ad_gid; ?>[]" multiple="multiple">
              <?php reset ($groups); while (list($g_gid, $g_group) = each($groups)) { ?>
                <option value="<?php echo $g_gid; ?>" <?php if (array_key_exists($g_gid, $ad_gid)) { echo 'selected="selected"'; } ?>><?php echo $g_group; ?></option>
              <?php } ?>
              </select>
            </div>
          </div>
          <!-- Password -->
          <div class="form-group">
            <label for="<?php echo $field_passwd; ?>" class="col-sm-4 control-label">Password</label>
            <div class="controls col-sm-8">
              <input type="password" class="form-control" id="<?php echo $field_passwd; ?>" name="<?php echo $field_passwd; ?>" value="" placeholder="Change password" />
              <p class="help-block"><small>Minimum length <?php echo $cfg['min_passwd_length']; ?> characters.</small></p>
            </div>
          </div>
          <!-- Password confirmation -->
          <div class="form-group">
            <label for="<?php echo $field_passwd2; ?>" class="col-sm-4 control-label">Confirm password</label>
            <div class="controls col-sm-8">
              <input type="password" class="form-control" id="<?php echo $field_passwd2; ?>" name="<?php echo $field_passwd2; ?>" value="" placeholder="Confirm password" />
            </div>
          </div>
          <!-- Home directory -->
          <div class="form-group">
            <label for="<?php echo $field_homedir; ?>" class="col-sm-4 control-label">Home directory</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_homedir; ?>" name="<?php echo $field_homedir; ?>" value="<?php echo $homedir; ?>" placeholder="Enter a home directory" />
            </div>
          </div>
          <!-- Shell -->
          <div class="form-group">
            <label for="<?php echo $field_shell; ?>" class="col-sm-4 control-label">Shell</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_shell; ?>" name="<?php echo $field_shell; ?>" value="<?php echo $shell; ?>" placeholder="Enter the user's shell" />
            </div>
          </div>
          <!-- Title -->
          <div class="form-group">
            <label for="<?php echo $field_title; ?>" class="col-sm-4 control-label">Title</label>
            <div class="col-sm-8">
              <select class="form-control" id="<?php echo $field_title; ?>" name="<?php echo $field_title; ?>" required>
                <option value="m" <?php if ($title == 'm') { echo 'selected="selected"'; } ?>>Mr.</option>
                <option value="f" <?php if ($title == 'f') { echo 'selected="selected"'; } ?>>Ms.</option>
              </select>
            </div>
          </div>
          <!-- Real name -->
          <div class="form-group">
            <label for="<?php echo $field_name; ?>" class="col-sm-4 control-label">Name</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" value="<?php echo $name; ?>" placeholder="Enter the user's real name" />
            </div>
          </div>
          <!-- Email -->
          <div class="form-group">
            <label for="<?php echo $field_email; ?>" class="col-sm-4 control-label">E-mail</label>
            <div class="controls col-sm-8">
              <input type="email" class="form-control" id="<?php echo $field_email; ?>" name="<?php echo $field_email; ?>" value="<?php echo $email; ?>" placeholder="Enter the user's email" />
            </div>
          </div>
          <!-- Company -->
          <div class="form-group">
            <label for="<?php echo $field_company; ?>" class="col-sm-4 control-label">Company</label>
            <div class="controls col-sm-8">
              <input type="text" class="form-control" id="<?php echo $field_company; ?>" name="<?php echo $field_company; ?>" value="<?php echo $company; ?>" placeholder="Enter a company or department" />
            </div>
          </div>
          <!-- Comment -->
          <div class="form-group">
            <label for="<?php echo $field_comment; ?>" class="col-sm-4 control-label">Comment</label>
            <div class="controls col-sm-8">
              <textarea class="form-control" id="<?php echo $field_comment; ?>" name="<?php echo $field_comment; ?>" rows="3" placeholder="Enter a comment or additional information about the user"><?php echo $comment; ?></textarea>
            </div>
          </div>
          <!-- Suspended -->
          <div class="form-group">
            <label for="<?php echo $field_disabled; ?>" class="col-sm-4 control-label">Status</label>
            <div class="controls col-sm-8">
              <div class="checkbox">
                <label>
                  <input type="checkbox" id="<?php echo $field_disabled; ?>" name="<?php echo $field_disabled; ?>" <?php if ($disabled) { echo 'checked="checked"'; } ?> />Suspended account
                </label>
              </div>
            </div>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <div class="col-sm-12">
              <input type="hidden" name="<?php echo $field_id; ?>" value="<?php echo $id; ?>" />
              <a class="btn btn-danger" href="remove_user.php?action=remove&<?php echo $field_id; ?>=<?php echo $id; ?>">Remove user</a>
              <button type="submit" class="btn btn-primary pull-right" name="action" value="update">Update user</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>
