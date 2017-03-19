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
          <a class="navbar-brand" href="index.php">ProFTPd Admin</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
<?php
  $uri = $_SERVER['REQUEST_URI'];
  $nav = ''
    //. '<li' . ( (strpos($uri, 'index.php') !== false) ? ' class="active"': '' ) . '><a href="index.php">Home</a></li>'
    . '<li' . ( (strpos($uri, 'groups.php') !== false) ? ' class="active"': '' ) . '><a href="groups.php">Groups</a></li>'
    . '<li' . ( (strpos($uri, 'users.php') !== false) ? ' class="active"': '' ) . '><a href="users.php">Users</a></li>'
    . '<li' . ( (strpos($uri, 'add_group.php') !== false) ? ' class="active"': '' ) . '><a href="add_group.php">Add Group</a></li>'
    . '<li' . ( (strpos($uri, 'add_user.php') !== false) ? ' class="active"': '' ) . '><a href="add_user.php">Add User</a></li>';
  if (isset($session_valid) && $session_valid === true) {
    $nav .= '<li><a href="index.php?logout">Logout</a></li>';
  }
  if (
    (
      (!isset($session_usage) || $session_usage !== true) &&
      (!isset($session_valid) || $session_valid !== true)
    ) || (
      isset($session_usage) && $session_usage === true &&
      isset($session_valid) && $session_valid === true
    )
  ) {
    echo $nav;
  }
?>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div>
    </nav>

    <div class="container">
      <div class="row">
