<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ProFTPd Admin</title>
    <link rel="stylesheet" type="text/css" media="screen" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="bootstrap/css/bootstrap-sortable.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="bootstrap/css/bootstrap-multiselect.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/admin.css" />
  </head>

  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="/index.php">ProFTPd Admin</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li <?php if (strpos($_SERVER['REQUEST_URI'], 'index.php') !== FALSE) { ?>class="active"<?php } ?>><a href="index.php">Home</a></li>
            <li <?php if (strpos($_SERVER['REQUEST_URI'], 'groups.php') !== FALSE) { ?>class="active"<?php } ?>><a href="groups.php">Groups</a></li>
            <li <?php if (strpos($_SERVER['REQUEST_URI'], 'users.php') !== FALSE) { ?>class="active"<?php } ?>><a href="users.php">Users</a></li>
            <li <?php if (strpos($_SERVER['REQUEST_URI'], 'add_group.php') !== FALSE) { ?>class="active"<?php } ?>><a href="add_group.php">Add group</a></li>
            <li <?php if (strpos($_SERVER['REQUEST_URI'], 'add_user.php') !== FALSE) { ?>class="active"<?php } ?>><a href="add_user.php">Add user</a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div>
    </nav>

    <div class="container">
      <div class="row">
