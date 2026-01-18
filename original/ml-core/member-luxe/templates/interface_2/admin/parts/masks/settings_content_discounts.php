<div id="tab-mbl-discount" class="tab discount-color-content mask-tab">
	<?php wpm_render_partial('masks/mask-discounts', 'admin'); ?>
	<div class="wpm-tab-content">
		<h3><?php _e('Кнопка применить скидку', 'mbl-admin'); ?></h3>
		<div class="row">
			<div class="col col-33">
				<?php wpm_render_partial('options/text-row', 'admin', array('label' => __('Текст кнопки', 'mbl_admin'), 'key' => 'mbl_discount.apply_discount_text')) ?>
				<?php wpm_render_partial('options/text-row', 'admin', array('label' => __('Текст на кнопке применения', 'mbl_admin'), 'key' => 'mbl_discount.apply_text')) ?>
			</div>
			
			<div class="col col-33">
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста:', 'mbl_admin'), 'key' => 'mbl_discount.apply_text_color', 'default' => 'fff', 'main' => true)) ?>
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста при наведении:', 'mbl_admin'), 'key' => 'mbl_discount.apply_hover_text_color', 'default' => 'fff', 'main' => true)) ?>
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста при клике:', 'mbl_admin'), 'key' => 'mbl_discount.apply_active_text_color', 'default' => 'fff', 'main' => true)) ?>
			
			</div>
			
			<div class="col col-33">
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона:', 'mbl_admin'), 'key' => 'mbl_discount.apply_color', 'default' => '3f79dd', 'main' => true)) ?>
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона при наведении:', 'mbl_admin'), 'key' => 'mbl_discount.apply_hover_color', 'default' => '336CDD', 'main' => true)) ?>
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона при клике:', 'mbl_admin'), 'key' => 'mbl_discount.apply_active_color', 'default' => '4380EB', 'main' => true)) ?>
			</div>
		</div>
		<hr>
		
		<h3><?php _e('Промокоды', 'mbl-admin'); ?></h3>
		<div class="row">
			<div class="col col-33">
				<?php wpm_render_partial('options/text-row', 'admin', array('label' => __('Текст названия купонов', 'mbl_admin'), 'key' => 'mbl_discount.coupon_name')) ?>
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет названия купонов:', 'mbl_admin'), 'key' => 'mbl_discount.coupon_name_color', 'default' => '000', 'main' => true)) ?>
			</div>
			
			<div class="col col-33">
				<?php wpm_render_partial('options/text-row', 'admin', array('label' => __('Текст названия скидки', 'mbl_admin'), 'key' => 'mbl_discount.promo_name')) ?>
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет названия скидки:', 'mbl_admin'), 'key' => 'mbl_discount.promo_name_color', 'default' => '000', 'main' => true)) ?>
			</div>
			
			<div class="col col-33">
				<?php wpm_render_partial('options/text-row', 'admin', array('label' => __('Текст отмены купона', 'mbl_admin'), 'key' => 'mbl_discount.remove_coupon_name')) ?>
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет отмены купона:', 'mbl_admin'), 'key' => 'mbl_discount.remove_coupon_name_color', 'default' => '000', 'main' => true)) ?>
			</div>
		</div>
		<hr>
		
		<h3><?php _e('Таймер', 'mbl-admin'); ?></h3>
		<div class="row">
			<div class="col col-33">
				<?php wpm_render_partial('options/text-row', 'admin', array('label' => __('Текст таймера', 'mbl_admin'), 'key' => 'mbl_discount.timer_text')) ?>
			</div>
			
			<div class="col col-33">
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста таймера:', 'mbl_admin'), 'key' => 'mbl_discount.timer_text_color', 'default' => 'fff', 'main' => true)) ?>
			</div>
			
			<div class="col col-33">
				<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет фона таймера:', 'mbl_admin'), 'key' => 'mbl_discount.timer_bg_color', 'default' => '000', 'main' => true)) ?>
			</div>
		
		</div>
		
		<?php wpm_render_partial('settings-save-button', 'common'); ?>
	</div>
</div>

<style>
	#tab-mbl-discount .row {
		display: flex;
	}
	#tab-mbl-discount .row .col:not(:last-child) {
		margin-right: 15px;
	}
	#tab-mbl-discount .row .col-33 {
		min-width: calc(33% - 10px);
	}
	#tab-mbl-discount .row input {
		width: 100%;
	}
	#tab-mbl-discount h3 {
		margin-bottom: 0;
	}
	#tab-mbl-discount h3:first-child {
		margin-top: 0;
	}
</style>