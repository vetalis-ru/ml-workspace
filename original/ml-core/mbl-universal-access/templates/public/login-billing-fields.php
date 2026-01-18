<div class="woocommerce-billing-fields">
	
	<?php if (wpm_option_is('mblp.existing_clients.surname', 'on') ||
                wpm_option_is('mblp.existing_clients.name', 'on') ||
                wpm_option_is('mblp.existing_clients.patronymic', 'on') ||
                wpm_option_is('mblp.existing_clients.email', 'on') ||
                wpm_option_is('mblp.existing_clients.phone', 'on') ||
                wpm_option_is('mblp.existing_clients.comment', 'on')
             ) { ?>
        <div class="form-fields-group">
            
            <?php if (wpm_option_is('mblp.existing_clients.surname', 'on')) { ?>
                <div class="form-group form-icon form-icon-user">
                    <input type="text"
                           name="billing_last_name"
                           id="billing_last_name"
                           class="form-control"
                           placeholder="Фамилия"
                           required=""
                           autocomplete="family-name"
                    >
                </div>
            <?php } ?>
            
            <?php if (wpm_option_is('mblp.existing_clients.name', 'on')) { ?>
                <div class="form-group form-icon form-icon-user">
                    <input type="text"
                           name="billing_first_name"
                           id="billing_first_name"
                           class="form-control"
                           placeholder="Имя"
                           required=""
                           autocomplete="given-name"
                    >
                </div>
            <?php } ?>
            
            <?php if (wpm_option_is('mblp.existing_clients.patronymic', 'on')) { ?>
                <div class="form-group form-icon form-icon-user">
                    <input type="text"
                           name="patronymic"
                           id="patronymic"
                           class="form-control"
                           placeholder="Отчество"
                           required=""
                           autocomplete="surname"
                    >
                </div>
            <?php } ?>
        
            <?php if (wpm_option_is('mblp.existing_clients.email', 'on')) { ?>
                <div class="form-group form-icon form-icon-email">
                    <input type="email"
                           name="billing_email"
                           id="billing_email"
                           class="form-control"
                           placeholder="Email"
                           required=""
                    >
                </div>
            <?php } ?>
            
            <?php if (wpm_option_is('mblp.existing_clients.phone', 'on')) { ?>
                <div class="form-group form-icon form-icon-phone">
                    <input type="text"
                           name="billing_phone"
                           id="billing_phone"
                           class="form-control"
                           placeholder="Телефон"
                           required=""
                           autocomplete="tel"
                    >
                </div>
            <?php } ?>
        </div>
	<?php } ?>
	
	<?php if (wpm_option_is('mblp.existing_clients.comment', 'on')) { ?>
        <div class="form-fields-group">
            <div class="form-group form-icon form-icon-comment">
                <textarea name="order_comments"
                          id="order_comments"
                          class="form-control"
                          placeholder="<?php echo wpm_get_option('mblp_texts.order_comment', __('Комментарий к заказу', 'mbl_admin')); ?>"
                          rows="6" cols="5"></textarea>
            </div>
        </div>
	<?php } ?>

</div>
