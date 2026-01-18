<div id="mbla_autotraining_options" class="mbl_autotraining_first_material mask-tab ">
	<?php wpm_render_partial('masks/mask-mpt', 'admin'); ?>
	<dl>
		<dt>
			<h4>
				<label>
					<input type="hidden" name="term_meta[add_level_after_finish]" value="off">
					<input
						type="checkbox"
						name="term_meta[add_level_after_finish]"
						value="on"
						checked
					>
					<?php _e('Добавить уровень доступа после завершения автотренига','mbl_admin');?>:</label>
			</h4>
		</dt>
		<dd>
			<?php _e('Уровень доступа', 'mbl_admin') ?>
			<select name="term_meta[add_level_term_key]">
			
			</select>
			<p class="form-field">
				<label for="duration-manual"><?php _e('Время действия', 'mbl_admin'); ?></label>
				
				<span class="wrap">
                   <input
	                   name="term_meta[add_level_duration]"
	                   type="number"
	                   size="2"
	                   min="1"
	                   max="99"
	                   value="12"
	                   maxlength="2"
	                   style="width: 50px">
                   <select name="term_meta[add_level_duration_units]" style="margin-left: 5px; margin-top: 0">
                        <option value="months" ><?php _e('месяцев', 'mbl_admin'); ?></option>
                    </select>
               </span>
			</p>
		</dd>
	</dl>
</div>