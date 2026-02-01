(function($) {
    'use strict';
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        
        // Инициализируем вкладки писем (если блок существует и доступен jQuery UI)
        var $emailTabs = $('#mlm_email_tabs');
        if ($emailTabs.length && $.fn.tabs && !$emailTabs.data('ui-tabs')) {
            $emailTabs.tabs();
        }

        // Текущее состояние сортировки таблицы "сонь" (по умолчанию: user_id DESC)
        var sortState = 'user_id';
        var orderState = 'DESC';

        
        /**
         * Load sleepers data via AJAX
         * @param {number} page Page number
         */
        function loadSleepers(page, sort, order) {
            var $button = $('#mlm_show_sleepers');
            var $container = $('#mlm_sleepers_container');
            var $loading = $('#mlm_sleepers_status');
            
            // Get form data
            var termId = $('input[name="tag_ID"]').val();
            var dateFrom = $('#mlm_sleepers_from').val();
            var dateTo = $('#mlm_sleepers_to').val();
            
            // Set default dates if empty
            if (!dateFrom || !dateTo) {
                var today = currentDate();
                dateFrom = dateFrom || today;
                dateTo = dateTo || today;
                $('#mlm_sleepers_from').val(dateFrom);
                $('#mlm_sleepers_to').val(dateTo);
            }
            
            // Validate dates
            if (!isValidDate(dateFrom) || !isValidDate(dateTo)) {
                alert('Пожалуйста, выберите корректную дату в формате ГГГГ-ММ-ДД');
                return;
            }
            
            // Show loading
            var originalText = $button.text();
            $button.prop('disabled', true).text('Загрузка...');
            $loading.text('Загрузка...').show();
            $container.html('');
            
            // AJAX request
            $.ajax({
                url: MLM.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'mlm_get_sleepers',
                    nonce: MLM.nonce,
                    term_id: termId,
                    page: page,
                    date_from: dateFrom,
                    date_to: dateTo
                },
                dataType: 'json',
                success: function(response) {
                    console.log('MLM Response:', response); // Для отладки
                    
                    if (response.success) {
                        // ОРИГИНАЛЬНАЯ СТРУКТУРА ОТВЕТА:
                        // response.html - полный HTML таблицы с пагинацией
                        // response.total - общее количество
                        // response.page - текущая страница
                        var responseData = response.data || {};
                        $container.html(responseData.html || '<div class="notice notice-info"><p>Нет данных для отображения</p></div>');
                        
                    } else {
                        // Ошибка от сервера
                        console.error('MLM Server Error:', response);
                        $container.html(
                            '<div class="notice notice-error">' +
                            '<strong>Ошибка сервера:</strong> ' + (response.data?.message || 'Неизвестная ошибка') +
                            '</div>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('MLM AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    
                    $container.html(
                        '<div class="notice notice-error">' +
                        '<strong>Ошибка соединения:</strong> ' + error + '<br>' +
                        'Проверьте консоль браузера для деталей.' +
                        '</div>'
                    );
                },
                complete: function() {
                    // Restore button
                    $button.prop('disabled', false).text(originalText);
                    $loading.text('');
                }
            });
        }
        
        // Toggle monitor block
        $('#mlm_enabled').on('change', function() {
            // Скрываем/показываем основной блок настроек
            $('#mlm_monitor_block').toggle($(this).is(':checked'));
            // Обновляем вкладки после показа блока, если они ещё не инициализированы
            if ($(this).is(':checked') && $emailTabs.length && $.fn.tabs) {
                if ($emailTabs.data('ui-tabs')) {
                    $emailTabs.tabs('refresh');
                } else {
                    $emailTabs.tabs();
                }
            }
        }).trigger('change');
        
        // Show sleepers button
        $('#mlm_show_sleepers').on('click', function(e) {
            e.preventDefault();
            loadSleepers(1, sortState, orderState);
        });
        
        // Pagination handler (оригинальная пагинация из MLM_Renderer)
        $(document).on('click', '.mlm-page-btn', function(e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) return;
            
            var page = $(this).data('page');
            var termId = $(this).data('term-id');
            
            if (page && termId) {
                // Подстраховка: если даты не заполнены, выставляем сегодняшнюю
                $('#mlm_sleepers_from').val($('#mlm_sleepers_from').val() || currentDate());
                $('#mlm_sleepers_to').val($('#mlm_sleepers_to').val() || currentDate());
                loadSleepers(page, sortState, orderState);
            }
        });

        /**
         * Get current date in YYYY-MM-DD format
         * @return {string}
         */
        function currentDate() {
            var now = new Date();
            var year = now.getFullYear();
            var month = String(now.getMonth() + 1).padStart(2, '0');
            var day = String(now.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }


        
        /**
         * Validate date format YYYY-MM-DD
         * @param {string} dateStr
         * @return {boolean}
         */
        function isValidDate(dateStr) {
            if (!dateStr) return false;
            var regex = /^\d{4}-\d{2}-\d{2}$/;
            if (!regex.test(dateStr)) return false;
            
            var date = new Date(dateStr);
            return date instanceof Date && !isNaN(date) && dateStr === date.toISOString().split('T')[0];
        }
        
        // Initialize date pickers (if available)
        if ($.fn.datepicker) {
            // Инициализируем календарь для фильтров по дате
            $('#mlm_sleepers_from, #mlm_sleepers_to').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                maxDate: 0 // Нельзя выбрать будущие даты
            });
        }
    });
    
})(jQuery);