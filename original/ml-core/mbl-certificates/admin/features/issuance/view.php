<?php
/**
 * @global array $wpmLevelsOptions
 * @global bool $isCoach
 */
?>
<style>
    .tablenav {
        height: auto;
    }

    .sortable, .sorted {
        cursor: pointer;
        font-size: 14px;
    }

    .sortable:after, .sorted:after {
        font-family: "Font Awesome 5 Free";
        float: right;
    }

    .sortable:after {
        display: none;
    }

    .sorted:after {
        display: inline-block;
    }

    .sortable:hover:after {
        display: inline-block;
    }

    .sortable.asc:hover:after {
        content: "\f0d8";
    }

    .sortable.desc:hover:after {
        content: "\f0d7";
    }

    .sorted.asc:after {
        content: "\f0d8";
    }

    .sorted.desc:after {
        content: "\f0d7";
    }

    .sorted.asc:hover:after {
        content: "\f0d7";
    }

    .sorted.desc:hover:after {
        content: "\f0d8";
    }

    input[type=date].form-control {
        height: calc(1.5em + .75rem + 0px) !important;
    }

    .flex {
        display: -ms-flexbox;
        display: flex;
    }

    .inline-block {
        display: inline-block;
    }

    @media (min-width: 1024px) {
        .wpml-select {
            width: 240px;
        }
    }
    .was-validated .form-control:valid {
        background-image: none;
        border-color: #8c8f94;
        padding: 0.375rem 0.75rem;
    }
    .was-validated .js-select2:invalid ~ .select2 .select2-selection {
        border: 1px solid #dc3545;
    }
</style>
<div class="container-fluid">
    <div class="mt-4 d-flex">
        <h1 class="mr-5">Выдача сертификатов</h1>
        <div class="mbl-options-preloader flex align-items-center" style="display: none">
            <div class="loader-ellipse">
                <span class="loader-ellipse__dot"></span>
                <span class="loader-ellipse__dot"></span>
                <span class="loader-ellipse__dot"></span>
                <span class="loader-ellipse__dot"></span>
            </div>
        </div>
    </div>
    <!-- p class="text">
        Выберите в выпадающем списке онлайн-курс. Нажмите кнопку "выбрать" и в таблице появиться список обучающихся.
    </p -->
    <?php /* if(!$isCoach): */ ?>
    <!-- form id="mlDayAfterCourseEndForm">
            <div class="form-group">
                <input type="hidden" name="action" value="ml_save_day_after_course_end">

                <label for="mlDayAfterCourseEnd" class="form-check-label">
                    Выводить кураторам студентов с датой окончания уровня доступа +
                    <input type="number" id="mlDayAfterCourseEnd" value="<?php /* <?= get_option('ml_day_after_course_end') */ ?>"
                           name="day_number" class="form-control d-inline-block" style="width: 70px;"> дней</label>
                <button type="submit" name="submit" class="btn">
                    <span class="fa fa-save"></span>
                </button>
            </div>
        </form -->
    <?php /* endif; */ ?>
    <div class="tablenav top">
        <form id="usersByWmpLevel" class="needs-validation" novalidate>
            <div class="mb-3 w-100 d-flex flex-wrap align-items-center">
                <div class="position-relative inline-block mr-4">
                    <label style="display:block;" for="wpm_level">Уровень доступа</label>
                    <select name="wpm_level" id="wpm_level" class="js-select2" required style="width: 240px;">
                        <option value="">Выбрать уровень доступа</option>
                        <?php foreach ($wpmLevelsOptions as $wpm_level): ?>
                            <option value="<?= esc_attr($wpm_level->term_id) ?>"><?= $wpm_level->name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback position-absolute mt-0" style="bottom: -20px;">
                        Укажите уровень доступы
                    </div>
                </div>
                <div class="position-relative inline-block">
                    <label>Дата добавления доступа</label>
                    <div class="">
                        <div class="d-inline-block">
                            <label style="display: inline-block;" for="wpm_level_date_start">с</label>
                            <input id="date" data-toggle="datepicker" autocomplete="off"
                                   placeholder="дд.мм.гггг"
                                   name="date_start" class="form-control">
                        </div>
                        <div class="d-inline-block">
                            <label style="display: inline-block;" for="wpm_level_date_end">до</label>
                            <input id="date" data-toggle="datepicker" autocomplete="off"
                                   placeholder="дд.мм.гггг"
                                   name="date_end" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100">
                <?php if (!$isCoach): ?>
                    <div class="form-check-inline">
                        <input type="checkbox" id="activeWPMLevel" name="active_wpmlevel" checked value="active-wpmlevel">
                        <label for="activeWPMLevel" class="form-check-label">Только активные уровни доступа</label>
                    </div>
                    <div class="mr-2 d-inline-block">
                        <label style="display: none" for="user_email">Выдать пользователю</label>
                        <input class="form-control" style="max-width: 250px" type="text" name="user_email" id="user_email"
                               placeholder="Введите email">
                    </div>
                <?php endif ?>
                <input type="submit" id="ml_select_user" class="btn btn-success" value="Показать">
            </div>
            <input type="hidden" name="action" value="ml_select_user">
            <input type="hidden" name="orderby" value="user_login">
            <input type="hidden" name="order" value="asc">
        </form>
    </div>
    <div id="alertError" class="alert alert-danger mt-4" role="alert" style="display: none">
    </div>
    <div id="alertSuccess" class="alert alert-success mt-4" role="alert" style="display: none">
        Сертификат успешно присвоен
    </div>
    <form style="display: none" id="usersSetCertificate" class="needs-validation mt-2" novalidate>
        <div class="mblc-table card mb-4 p-0" style="max-width: 100%">
            <span class="table-loading-icon fas fa-spin fa-spinner"></span>
            <div class="card-header py-3">
                <h6 class="d-flex m-0">
                    <span class="h-auto ">
                        <label for="mblc_dateIssuance" class="text-primary">Дата присвоения сертификата</label>
                        <input id="mblc_dateIssuance" data-toggle="datepicker" autocomplete="off"
                               placeholder="дд.мм.гггг"
                               name="date_issue" required class="form-control mb-2 mb-lg-0"
                               style="display: inline-block; width: 200px; margin-left: 10px; margin-right: 10px;">
                        <input type="hidden" id="wpm_level_users" name="wpm_level" value="">
                        <input type="hidden" name="action" value="ml_certificate_delivery">
                        <input type="submit" value="Присвоить сертификат" class="btn btn-primary"
                               id="ml_certificate_delivery">
                    </span>
                    <span style="display: none" id="mblcFindCount" class="ml-auto"></span>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover table-bordered dataTable" id="dataTable"
                                       role="grid" aria-describedby="dataTable_info"
                                       style="width: 100%; border: 0px; border-bottom: 1px solid #000;">
                                    <thead>
                                    <tr role="row">
                                        <th id="cb" class="manage-column column-cb check-column">
                                            <input id="cb-select-all-1" type="checkbox">
                                        </th>
                                        <th scope="col" id="user_login" data-orderby="user_login"
                                            style="font-size: 1rem;"
                                            class="manage-column column-username column-primary sorted asc">
                                            Логин пользователя
                                        </th>
                                        <th scope="col" id="last_name"
                                            class="manage-column column-primary sortable desc"
                                            style="font-size: 1rem;" data-orderby="last_name">
                                            Фамилия
                                        </th>
                                        <th scope="col" id="first_name"
                                            style="font-size: 1rem;" class="manage-column column-primary sortable desc"
                                            data-orderby="first_name">
                                            Имя
                                        </th>
                                        <th scope="col" id="surname" class="manage-column column-primary sortable desc"
                                            style="font-size: 1rem;" data-orderby="surname">
                                            Отчество
                                        </th>
                                    </tr>
                                    </thead>

                                    <tbody id="the-list" style="border-bottom: 1px solid #DEE2E6;">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    (function ($) {
        let loader = $('.mbl-options-preloader');
        let form = document.getElementById('usersByWmpLevel');
        let certificateForm = document.getElementById('usersSetCertificate');
        let findCount = document.getElementById('mblcFindCount');
        let table = document.getElementById('the-list');
        form.addEventListener('submit', function (event) {
            $(certificateForm).show()
            event.preventDefault();
            $('#alertError').hide();
            $('#alertSuccess').hide();
            if (form.checkValidity() === false) {
                event.stopPropagation();
            }
            $(form).addClass("was-validated");
            if (form.checkValidity() === true) {
                getUserByWmpLevel(form, table);
            }
        })

        certificateForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const data = $(certificateForm).serialize();
            if (certificateForm.checkValidity() === false) {
                event.stopPropagation();
            }
            $(certificateForm).addClass("was-validated");
            if (certificateForm.checkValidity() === true) {
                blockUi(true)
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    processData: false,
                    data: data,
                    success: function (response) {
                        let data = JSON.parse(response);
                        if (data.error) {
                            $('#alertError').text(data.error);
                            $('#alertError').show();
                        } else if (data.success) {
                            table.innerHTML = "";
                            $('#alertSuccess').show();
                        }
                    }
                }).always(function () {
                    blockUi(false)
                })
            }
        })

        $(certificateForm).on('change', function (event) {
            $('#alertError').hide();
            $('#alertSuccess').hide();
        })
        $('[data-orderby]').on('click', doOrder);

        $('#wpm_level').on('select2:select', function (event) {
            $(form).removeClass("was-validated");
        })

        function doOrder(event) {
            resetOrder();
            let orderby = $(this).attr('data-orderby');
            let oldOrder = $(this).hasClass('asc') ? 'asc' : 'desc';
            let newOrder = $(this).hasClass('asc') ? 'desc' : 'asc';
            $(this).removeClass(`${oldOrder} sortable`);
            $(this).addClass(`${newOrder} sorted`);
            $('[name="orderby"]').val(orderby);
            $('[name="order"]').val(newOrder);
            getUserByWmpLevel(form, table);
        }

        function resetOrder() {
            jQuery('[data-orderby]').addClass('sortable');
            jQuery('[data-orderby]').removeClass('sorted');
        }

        function getUserByWmpLevel(form, table) {
            const data = $(form).serialize();
            blockUi(true)
            $(findCount).text('').hide()
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                processData: false,
                data: data,
                success: function (response) {
                    let wpmLevel = $('#wpm_level').val();
                    $('#wpm_level_users').val(wpmLevel);
                    let data = JSON.parse(response);
                    table.innerHTML = data.html;
                    if (data.count) {
                        $(findCount).text(`Найдено: ${data.count}`).show()
                    }
                }
            }).always(function () {
                blockUi(false)
            })
        }


        $("#mlDayAfterCourseEndForm").on("submit", saveDayAfterCourseEnd);

        function saveDayAfterCourseEnd(event) {
            event.preventDefault();
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: $(this).serialize(),
                success: function (json) {
                    let data = JSON.parse(json);
                    if (data.status === "success") {
                        alert("Настройка сохранена");
                    } else {
                        alert("Произошла ошибка");
                    }
                }
            })
        }

        function blockUi(state) {
            let controls = ['wpm_level', 'activeWPMLevel', 'user_email', 'ml_select_user', 'ml_certificate_delivery',]
            if (state) {
                loader.show();
                $(certificateForm).addClass('table-loading')
                controls.forEach(function (id) {
                    $(`#${id}`).attr('disabled', true)
                })
            } else {
                loader.hide();
                $(certificateForm).removeClass('table-loading')
                controls.forEach(function (id) {
                    $(`#${id}`).attr('disabled', false)
                })
            }
        }

        $('.js-select2').select2({
            placeholder: 'Выберите уровень доступа',
            language: {
                noResults: function () {
                    return "Не найдено"
                }
            }
        })
    })(jQuery);

</script>
