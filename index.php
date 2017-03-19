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

include ("includes/header.php");
include ("includes/messages.php");


if (
    (
      (!isset($session_usage) || $session_usage !== true) &&
      (!isset($session_valid) || $session_valid !== true)
    ) || (
      isset($session_usage) && $session_usage === true &&
      isset($session_valid) && $session_valid === true
    )
  ) {
  ?>
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Groups</h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-xs-8 col-sm-7 col-md-6">
            <p>Groups in database:</p>
          </div>
          <div class="col-xs-4 col-sm-5 col-md-6">
            <p><span class="form-control"><?php echo $ac->get_group_count(); ?></span></p>
          </div>
          <div class="col-xs-8 col-sm-7 col-md-6">
            <p>Empty groups in database:</p>
          </div>
          <div class="col-xs-4 col-sm-5 col-md-6">
            <p><span class="form-control"><?php echo $ac->get_group_count(TRUE); ?></span></p>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12">
            <p><a class="btn btn-primary pull-right" href="groups.php" role="button">View groups &raquo;</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Users</h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-xs-8 col-sm-7 col-md-6">
            <p>Users in database:</p>
          </div>
          <div class="col-xs-4 col-sm-5 col-md-6">
            <p><span class="form-control"><?php echo $ac->get_user_count(); ?></span></p>
          </div>
          <div class="col-xs-8 col-sm-7 col-md-6">
            <p>Deactivated users in database:</p>
          </div>
          <div class="col-xs-4 col-sm-5 col-md-6">
            <p><span class="form-control"><?php echo $ac->get_user_count(TRUE); ?></span></p>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12">
            <p><a class="btn btn-primary pull-right" href="users.php" role="button">View users &raquo;</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
}
else {
  ?>
  <div class="col-md-6 col-md-offset-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          Login
        </h3>
      </div>
      <div class="panel-body">

        <form action="index.php" method="post">
          <div class="form-group">
            <div class="input-group">
              <label for="username" class="input-group-addon"><span class="glyphicon glyphicon-user"><span class="text-hide" aria-hidden="true">Username</span></label>
              <input type="text" class="form-control" id="username" name="username" placeholder="Username">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
              <label for="password" class="input-group-addon"><span class="glyphicon glyphicon-lock"><span class="text-hide" aria-hidden="true">Password</span></label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group pull-right">
              <button type="submit" class="btn btn-primary" name="login" id="login" value="login">Sign in</button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
<?php
}

include ("includes/footer.php");
?>
