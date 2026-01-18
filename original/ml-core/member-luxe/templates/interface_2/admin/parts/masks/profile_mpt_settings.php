<table id="mbla_coach_access_holder" class="form-table">
	<tbody>
	<tr id="mbla_coach_access">
		<th><label><?php _e('Проверка заданий', 'mbl'); ?></label></th>
		<td>
<!--		<td rowspan="2">-->
			<div class="mpt-color-content mask-tab" style="padding: 15px">
				<?php wpm_render_partial('masks/mask-mpt', 'admin'); ?>
				<div class="">
					<div class="mbla-autotraining-config-row mbla-bold" id="mbla_coach_access_all">
						<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Все автотренинги', 'mbl_admin'), 'name' => '', 'value' => 'on')) ?>
					</div>
				</div>
			</div>
		</td>
	</tr>

	<tr id="mbla_coach_stats">
		<th><label><?php _e('Статистика ДЗ', 'mbl'); ?></label></th>
		<td>
			<div class="mpt-color-content mask-tab" style="padding: 15px">
				<?php wpm_render_partial('masks/mask-mpt', 'admin'); ?>
				<dl class="">
					<div class="mbla-autotraining-config-row mbla-bold" id="mbla_coach_access_stats">
						<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Разрешить просмотр статистики ДЗ', 'mbl_admin'), 'name' => '', 'value' => 'on')) ?>
					</div>
					<div class="mbla-autotraining-config-row">
						<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Запретить просмотр статистики других тренеров', 'mbl_admin'), 'name' => '', 'value' => 'on')) ?>
					</div>
				</dl>
			</div>
		</td>
	</tr>
	</tbody>

</table>
