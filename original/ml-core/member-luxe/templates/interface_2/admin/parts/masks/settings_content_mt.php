<div id="tab-mt-options" class="tab mt-color-content mask-tab">
	<?php wpm_render_partial('masks/mask-mt', 'admin'); ?>
	<div class="wpm-tab-content mblr_autoreg">
        <div class="wpm-control-row">
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет номера вопроса', 'mbl_admin'), 'key' => 'mt_design.question_number_color', 'default' => '4a4a4a', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста вопроса', 'mbl_admin'), 'key' => 'mt_design.question_text_color', 'default' => '4a4a4a', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста "Варианты ответов:"', 'mbl_admin'), 'key' => 'mt_design.text_variants_color', 'default' => '4a4a4a', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет номера варианта ответа', 'mbl_admin'), 'key' => 'mt_design.answer_number_color', 'default' => '4a4a4a', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет селектора', 'mbl_admin'), 'key' => 'mt_design.checkbox_color', 'default' => '3d84e6', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста варианта ответа', 'mbl_admin'), 'key' => 'mt_design.answer_text_color', 'default' => '4a4a4a', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконки "Ваш вариант ответа"', 'mbl_admin'), 'key' => 'mt_design.you_answer_icon_color', 'default' => '4a4a4a', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста "Ваш вариант ответа"', 'mbl_admin'), 'key' => 'mt_design.you_answer_text_color', 'default' => '4a4a4a', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет линии между вопросами', 'mbl_admin'), 'key' => 'mt_design.line_color', 'default' => 'e3e3e3', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет иконки “ОПИСАНИЕ ТЕСТА” и “ПРОЙДИТЕ ТЕСТ”', 'mbl_admin'), 'key' => 'mt_design.test_desc_icon_color', 'default' => '000', 'main' => true)) ?>
			<?php wpm_render_partial('options/color-row', 'admin', array('label' => __('Цвет текста “ОПИСАНИЕ ТЕСТА” и “ПРОЙДИТЕ ТЕСТ”', 'mbl_admin'), 'key' => 'mt_design.test_desc_text_color', 'default' => '000', 'main' => true)) ?>
        </div>
		<?php wpm_render_partial('settings-save-button', 'common'); ?>
	</div>
</div>