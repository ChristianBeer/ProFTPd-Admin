<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<?php if (isset($errormsg)) { ?>
  <div class="alert alert-danger" role="alert">
    <p><?= $errormsg ?></p>
  </div>
<?php } ?>
<?php if (isset($warnmsg)) { ?>
  <div class="alert alert-warning" role="alert">
    <p><?= $warnmsg ?></p>
  </div>
<?php } ?>
<?php if (isset($infomsg)) { ?>
  <div class="alert alert-success" role="alert">
    <p><?= $infomsg ?></p>
  </div>
<?php } ?>
</div>


