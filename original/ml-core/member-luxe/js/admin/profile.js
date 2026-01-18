jQuery(document).ready(function () {
    let $ = jQuery;
    $(document).on('click', '.mbl-remove-key', function () {
        $('#mlbResult').hide()
        let elem = $(this);
        elem.prop("disabled", true)
        let key = elem.attr('data-key');
        $('.wpm-ajax-overlay').show()
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: "wpm_move_key_to_ban_action",
                key: key,
                user: wpm.user_id
            },
            success: function (data) {
                if (data.error) {
                    $('#mlbResult').removeClass('notice-success').addClass('notice-error').show().html(data.message);
                } else {
                    $('#mlbResult').removeClass('notice-error').addClass('notice-success').show().html('Ключ удалён');
                    render(data['_keys'])
                }
            },
            error: function (errorThrown) {
                //console.log(errorThrown);
            }
        }).always(function () {
            $('.wpm-ajax-overlay').hide()
        });
    });

    $('#addKeyFromData').on('click', function (event) {
        $('.wpm-ajax-overlay').show()
        $('#mlbResult').hide()
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'wpm_add_key_to_user_profile_action',
                user: wpm.user_id,
                term_id: $('#userWpmLvl').val(),
                duration: $('#userWpmDuration').val(),
                units: $('#userWpmUnits').val(),
                is_unlimited: ($('#userWpmUnlimited').prop('checked') ? 1 : 0)
            },
            success: function (data) {
                if (data.error) {
                    $('#mlbResult').removeClass('notice-success').addClass('notice-error').show().html(data.message);
                } else {
                    $('#mlbResult').removeClass('notice-error').addClass('notice-success').show().html(data.message);
                    render(data['_keys'])
                }
            },
            error: function (errorThrown) {
                //console.log(errorThrown);
            }
        }).always(function () {
            $('.wpm-ajax-overlay').hide()
        });
    });

    $('#addNewKey').on('click', function (event) {
        $('.wpm-ajax-overlay').show()
        $('#mlbResult').hide()
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: "wpm_add_key_to_user_action",
                key: $('#userWpmKey').val(),
                user_id: wpm.user_id,
                include_keys: true,
                source: 'profile_page_admin'
            },
            success: function (data) {
                if (data.error) {
                    $('#mlbResult').removeClass('mbl-success').addClass('mbl-error').show().html(data.message);
                } else {
                    $('#mlbResult').removeClass('mbl-error').addClass('mbl-success').show().html(data.message);
                    $('#userWpmKey').val('')
                    render(data['_keys'])
                }
            },
            error: function (errorThrown) {
                //console.log(errorThrown);
            }
        }).always(function () {
            $('.wpm-ajax-overlay').hide()
        });
    });

    $('#userWpmUnlimited').on('change', function () {
        updateUnlimitedTermOption($(this));
    })

    function updateUnlimitedTermOption($checkbox) {
        $('#userWpmDuration').prop('disabled', $checkbox.prop('checked'));
        $('#userWpmUnits').prop('disabled', $checkbox.prop('checked'));
    }

    function render(keys) {
        let html = keys.map((k) => {
            let statusHtml;
            if (k.status === 'banned') {
                statusHtml = `<span title="Удален" style="color: red" class="dashicons dashicons-no-alt"></span>`
            } else if (k.status === 'expired') {
                statusHtml = `<span title="Закончился" style="color: #ff9f09" class="dashicons dashicons-backup"></span>`
            } else {
                statusHtml = `<span title="Активен" style="color: #00AD47" class="dashicons dashicons-post-status"></span>`
            }
            let duration = '';
            if (k.is_unlimited === 1) {
                duration = 'Не ограничено'
            } else if (k.units === 'months') {
                duration = `${k.duration} мес.`
            } else if (k.units === 'days') {
                duration = `${k.duration} дн.`
            }
            return `<tr>
                        <td>${k.term ? k.term.name : 'Уровень доступа удалён'}</td>
                        <td class="mbl-key-col"><span class="mbl-key">${k.key}</span></td>
                        <td class="mbl-key-status">${statusHtml}</td>
                        <td>${duration}</td>
                        <td>${k.date_start_format}</td>
                        <td>${k.is_unlimited === 1
                ? '<svg class="mbl-icon"><use href="#Infinity"></use></svg>'
                : k.date_end_format}
                        </td>
                        <td class="mbl-key-left">${k.is_unlimited === 1
                ? '<svg class="mbl-icon"><use href="#Infinity"></use></svg>'
                : (k.left === '—' ? k.left : `${k.left} дн.`)}
                        </td>
                        <td class="mbl-key-action">${k.is_banned !== 1
                ? `<button class="mbl-remove-key" data-key="${k.key}">` +
                '<span class="dashicons dashicons-trash"></span>' +
                '</button>'
                : '—'}
                        </td>
                    </tr>`
        }).join()
        jQuery('#mblKeysTable').show().find('tbody').html(html)
    }
});
