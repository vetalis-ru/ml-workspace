<div id="tab-mpt-options" class="tab mpt-color-content mask-tab">
	<?php wpm_render_partial('masks/mask-mpt', 'admin'); ?>
	<div class="wpm-tab-content">
        <div class="mbla-bold" id="mbla_coach_access_stats" style="font-weight: bold">
			<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Разрешить просмотр статистики ДЗ всем Тренерам', 'mbl_admin'), 'name' => '', 'value'=>'' )) ?>
        </div>
        <div class="disabled_field">
			<?php wpm_render_partial('fields/checkbox', 'admin', array('label' => __('Запретить всем Тренерам просмотр статистики других тренеров', 'mbl_admin'), 'name' => '', 'value'=>'')) ?>
        </div>
		<?php wpm_render_partial('settings-save-button', 'common'); ?>
	</div>
</div>