<?php
$custom_links = range(0, 6);
?>

<div id="navpanel_options_tab_3" class="wpm-tab-content">
    <div class="telegram_settings">
	    <?php wpm_render_partial( 'fields/checkbox', 'admin',
		    array(
			    'label' => __( 'Скрыть профиль в меню', 'mbl_admin' ),
			    'name'  => 'main_options[mbl_access][enable_profile_menu_link]',
			    'value' => wpm_get_option( 'mbl_access.enable_profile_menu_link', 'off'  )
		    ) )
        ?>
	    <?php wpm_render_partial( 'fields/checkbox', 'admin',
		    array(
			    'label' => __( 'Скрыть Активацию', 'mbl_admin' ),
			    'name'  => 'main_options[mbl_access][enable_activation_menu_link]',
			    'value' => wpm_get_option( 'mbl_access.enable_activation_menu_link', 'off' )
		    ) )
        ?>
        <div class="row" style="margin-top: 15px;">
            <div class="col-1">
                <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Включить пункт меню', 'mbl_admin'), 'name' => 'main_options[mbl_access][enable_partner_program]', 'value' => wpm_get_option('mbl_access.enable_partner_program') )) ?>
            </div>
            <div class="col-2">
	            <?php wpm_render_partial('options/icon', 'admin', array('label' => __('Иконка', 'mbl_admin'), 'key' => 'mbl_access.partner_link_icon' )) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-1">
                <?php wpm_render_partial('options/text-row', 'admin', array('label' => __('Название пункта меню', 'mbl_admin'), 'key' => 'mbl_access.partner_link_name')) ?>
            </div>
            <div class="col-2" style="padding: 0 5px">
                <?php wpm_render_partial('options/text-row', 'admin', array('label' => __('URL', 'mbl_admin'), 'key' => 'mbl_access.partner_link_url')) ?>
            </div>
        </div>
        <hr>
    </div>

    <div class="telegram_settings">
        <?php foreach ($custom_links as $i => $link_item) : ?>
            <div class="row">
                <div class="col-1">
                    <?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Включить пункт меню', 'mbl_admin'), 'name' => 'main_options[mbl_access][enable_custom_link]' . '[' . $link_item .  ']', 'value' => wpm_get_option('mbl_access.enable_custom_link.' . $link_item) )) ?>
                </div>
                <div class="col-2">
	                <?php wpm_render_partial('options/icon', 'admin', array('label' => __('Иконка', 'mbl_admin'), 'key' => 'mbl_access.custom_link_icon.' . $link_item )) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-1">
                    <?php wpm_render_partial('options/text-row', 'admin', array('label' => __('Название пункта меню', 'mbl_admin'), 'key' => 'mbl_access.custom_link_name.' . $link_item)) ?>
                </div>
                <div class="col-2" style="padding: 0 5px">
                    <?php wpm_render_partial('options/text-row', 'admin', array('label' => __('URL', 'mbl_admin'), 'key' => 'mbl_access.custom_link_url.' . $link_item)) ?>
                </div>
            </div>
            <?php if ($i < count($custom_links) - 1): ?> <hr> <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="wpm-tab-footer">
        <button type="submit" class="button button-primary wpm-save-options">Сохранить</button>
        <span class="buttom-preloader"></span>
    </div>
</div>
