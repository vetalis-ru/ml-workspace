<script>
    $(document).ajaxComplete(function (event, request, settings) {
        
        let message = request.responseJSON.message;
        let email = $(message).data('already-registered');
        if (email) {
            $('.login-form .status').html(`
            	<div class="result alert alert-warning ajax-result" style="display: block;">
            		<?php _e('Этот email уже используется. Введите пароль.', 'mbl'); ?>
				</div>
            `);
            
            $('.login-form [name="username"]').val(email);
            
            $('[aria-controls="login-tab"]').click();
            $('#wpm-register .alert-warning').remove();
        }
    });
</script>