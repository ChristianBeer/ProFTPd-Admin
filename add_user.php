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

$field_userid     = $cfg['field_userid'];
$field_uid        = $cfg['field_uid'];
$field_ugid       = $cfg['field_ugid'];
$field_ad_gid     = 'ad_gid';
$field_passwd     = $cfg['field_passwd'];
$field_homedir    = $cfg['field_homedir'];
$field_shell      = $cfg['field_shell'];
$field_sshpubkey  = $cfg['field_sshpubkey'];
$field_title      = $cfg['field_title'];
$field_name       = $cfg['field_name'];
$field_company    = $cfg['field_company'];
$field_email      = $cfg['field_email'];
$field_comment    = $cfg['field_comment'];
$field_disabled   = $cfg['field_disabled'];
$field_expiration = $cfg['field_expiration'];

$groups = $ac->get_groups();

if (count($groups) == 0) {
  $errormsg = 'There are no groups in the database; please create at least one group before creating users.';
}

/* Data validation */
if (empty($errormsg) && !empty($_REQUEST["action"]) && $_REQUEST["action"] == "create") {
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
  if ($cfg['max_uid'] != -1 && $cfg['min_uid'] != -1) {
    if ($_REQUEST[$field_uid] > $cfg['max_uid'] || $_REQUEST[$field_uid] < $cfg['min_uid']) {
      array_push($errors, 'Invalid UID; UID must be between ' . $cfg['min_uid'] . ' and ' . $cfg['max_uid'] . '.');
    }
  } else if ($cfg['max_uid'] != -1 && $_REQUEST[$field_uid] > $cfg['max_uid']) {
    array_push($errors, 'Invalid UID; UID must be at most ' . $cfg['max_uid'] . '.');
  } else if ($cfg['min_uid'] != -1 && $_REQUEST[$field_uid] < $cfg['min_uid']) {
    array_push($errors, 'Invalid UID; UID must be at least ' . $cfg['min_uid'] . '.');
  }

  /* gid validation */
  if (empty($_REQUEST[$field_ugid]) || !$ac->is_valid_id($_REQUEST[$field_ugid])) {
    array_push($errors, 'Invalid main group; GID must be a positive integer.');
  }

  /* password length validation */
  if (strlen($_REQUEST[$field_passwd]) < $cfg['min_passwd_length']) {
    array_push($errors, 'Password is too short; minimum length is '.$cfg['min_passwd_length'].' characters.');
  }

  /* home directory validation */
  if (strlen($_REQUEST[$field_homedir]) <= 1) {
    array_push($errors, 'Invalid home directory; home directory cannot be empty.');
  }

  /* shell validation */
  if (strlen($_REQUEST[$field_shell]) <= 1) {
    array_push($errors, 'Invalid shell; shell cannot be empty.');
  }

  /* SSH public key validation */
//  if (strpos($_REQUEST[$field_sshpubkey]) != 0) {
//    array_push($errors, 'Invalid ssh public key; SSH public key must start with "ssh-".');
//  }

  /* user name uniqueness validation */
  if ($ac->check_username($_REQUEST[$field_userid])) {
    array_push($errors, 'User name already exists; name must be unique.');
  }

  /* gid existance validation */
  if (!$ac->check_gid($_REQUEST[$field_ugid])) {
    array_push($errors, 'Main group does not exist; GID cannot be found in the database.');
  }

  /* data validation passed */
  if (count($errors) == 0) {
    $disabled = isset($_REQUEST[$field_disabled]) ? '1':'0';
    $userdata = array($field_userid     => $_REQUEST[$field_userid],
                      $field_uid        => $_REQUEST[$field_uid],
                      $field_ugid       => $_REQUEST[$field_ugid],
                      $field_passwd     => $_REQUEST[$field_passwd],
                      $field_homedir    => $_REQUEST[$field_homedir],
                      $field_shell      => $_REQUEST[$field_shell],
                      $field_sshpubkey  => $_REQUEST[$field_sshpubkey],
                      $field_title      => $_REQUEST[$field_title],
                      $field_name       => $_REQUEST[$field_name],
                      $field_email      => $_REQUEST[$field_email],
                      $field_company    => $_REQUEST[$field_company],
                      $field_comment    => $_REQUEST[$field_comment],
                      $field_expiration => $_REQUEST[$field_expiration],
                      $field_disabled   => $disabled);

    if ($ac->add_user($userdata)) {
      if (isset($_REQUEST[$field_ad_gid])) {
        foreach ($_REQUEST[$field_ad_gid] as $g_key => $g_gid) {
          if (!$ac->is_valid_id($g_gid)) {
            $warnmsg = 'Adding additional group failed; at least one of the additional groups had an invalid GID.';
            continue;
          }
          // XXX: fix error handling here
          $ac->add_user_to_group($_REQUEST[$field_userid], $g_gid);
        }
      }
      $infomsg = 'User "'.$_REQUEST[$field_userid].'" created successfully.';
    } else {
      $errormsg = 'User "'.$_REQUEST[$field_userid].'" creation failed; check log files.';
    }
  } else {
    $errormsg = implode($errors, "<br />\n");
  }
}

/* Form values */
if (isset($errormsg)) {
  /* This is a failed attempt */
  $userid     = $_REQUEST[$field_userid];
  $uid        = $_REQUEST[$field_uid];
  $ugid       = $_REQUEST[$field_ugid];
  $ad_gid     = $_REQUEST[$field_ad_gid];
  $passwd     = $_REQUEST[$field_passwd];
  $expiration = $_REQUEST[$field_expiration];
  $homedir    = $_REQUEST[$field_homedir];
  $shell      = $_REQUEST[$field_shell];
  $sshpubkey  = $_REQUEST[$field_sshpubkey];
  $title      = $_REQUEST[$field_title];
  $name       = $_REQUEST[$field_name];
  $email      = $_REQUEST[$field_email];
  $company    = $_REQUEST[$field_company];
  $comment    = $_REQUEST[$field_comment];
  $disabled   = isset($_REQUEST[$field_disabled]) ? '1' : '0';
} else {
  /* Default values */
  $userid   = "";
  if (empty($cfg['default_uid'])) {
    $uid    = $ac->get_last_uid() + 1;
  } else {
    $uid    = $cfg['default_uid'];
  }

  if (empty($infomsg)) {
    $ugid   = "";
    $ad_gid = array();
    $shell  = "/bin/false";
  } else {
    $ugid   = $_REQUEST[$field_ugid];
    $ad_gid = $_REQUEST[$field_ad_gid];
    $shell  = $_REQUEST[$field_shell];
  }

  $expiration = date("Y-m-d H:i:s", strtotime("+1 month", $time));
  $sshpubkey  = "";
  $passwd     = $ac->generate_random_string((int) $cfg['min_passwd_length']);
  $homedir    = $cfg['default_homedir'];
  $title      = "m";
  $name       = "";
  $email      = "";
  $company    = "";
  $comment    = "";
  $disabled   = '0';
}

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<div class="col-xs-12 col-sm-8 col-md-6 center">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Add user</h3>
    </div>
    <div class="panel-body">
      <div class="row">
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
                <p class="help-block"><small>Positive integer.</small></p>
              </div>
            </div>
            <!-- Main group -->
            <div class="form-group">
              <label for="<?php echo $field_ugid; ?>" class="col-sm-4 control-label">Main group</label>
              <div class="controls col-sm-8">
                <select class="form-control multiselect" id="<?php echo $field_ugid; ?>" name="<?php echo $field_ugid; ?>" required>
                <?php foreach ($groups as $g_gid => $g_group) { ?>
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
                <?php foreach ($groups as $g_gid => $g_group) { ?>
                  <option value="<?php echo $g_gid; ?>" <?php if ($ad_gid && array_key_exists($g_gid, $ad_gid)) { echo 'selected="selected"'; } ?>><?php echo $g_group; ?></option>
                <?php } ?>
                </select>
              </div>
            </div>
            <!-- Password -->
            <div class="form-group">
              <label for="<?php echo $field_passwd; ?>" class="col-sm-4 control-label">Password</label>
              <div class="controls col-sm-8">
                <input type="text" class="form-control" id="<?php echo $field_passwd; ?>" name="<?php echo $field_passwd; ?>" value="<?php echo $passwd; ?>" placeholder="Enter a password" minlength="<?php echo $cfg['min_passwd_length']; ?>" required />
                <p class="help-block"><small>Minimum length <?php echo $cfg['min_passwd_length']; ?> characters.</small></p>
              </div>
            </div>
            <!-- expiration -->
            <div class="form-group">
              <label for="<?php echo $field_expiration; ?>" class="col-sm-4 control-label">Expiry Date</label>
              <div class="controls col-sm-8" >
                <input type="text" class="form-control" id='<?php echo $field_expiration; ?>' name="<?php echo $field_expiration; ?>" value="<?php echo $expiration; ?>" maxlength="19" />
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
            <!-- SSH Public Key -->
            <div class="form-group">
              <label for="<?php echo $field_sshpubkey; ?>" class="col-sm-4 control-label">SSH Public Key</label>
              <div class="controls col-sm-8">
                <textarea class="form-control" id="<?php echo $field_sshpubkey; ?>" name="<?php echo $field_sshpubkey; ?>" rows="9" placeholder="<?php echo $placeholder_sshpubkey; ?>"><?php echo $sshpubkey; ?></textarea>
                <p class="help-block"><small> RFC4716 format </small></p>
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
                <a class="btn btn-default" href="users.php">&laquo; View users</a>
                <button type="submit" class="btn btn-primary pull-right" name="action" value="create">Create user</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include ("includes/footer.php"); ?>
