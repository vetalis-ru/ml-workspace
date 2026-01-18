(function ($) {
    $("#certificate_series").on("blur", verifyCertificateSeries);
    $("#certificate_series").on("input", hideError);

    function verifyCertificateSeries(event) {
        event.preventDefault();
        if (!event.target.value) return;
        const body = new FormData();
        $('.edit-tag-actions [type="submit"]').prop("disabled", true);
        body.append("action", 'mblc_verify_certificate_series');
        body.append("wpmLvlId", window.mblc.wpmLvlId);
        body.append("series", event.target.value);
        fetch(ajaxurl, {
            method: "POST",
            body: body,

        })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    hideError()
                } else {
                    showError()
                }
            })
            .catch(e => console.error(e))
    }

    function hideError() {
        $("#certificate_series").removeClass("mblc-series-error");
        $('.edit-tag-actions [type="submit"]').prop("disabled", false);
        $(".mblc-series-error-msg").hide();
    }

    function showError() {
        $("#certificate_series").addClass("mblc-series-error");
        $('.edit-tag-actions [type="submit"]').prop("disabled", true);
        $(".mblc-series-error-msg").show();
    }
})(jQuery);
(function ($) {
    let optionGroup = jQuery('.mblc-options-group-certificate');
    let has_certificate_inp = $('#has_certificate');
    has_certificate_inp.on('change', function () {
        if (has_certificate_inp.prop('checked')) {
            optionGroup.removeClass('mblc-no-certificate');
        } else {
            optionGroup.addClass('mblc-no-certificate');
        }
    })
})(jQuery);