(function ($) {
    $(document).ready(e => $(".certificateForm").submit(verifyCertificateFormHandler));
    function verifyCertificateFormHandler(event) {
        event.preventDefault();
        let form = $(this);
        print_result(false, "success");
        print_result(false, "warning");
        $(this).addClass("was-validated");
        if (this.checkValidity() === false) {
            event.stopPropagation();
            return false;
        }
        $(this).find("[name=submit]").prop("disabled", true)
        $(this).find("[name=submit]").find("[role=status]").show();
        $.ajax({
            url: mbl_data.ajax_url,
            method : 'POST',
            data: $(this).serialize(),
            success: function (json) {
                let data = JSON.parse(json);
                if (data.status === "success") {
                    print_result(true, "success",  data.message);
                } else {
                    print_result(true, "warning", data.message);
                }
            },
            complete: function () {
                form.find("[name=submit]").prop("disabled", false)
                form.find("[name=submit]").find("[role=status]").hide();
            }
        });

    }

    function print_result(show, status, message = '') {
        let view = status === "success" ? $("#certificateSearchResult") : $("[data-status="+status+"]");
        if (show) {
            if (status === "success"){
                view.html(message)
                view.show();
                return view;
            }
            view.text(message);
            view.show();
        } else {
            view.hide();
        }
        return view;
    }
})(jQuery);