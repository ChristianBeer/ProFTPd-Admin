      </div><!-- /.row -->

      <hr/>
      <footer>
        <center><p style="font-size:x-small">ProFTPd Admin <?php echo $ac->get_version(); ?>  is licensed under GPLv2. See <a href="https://github.com/ChristianBeer/ProFTPd-Admin">github.com/ChristianBeer/ProFTPd-Admin</a> for more information.</p></center>
      </footer>
    </div> <!-- /container -->

    <script src="bootstrap/js/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="bootstrap/js/jqBootstrapValidation.js"></script>
    <script src="bootstrap/js/moment.min.js"></script>
    <script src="bootstrap/js/bootstrap-sortable.js"></script>
    <script src="bootstrap/js/bootstrap-multiselect.js"></script>
    <script src="bootstrap/js/bootstrap-datetimepicker.js"></script>

    <script type="text/javascript">
      $(function () {
        $('#expiration').datetimepicker({
        useCurrent : false,
        showClear: true,
        format:'YYYY-MM-DD HH:mm:00',
	minDate: 'now',
    <?php
      $field_expiration     = $cfg['field_expiration'];
      if (!empty($user[$field_expiration]) && $user[$field_expiration] != '0000-00-00 00:00:00' ) { ?>
        defaultDate: moment('<?php echo $user[$field_expiration]; ?>',"YYYY-MM-DD HH:mm:00"),
    <?php } ?>

        });
      });
    </script>

    <script>
      $(function () {
        $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
        $(".multiselect").multiselect({
          nonSelectedText: 'None selected',
          inheritClass: true,
          buttonWidth: '100%',
          includeSelectAllOption: true,
          enableFiltering: true,
        });
      } );
    </script>

  </body>
</html>
