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

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

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
          <p><span class="form-control"><?php echo $ac->get_group_count(true); ?></span></p>
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
          <p><span class="form-control"><?php echo $ac->get_user_count(true); ?></span></p>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
          <p><a class="btn btn-primary pull-right" href="users.php" role="button">View users &raquo;</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include ("includes/footer.php"); ?>