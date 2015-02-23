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

$groups = $ac->get_groups();

include ("includes/header.php");
?>
<?php include ("includes/messages.php"); ?>

<?php if(empty($groups)) { ?>
<div class="col-sm-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Groups</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            <p>Currently there are no groups available.</p>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <a class="btn btn-primary pull-right" href="add_group.php" role="button">Add group &raquo;</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } else { ?>
<div class="col-sm-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Groups</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-12 col-md-10 col-lg-8 center">
          <!-- Group table -->
          <div class="form-group">
            <table class="table table-striped table-condensed sortable">
              <thead>
                <th>GID</th>
                <th>Group</th>
                <th class="hidden-sm hidden-md hidden-lg">Users</th>
                <th class="hidden-xs">Main users</th>
                <th class="hidden-xs">Additional users</th>
                <th data-defaultsort="disabled"></th>
              </thead>
              <tbody>
                <?php while (list($g_gid, $g_group) = each($groups)) {
                  $n_main = $ac->get_user_count_by_gid($g_gid); 
                  $n_add = $ac->get_user_add_count_by_gid($g_gid); ?>
                  <tr>
                    <td class="pull-middle"><?= $g_gid ?></td>
                    <td class="pull-middle"><a href="edit_group.php?action=show&<?= $cfg['field_gid'] ?>=<?= $g_gid ?>"><?= $g_group ?></a></td>
                    <td class="pull-middle hidden-sm hidden-md hidden-lg"><?= $n_main + $n_add ?></td>
                    <td class="pull-middle hidden-xs"><?= $n_main ?></td>
                    <td class="pull-middle hidden-xs"><?= $n_add ?></td>
                    <td class="pull-middle">
                      <div class="btn-toolbar pull-right" role="toolbar">
                        <a class="btn-group" role="group" href="edit_group.php?action=show&<?= $cfg['field_gid'] ?>=<?= $g_gid ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                        <a class="btn-group" role="group" href="remove_group.php?action=remove&<?= $cfg['field_gid'] ?>=<?= $g_gid ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <!-- Actions -->
          <div class="form-group">
            <a class="btn btn-primary pull-right" href="add_group.php" role="button">Add group &raquo;</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include ("includes/footer.php"); ?>