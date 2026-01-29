(function($) {
    'use strict';
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        
        // Initialize tabs
        $('#mlm-email-tabs').tabs();
        
        // Toggle monitor block
        $('#mlm_enabled').on('change', function() {
            $('#mlm-monitor-block').toggle($(this).is(':checked'));
        }).trigger('change');
        
        // Show sleepers button
        $('#mlm-show-sleepers').on('click', function(e) {
            e.preventDefault();
            loadSleepers(1, sortState, orderState);
        });
        
        // Date filter form submit
        $('#mlm-sleepers-form').on('submit', function(e) {
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
                $('#mlm_date_from').val($('#mlm_date_from').val() || currentDate());
                $('#mlm_date_to').val($('#mlm_date_to').val() || currentDate());
                loadSleepers(page, sortState, orderState);
            }
        });
        
        /**
         * Load sleepers data via AJAX
         * @param {number} page Page number
         */
        function loadSleepers(page, sort, order) {
            var $button = $('#mlm-show-sleepers');
            var $container = $('#mlm-sleepers-results');
            var $loading = $('#mlm-loading');
            
            // Get form data
            var termId = $('input[name="tag_ID"]').val();
            var dateFrom = $('#mlm_date_from').val();
            var dateTo = $('#mlm_date_to').val();
            
            // Set default dates if empty
            if (!dateFrom || !dateTo) {
                var today = currentDate();
                dateFrom = dateFrom || today;
                dateTo = dateTo || today;
                $('#mlm_date_from').val(dateFrom);
                $('#mlm_date_to').val(dateTo);
            }
            
            // Validate dates
            if (!isValidDate(dateFrom) || !isValidDate(dateTo)) {
                alert('Пожалуйста, выберите корректную дату в формате ГГГГ-ММ-ДД');
                return;
            }
            
            // Show loading
            var originalText = $button.text();
            $button.prop('disabled', true).text('Загрузка...');
            $loading.show();
            $container.html('');
            
            // AJAX request
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'mlm_get_sleepers',
                    nonce: mlm_ajax.nonce,
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
                        
                        $container.html(response.html || '<div class="notice notice-info"><p>Нет данных для отображения</p></div>');
                        
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
                    $loading.hide();
                }
            });
        }
        
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

        // Текущее состояние сортировки таблицы "сонь" (по умолчанию: user_id DESC)
        var sortState = 'user_id';
        var orderState = 'DESC';

        
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
            $('#mlm_date_from, #mlm_date_to').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                maxDate: 0 // Нельзя выбрать будущие даты
            });
        }
    });
    
})(jQuery);