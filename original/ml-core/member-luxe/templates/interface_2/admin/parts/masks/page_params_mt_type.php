<dd class="mbl-test_homework-type" id="mbl-test-homework-type-selector">
	<label for="homework_type_question">
		<input type="radio" name="page_meta[homework_type]" id="homework_type_question" value="question" checked>
		<?php _e('Вопрос', 'mbl_admin'); ?>
		<i class="fa fa-question-circle"></i>
	</label>
	
	<label for="homework_type_tests" id="mbl-test-label">
		<input type="radio" name="page_meta[homework_type]" id="homework_type_tests" value="test">
		<?php _e('Тест', 'mbl_admin'); ?>
		<i class="fa fa-list-alt"></i>
	</label>
	<input type="hidden" name="page_meta[homework_type]" id="homework_type_question" value="question">
</dd>

<script>
    jQuery(function ($) {
        const test =$('#mbl-test-create');

        //toggle between tst and question
        $(document).on('change', '#mbl-test-homework-type-selector input[type="radio"]', function () {
            if ($(this).val() == 'test') {
                test.removeClass('hidden')
            } else {
                test.addClass('hidden')
            }
        });

    });
</script>
<style>
	.mbl-test_homework-type {
		margin-left: 0;
		margin-bottom: 20px;
		display: flex;
		flex-wrap: wrap;
	}
	.mbl-test_homework-type label:first-child {
		margin-right: 20px;
		margin-bottom: 15px;
	}
	#mbl-test-label {
		padding: 9px !important;
		box-shadow: #A49074 4px 4px 0 0 !important;
		border: 1px solid #6A6F73 !important;
		margin: -10px !important;
		height: 19px !important;
		background-color: #FFFDFB !important;
	}
	#mbl-test-create:not(.hidden) ~ dt {
		display: none;
	}
	#mbl-test-create {
		padding: 15px;
	}
	#homework_options {
		overflow-x: auto;
	}
	#poststuff #mbl-test-create .mask_content  h2 {
		font-size: 1.3em;
		padding: 0;
		margin: 1em 0;
		font-weight: 600;
	}
	#homework_options dt .mask_content {
		font-weight: normal;
	}
	#mbl-test-create .add-question-row {
		margin-top: 20px;
		padding: 15px 40px;
	}
	#mbl-test-create .add-question-row > *:first-child {
		margin-left: auto;
	}
</style>