<div id="tab-mmini-options" class="tab mmini-color-content mask-tab">
	<?php wpm_render_partial('masks/mask-mmini', 'admin'); ?>
	<div class="wpm-tab-content mblr_autoreg">
		<div class="wpm-control-row">
			<label>
				<input type="checkbox"
				       name="main_options[mblmi][on]"
				       checked
				>
				&nbsp;<?php _e('Включить/Выключить', 'mbl_admin') ?>
			</label><br>
		</div>
		<?php wpm_render_partial('settings-save-button', 'common'); ?>
	</div>
</div>