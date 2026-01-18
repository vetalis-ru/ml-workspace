<?php
	global $user_identity, $user_email, $user_ID, $current_user;

    $levels = wpm_get_all_levels();
    $plain_levels = get_terms('wpm-levels', array());
	$main_options = get_option('wpm_main_options');

	if (!isset($send_targets)) {
		$send_targets = array();
	}

	if (!isset($mail_format)) {
		$mail_format = 'html';
	}

	if (!isset($subject)) {
		$subject = '';
	}

	if (!isset($mail_content)) {
		$mail_content = '';
	}

	if (!isset($mail_content)) {
		$mail_content = '';
	}

    wp_get_current_user();

	$from_name = $current_user->display_name;
    $from_name = empty($from_name) ? get_bloginfo('name') : $from_name;
	$from_address = wpm_ses_is_on() ? $main_options['letters']['ses_email'] :$user_email;
?>

<div class="wrap">
	<div id="icon-users" class="icon32"><br/></div>
	<h2><?php _e('Рассылка'); ?></h2>

	<?php 	if (isset($err_msg) && $err_msg!='') { ?>
			<div class="error fade"><p><?php echo $err_msg; ?><p></div>
			<p><?php _e('Пожалуйста, исправьте ошибки отображаенные выше и повторите попытку.'); ?></p>
	<?php	} ?>

	<form name="WpmSendEmails" action="" method="post">
		<input type="hidden" name="send" value="true" />
		<input type="hidden" name="fromName" value="<?php echo $from_name;?>" />
		<input type="hidden" name="fromAddress" value="<?php echo $from_address;?>" />

		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
		<tr>
			<th scope="row" valign="top"><label><?php _e('Отправитель'); ?></label></th>
			<td><?php echo $from_name;?> &lt;<?php echo $from_address;?>&gt;</td>
		</tr>
		<tr>
            <?php if (defined('MBAN_VERSION') && version_compare(MBAN_VERSION, '1.41', '>=')): ?>
            <td></td>
            <td>
                <div class="wpm-ui-wrap">
                    <input type="hidden" name="tab"
                           value="<?= isset($_POST['tab']) && $_POST['tab'] === 'segment' ? 'segment' : 'send_targets' ?>">
                    <div id="tabs" class="wpm-inner-tabs-nav">
                        <ul style="padding: 0;margin-bottom: 10px;">
                            <li><a href="#wpm-levels"><?php _e('Уровни доступа'); ?></a></li>
                            <li><a href="#segments"><?php _e('Сегменты'); ?></a></li>
                        </ul>
                        <div id="wpm-levels" data-tab="send_targets" style="padding: 0">
                            <small><?php _e('Можно выделить несколько уровней доступа, удерживая клавишу CTRL на клавиатуре.'); ?></small>
                            <div>
                                <select id="send_targets" name="send_targets[]" multiple="multiple" size="8" style="width: 654px; height: 250px;">
                                    <?php echo wpm_get_levels_options_for_emails($send_targets); ?>
                                </select>
                            </div>
                        </div>
                        <div id="segments" data-tab="segment" style="padding: 0;margin-bottom: 10px;">
                            <label style="display: none" for="segment-items"><?php _e('Сегменты'); ?></label>
                            <select name="segment" id="segment-items">
                                <option value="">Выбрать сегмент</option>
                                <?php foreach((new \Mbl\Analytics\Segments())->query() as $segment): ?>
                                    <option value="<?= $segment['id'] ?>"><?= $segment['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </td>
            <?php else: ?>
            <th scope="row" valign="top">
                <label for="send_targets"><?php _e('Уровни доступа'); ?></label><br/><br/>
                <small><?php _e('Можно выделить несколько уровней доступа, удерживая клавишу CTRL на клавиатуре.'); ?></small>
            </th>
			<td>
				<select id="send_targets" name="send_targets[]" multiple="multiple" size="8" style="width: 654px; height: 250px;">
                    <?php echo wpm_get_levels_options_for_emails($send_targets); ?>
				</select>
			</td>
            <?php endif; ?>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="subject"><?php _e('Тема'); ?></label></th>
			<td><input type="text" id="subject" name="subject" value="<?php echo format_to_edit($subject);?>" style="width: 647px;" /></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="mailcontent"><?php _e('Сообщение'); ?></label></th>
			<td>
				<div id="mail-content-editor" style="width: 647px;">
				    <?php wp_editor(stripslashes($mail_content), "mailcontent"); ?>
				</div>
                <div class="wpm-help-wrap">
                    <p><span class="code-string">[pin_code]</span> - код доступа</p>
                    <?php wpm_auto_login_shortcodes_tips(); ?>
                </div>
			</td>
		</tr>
        <?php do_action('wpm_send_mails_fields'); ?>
		<tr>
			<th scope="row" valign="top"><label for="mailcontent"><?php _e('Выберите уровень доступа'); ?></label></th>
			<td>
				<select id="send_term_key_lvl" name="send_term_key_lvl" onchange="changeLinkedList(this, '#send_term_key')">
                    <option value=""></option>
                    <?php foreach ($plain_levels AS $level) :?>
                        <option value="<?php echo $level->term_id; ?>"><?php echo $level->name; ?></option>
                    <?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><label for="mailcontent"><?php _e('Выберите код доступа'); ?></label></th>
			<td>
				<select id="send_term_key" name="send_term_key">
                    <option value=""></option>
				</select>
				<select id="send_term_key_src" name="send_term_key_src" style="display: none">
                    <option value=""></option>
                    <?php echo wpm_get_term_keys_options_for_emails($plain_levels); ?>
				</select>
			</td>
		</tr>
		</table>

		<p class="submit">
			<input class="button-primary" type="submit" name="Submit" value="<?php _e('Отправить сообщение'); ?> &raquo;" />
		</p>
	</form>
</div>
<script type="text/javascript">
    function changeLinkedList(main, linked) {
        var $ = jQuery,
            val = $(main).val(),
            linkedSrc,
            options;

        linked = $(linked);

        if (linked.length) {
            linkedSrc = $('#' + linked.attr('id') + '_src');
            options = linkedSrc.find('option');

            if (val != '') {
                linked.prop('disabled', false);
                if(linked.data('empty') == '1') {
                    linked.html('<option value=""></option>');
                } else {
                    linked.html('');
                }
                options
                    .filter(function () {
                        return $(this).data('main') == val;
                    })
                    .clone()
                    .appendTo(linked);
            } else {
                linked.prop('disabled', true);
            }
        }
    }

    jQuery(function(){
        changeLinkedList('#send_term_key_lvl', '#send_term_key');
        <?php if (defined('MBAN_VERSION') && version_compare(MBAN_VERSION, '1.41', '>=')): ?>
        jQuery( "#tabs" ).tabs({
            active: <?= isset($_POST['tab']) && $_POST['tab'] === 'segment' ? 1 : 0 ?>,
            activate: function( event, ui ) {
                jQuery('[name="tab"]').val(ui.newPanel.get(0).dataset.tab);
            }
        });
        <?php endif; ?>
    });
</script>
