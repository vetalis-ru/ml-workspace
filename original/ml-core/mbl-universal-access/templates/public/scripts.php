<script>
  jQuery('form[name=wpm-user-register-form]').on('ajax-user-registered-success', function () {
    setTimeout(function () {
      location.href = '<?php echo $redirect_url; ?>';
    }, 2000);
  });
</script>