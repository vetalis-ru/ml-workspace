<?php echo apply_filters('wpm_hw_extra_styles', wpm_render_partial('homework/extra-styles', 'admin', [], true)) ?>
<div class="wrap mbl-homework-wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php _e('Домашние задания', 'mbl_admin') ?></h2>
    <div class="panel with-nav-tabs panel-default mbl-hw-panel">
        <div class="panel-heading">
            <ul class="nav nav-tabs mbl-homework-tabs">
                <li class="active"><a href="#mbl_hw_list_pane"><i class="fa fa-list"></i>
                        <span><?php _e('Список заданий', 'mbl_admin'); ?></span></a></li>
                <?php if (current_user_can('manage_options')) : ?>
                    <li><a href="#mbl_hw_settings_pane"><i class="fa fa-cog"></i>
                            <span><?php _e('Настройки списка', 'mbl_admin'); ?></span></a></li>
                <?php endif; ?>
                <?php do_action('mbl_hw_tabs'); ?>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content clearfix">
                <div class="tab-pane active" id="mbl_hw_list_pane">
                    <div class="page-content-wrap">
                        <?php wpm_render_partial('homework/list', 'admin', compact('categories', 'levels')) ?>
                    </div>
                </div>
                <?php if (current_user_can('manage_options')) : ?>
                    <div class="tab-pane" id="mbl_hw_settings_pane">
                        <div class="page-content-wrap">
                            <?php wpm_render_partial('homework/settings', 'admin') ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php do_action('mbl_hw_content'); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        var $wpmHWContent = $('#wpm-hw-content');

        updateStats();
        updateBulkStatusesSelect();
        initSelect2();

        $(document).on('click', '.mbl-homework-tabs > li', function () {
            switchTab($(this).find('>a'));

            return false;
        });

        function switchTab($link) {
            if ($link.length) {
                var $li = $link.closest('li'),
                    $tabs = $li.closest('.nav-tabs'),
                    $selector = $link.attr('href'),
                    $pane = $($selector),
                    $tabContent = $pane.closest('.tab-content');

                $tabs.find('>li').removeClass('active');
                $tabContent.find('>.tab-pane').removeClass('active');
                history.pushState(null, null, $link.attr('href'));

                $li.addClass('active');
                $pane.addClass('active');
            }

        }

        if (window.location.hash != '') {
            switchTab($('a[href="' + window.location.hash + '"]'));
        }

        $wpmHWContent.on('click', '.mbl-homework-inner-tabs li', function () {
            switchInnerTab($(this).find('>a'));

            return false;
        });

        function updateBulkStatusesSelect() {
            var $holder = $('#wpm-hw-select-all-action');

            hideBulkStatusesSelect();

            $holder.html('');

            $('#mbl_hw_bulk_statuses').contents().detach().appendTo($holder);
        }

        function hideBulkStatusesSelect() {
            $('#wpm-hw-select-all').prop('checked', false);
            $('#wpm-hw-select-all-action').hide();
            $('#wpm-hw-select-all-label').show();
        }

        function showBulkStatusesSelect() {
            $('#wpm-hw-select-all-action').show();
            $('#wpm-hw-select-all-label').hide();
        }

        function switchInnerTab($link) {
            if ($link.length) {
                var $li = $link.closest('li'),
                    $tabs = $li.closest('.nav-tabs'),
                    $selector = $link.attr('href'),
                    $pane = $($selector),
                    $tabContent = $pane.closest('.tab-content');

                $tabs.find('li').removeClass('active');
                $tabContent.find('.tab-pane').removeClass('active');

                $li.addClass('active');
                $pane.addClass('active');
            }
        }


        function initSelect2() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('[data-mbl-select-2]').select2({
                    dropdownCssClass : "wpm-hw-select-2"
                });

                $('[data-mbl-select-2-html]').select2({
                    dropdownCssClass  : "wpm-hw-select-2",
                    templateResult    : formatSelect2Html,
                    templateSelection : formatSelect2Html,
                    escapeMarkup      : function (m) {
                        return m;
                    }
                });
            }
        }

        function formatSelect2Html(state) {
            var $option = $(state.element);

            if (!$option.data('html')) {
                return state.text;
            }

            return $option.data('html');
        }
        
        if (typeof $.fn.select2 !== 'undefined') {
            $('#mbl_stats_material_select').select2({
                allowClear         : true,
                dropdownCssClass   : "wpm-hw-select-2",
                ajax               : {
                    url            : ajaxurl,
                    dataType       : 'json',
                    delay          : 250,
                    data           : function (params) {
                        return {
                            q      : params.term,
                            action : 'mbl_stats_materials'
                        };
                    },
                    processResults : function (data) {
                        var options = [];
                        if (data) {
                            $.each(data, function (index, text) {
                                options.push({id : text[0], text : text[1]});
                            });
                        }
                        return {
                            results : options
                        };
                    },
                    cache          : true
                },
                minimumInputLength : 0
            });
        }


        $('.mbl-dates-select')
            .on('click', '.selection', function () {
                var $holder = $(this).closest('.mbl-dates-select');
                $holder.addClass('opened');
                $holder.find('.mbl-dates-popup').show();
                $holder.find('#mbl_stats_date_to, #mbl_stats_date_from').each(function () {
                    $(this).datepicker('refresh');
                })
            })
            .on('click', '.mbl-date-close', function () {
                closeDateSelect();
                return false;
            })
            .on('click', '.mbl-date-submit', function () {
                updateHwList();
                return false;
            });

        $("#mbl_stats_date_to").datepicker({
            dateFormat: 'dd.mm.yy',
            altField: '#mbl_stats_date_to_input'
        });

        $("#mbl_stats_date_from").datepicker({
            dateFormat: 'dd.mm.yy',
            defaultDate: $('#mbl_stats_date_from').data('start-date'),
            altField: '#mbl_stats_date_from_input'
        }).bind("change", function () {
            var minValue = $(this).val();
            minValue = $.datepicker.parseDate("dd.mm.yy", minValue);

            if (minValue) {
                minValue.setDate(minValue.getDate());
                $("#mbl_stats_date_to").datepicker("option", "minDate", $(this).val());
            }
        });

        $(document).on('submit', '#wpm-hw-filter', function () {
            updateHwList();

            return false;
        });

        $(document).on('click', '.wpm-status-filters > a', function () {
            var $this = $(this);

            if ($this.hasClass('active')) {
                return false;
            }

            $('.wpm-status-filters > a').removeClass('active');
            $this.addClass('active');
            $('#wpm-hw-filter-status').val($this.data('status'));

            updateHwList();
            return false;
        });

        $(document).on('click', '#mbl_hw_order_buttons [data-order]', function () {
            var $this = $(this);

            $('#mbl_hw_order_buttons [data-order]').removeClass('order-desc').removeClass('order-asc');

            $this.data('order', $this.data('order') === 'asc' ? 'desc' : 'asc');
            $this.addClass('order-' + $this.data('order'));

            $('#wpm-hw-filter-order-by').val($this.data('order-by'));
            $('#wpm-hw-filter-order').val($this.data('order'));
            updateHwList();
        });

        $wpmHWContent.on('click', '[data-toggle-row]>td:not(.mbl-checkbox-td)', function (e) {
            var $item = $(this).closest('[data-toggle-row]'),
                $panel = $('#mbl_hw_list_pane'),
                $id = $item.data('toggle-row'),
                $row = $('#details-row-' + $id + ' div:first');


            $('.mbl-hw-details-row-inner.expanded').removeClass('expanded');

            if (!$item.hasClass('mbl-row-opened')) {
                $panel.find('.mbl-row-opened').each(function () {
                    var $this = $(this),
                        $_row = $('#details-row-' + $this.data('toggle-row') + ' div:first');

                    $this.removeClass('mbl-row-opened');
                    $_row.slideUp();
                });
            }

            $item.toggleClass('mbl-row-opened');

            if ($row.hasClass('loaded')) {
                $row.slideToggle(addExpandedClass);
            } else {
                mbl_hw_loader($row, 'start', true, true);
                $row.slideToggle(addExpandedClass);

                $.post(ajaxurl, {action: 'wpm_hw_item_content', response_id: $id}, function (response) {
                    $row.html(response);
                    $row.addClass('loaded');
                    initFancybox($row);
                    initFileUpload(jQuery);
                    initSelect2();
                    initMblTooltips($row);
                });
            }

            function addExpandedClass() {
                var $this = $(this);
                if (!$this.is(":hidden")) {
                    $this.addClass('expanded');
                }
            }

            return false;
        });

        $wpmHWContent.on('change', '[name="hw-action"]', function () {
            var val = $(this).val(),
                $holder = $(this).closest('.mbl-hw-actions');

            if (val !== '') {
                $holder.find('> .buttons').addClass('opened');
            }
        });
        $wpmHWContent.on('click', '.wpm-hw-status-change.accept', function () {
            var $this = $(this),
                $holder = $this.closest('.mbl-hw-actions'),
                data = {
                    action: 'wpm_update_response_action',
                    response_id: $this.data('response-id'),
                    post_id: $this.data('post-id'),
                    response_status: $holder.find('[name="hw-action"]').val()
                };

            mbl_hw_loader($holder.closest('.page-content-wrap'), 'start', true, true);
            $.post(ajaxurl, data, updateHwList);

            return false;
        });

        $wpmHWContent.on('click', '.wpm-hw-status-change.decline', function () {
            var $holder = $(this).closest('.mbl-hw-actions');

            $holder.find('[name="hw-action"]').val('').trigger('change');
            $holder.find('.buttons').removeClass('opened');

            return false;
        });

        $(document).on('change', '#wpm-hw-select-all', function () {
            $('.mbl-hw-status-checkbox').prop('checked', $(this).prop('checked'));
            updateStatusesForm();
        });

        $('#wpm-hw-select-all-action')
            .on('change', '[name="hw-bulk-action"]', function () {
                var val = $(this).val();

                if (val !== '') {
                    $('#wpm-hw-select-all-action > .buttons').addClass('opened');
                }
            })
            .on('click', '.wpm-hw-status-bulk-change.accept', function () {
                var data = {
                    action: 'wpm_bulk_update_response_action',
                    ids: $(".mbl-hw-status-checkbox:checked").map(function () {
                        return this.value;
                    }).get(),
                    response_status: $('[name="hw-bulk-action"]').val()
                };

                mbl_hw_loader($('#wpm-hw-select-all-action'), 'start', true, true, 'small');
                $.post(ajaxurl, data, updateHwList);

                return false;
            })
            .on('click', '.wpm-hw-status-bulk-change.decline', function () {
                $('[name="hw-bulk-action"]').val('').trigger('change');
                $('#wpm-hw-select-all-action > .buttons').removeClass('opened');

                return false;
            })
            .on('click', '.wpm-hw-status-bulk-cancel', function () {
                $('.mbl-hw-status-checkbox').prop('checked', false);
                updateStatusesForm();

                return false;
            });

        $wpmHWContent.on('change', '.mbl-hw-status-checkbox', function () {
            if (!$(this).prop('checked')) {
                $('#wpm-hw-select-all').prop('checked', false);
            }

            updateStatusesForm();
        });

        function updateStatusesForm() {
            if ($('.mbl-hw-status-checkbox:checked').length) {
                showBulkStatusesSelect();
            } else {
                hideBulkStatusesSelect();
            }
        }

        function updateHwList() {
            var $form = $('#wpm-hw-filter'),
                $holder = $('#wpm-hw-content');

            mbl_hw_loader($holder, 'start', true);
            closeDateSelect();
            updateDateSelectClass();
            $form.find('[name="s"]:not(:visible)').prop('disabled', 1);
            $.post(ajaxurl, $form.serialize(), function (response) {
                $holder.html(response);
                updateStats();
                updateBulkStatusesSelect();
                initSelect2();
                $form.find('[name="s"]').prop('disabled', 0);
            });
        }

        function closeDateSelect() {
            var $select = $('.mbl-dates-select');

            $select.removeClass('opened');
            $select.find('.mbl-dates-popup').hide();
            updateDatePlaceholder();
            return false;
        }

        function updateDateSelectClass() {
            var $select = $('#mbl-hw-dates');

            $select.removeClass('mbl-border-opened mbl-border-approved mbl-border-rejected mbl-border-trash');
            $select.addClass('mbl-border-' + $('#wpm-hw-filter-status').val());
        }

        function updateDatePlaceholder() {
            var dateFrom = shortenDate($('#mbl_stats_date_from_input').val()),
                dateTo = shortenDate($('#mbl_stats_date_to_input').val()),
                text;


            if (dateFrom === dateTo) {
                text = dateFrom;
            } else {
                text = (dateFrom + ' - ' + dateTo);
            }

            $('#mbl-dates-placeholder').text(text);
        }

        function shortenDate(dateStr) {
            var arr;
            if (typeof dateStr === 'undefined' || !dateStr) {
                return '';
            }

            arr = dateStr.split('.');

            if (typeof arr[2] === 'undefined') {
                return '';
            }

            arr[2] = arr[2].slice(2);

            return arr.join('.');
        }

        function mbl_hw_loader($elem, action, replace, plain, addClass) {
            var tpl, colspan, tr;

            addClass = addClass || '';

            tpl = '<div class="loader-ellipse ' + addClass + '" loader>' +
                '<span class="loader-ellipse__dot"></span>' +
                '<span class="loader-ellipse__dot"></span>' +
                '<span class="loader-ellipse__dot"></span>' +
                '<span class="loader-ellipse__dot"></span>' +
                '</div>';
            colspan = $('#mbl_hw_order_buttons>th').length + 1;
            tr = '<tr><td colspan="' + colspan + '">' + tpl + '</td></tr>';

            plain = plain || false;

            action = action || 'start';
            replace = replace || true;

            if (action === 'start') {
                $elem[replace ? 'html' : 'append'](plain ? tpl : tr)
            } else if (action === 'stop') {
                $elem.find('[loader]').remove();
            }
        }

        function updateStats() {
            var $inputs = $('.wpm-hw-stats-input'),
                $labels = $('.wpm-status-filters');

            if ($inputs.length) {
                $inputs.each(function () {
                    var $this = $(this);
                    $labels.find('.' + $this.data('type') + ' .wpm-status-nb').text($this.val());
                })
            } else {
                $labels.find('.wpm-status-nb').text('0');
            }

            $labels.find('a:focus').blur();
        }

        initFancybox();

        function initFancybox($elem) {
            $elem = $elem || $(document);

            $elem.find('.fancybox').fancybox({
                buttons: [
                    "zoom",
                    "fullScreen",
                    "download",
                    "thumbs",
                    "close"
                ],
                smallBtn: false,
                toolbar: true,
                video: {
                    tpl: '<video class="fancybox-video" controls poster="{{poster}}"><source src="{{src}}" type="{{format}}" />К сожалению, Ваш браузер не поддерживает встроенные видео, <a href="{{src}}">скачать</a></video>',
                    format: "",
                    autoStart: true
                },
                lang: 'ru',
                i18n: {
                    'ru': {
                        CLOSE: 'Закрыть',
                        NEXT: 'Следующий',
                        PREV: 'Предыдущий',
                        ERROR: 'Не удалось отобразить содержимое',
                        ZOOM: 'Масштаб',
                        FULL_SCREEN: 'На весь экран',
                        THUMBS: 'Превью'
                    }
                }
            })
        }
    });


</script>
<?php wpm_enqueue_script('jquery-fancybox', plugins_url('/member-luxe/2_0/fancybox/jquery.fancybox.min.js?v=' . WP_MEMBERSHIP_VERSION)); ?>