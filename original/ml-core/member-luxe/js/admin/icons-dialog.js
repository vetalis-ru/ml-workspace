(function ($) {
    $(document).ready(function () {
        let current = null
        let dialog = $( "#icons-dialog" ).dialog({
            classes: {
                "ui-dialog": "icons-dialog"
            },
            autoOpen: false,
            modal: true,
            height: 740,
            width: 920,
            closeText: '',
            close: function( event, ui ) {
                $(this).find('[data-check]').removeClass('selected')
            }
        });
        $( ".wpm-settings-form" ).on( "click", '[data-icon] button', function() {
            current = {
                el: $(this).closest('[data-icon]'),
                val: $(this).closest('[data-icon]').find('input').val()
            }
            $('#icons-dialog').find(`[data-check="${current.val}"]`).addClass('selected')
            dialog.dialog( "open" );
        });
        $('#icons-dialog').on('click', '[data-check]', function () {
            current.el.find('input').val($(this).data('check'))
            current.el.find('.fa').removeClass(current.val).addClass($(this).data('check'))
            dialog.dialog( "close" );
        })
        $('#icons-dialog [data-search]').on('input', function (event) {
            if (event.target.value.trim() === '') {
                $('#icons-dialog [data-check]').each(function (i, item) {
                    $(item).closest('.icons-dialog__col').show()
                })
            } else {
                let search = event.target.value.trim();
                $('#icons-dialog [data-check]').each(function (i, item) {
                    let re = new RegExp(search);
                    let icon = $(item).data('check').slice(3)
                    if(re.test(icon)) {
                        $(item).closest('.icons-dialog__col').show()
                    } else {
                        $(item).closest('.icons-dialog__col').hide()
                    }
                })
            }
        })
    })
})(jQuery);
