<div id="mbl_inner_tab_mblp_icons" class="wpm-tab-content">
	
	<?php if ( !empty($available_gateways) ) : ?>
    
        <ul class="">
            <?php foreach ($available_gateways as $gateway) {
                $payments_id = esc_attr($gateway->id);
                $img_src = wpm_get_option( 'mblp_payments_icon.' . $payments_id );
                ?>
                <li class="payment_methods_icons">
                    <button type="button"
                            class="wpm-media-upload-button button"
                            data-id="payments-icon-<?php echo $payments_id; ?>"
                    >
                        <?php _e('Задать', 'mbl-admin'); ?>
                    </button>
                    
                    &nbsp;&nbsp; <a id="delete-wpm-background"
                                        class="wpm-delete-media-button button submit-delete"
                                        data-id="payments-icon-<?php echo $payments_id; ?>"><?php _e('Удалить', 'mbl_admin') ?></a>
                    &nbsp;&nbsp; 
                    
                    <div class="wpm-payments-icon-<?php echo $payments_id; ?>-preview-wrap">
                        <div class="wpm-payments-icon-<?php echo $payments_id; ?>-preview-box preview-box">
                            <img src="<?php echo $img_src; ?>" alt="" id="wpm-payments-icon-<?php echo $payments_id; ?>-preview">
                        </div>
                    </div>
                    
                    <input type="text"
                           id="wpm_payments-icon-<?php echo $payments_id; ?>"
                           name="main_options[mblp_payments_icon][<?php echo $payments_id; ?>]"
                           value="<?php echo $img_src; ?>"
                    />
                    
                    &nbsp;&nbsp; 
                    
                    <?php echo $gateway->get_title(); ?>
                    
                </li>
            <?php } ?>
        </ul>
    <?php endif; ?>
	
	<?php wpm_render_partial('settings-save-button', 'common'); ?>
</div>
