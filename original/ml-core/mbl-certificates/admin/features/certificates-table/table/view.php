<?php
/**
 * @global $perPage
 * @global $pageNum
 * @global $certificates
 * @global $totalPages
 * @global $total
 */
?>

<div class="container-fluid">
    <h1 class="mt-4">Выданные сертификаты</h1>
    <form id="filterForm">
        <div class="row">
            <div class="col-lg-12 col-xl-9">
                <div class="card p-0" style="max-width: unset;">
                    <div class="card-header">
                        <h6 class="m-0">
                            Фильтры
                            <a data-toggle="collapse" href="#collapse-filter" aria-expanded="true"
                               aria-controls="collapse-filter"
                               class="d-block float-right" id="heading-filter">
                                <i class="fa fa-chevron-down pull-right"></i>
                            </a>
                        </h6>
                    </div>
                    <div class="card-body p-3 collapse show" id="collapse-filter" aria-labelledby="heading-filter">
                        <div class="form-row">
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label for="certificateTemplateId">Шаблон сертификата</label>
                                    <select class="form-control" id="certificateTemplateId"
                                            name="filter[certificate_template_id]">
                                        <option value="">Не выбран</option>
                                        <?php foreach (CertificateTemplate::getCertificateTemplates() as $template): ?>
                                            <?php
                                            $selected = '';
                                            if (isset($filters['certificate_template_id']) &&
                                                $filters['certificate_template_id'] === $template->id) {
                                                $selected = ' selected';
                                            } ?>
                                            <option value="<?= $template->id ?>"<?= $selected ?>><?= $template->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label for="wpmlevel_id">Уровень доступа</label>
                                    <select class="js-select2 form-control" id="wpmlevel_id" name="filter[wpmlevel_id]">
                                        <option value="">Не выбран</option>
                                        <?php foreach ((new MBLC_WpmLevels())->query() as $wpm_level): ?>
                                            <option value="<?= esc_attr($wpm_level->term_id) ?>">
                                                <?= $wpm_level->name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <!-- div class="col-xl-4">
                                <label>Дата создания</label>
                                <div class="form-row">
                                    <div class="col-xl-6 form-group">
                                        <input type="date" class="form-control" name="filter[create_date][from]">
                                    </div>
                                    <div class="col-xl-6 form-group">
                                        <input type="date" class="form-control" name="filter[create_date][to]">
                                    </div>
                                </div>
                            </div -->
                            <div class="col-xl-4">
                                <label>Дата выдачи</label>
                                <div class="form-row">
                                    <div class="col-xl-6 form-group">
                                        <input data-toggle="datepicker" autocomplete="off"
                                               placeholder="дд.мм.гггг" class="form-control" name="filter[date_issue][from]">
                                    </div>
                                    <div class="col-xl-6 form-group">
                                        <input data-toggle="datepicker" autocomplete="off"
                                               placeholder="дд.мм.гггг" class="form-control" name="filter[date_issue][to]">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label for="">Кем выдан</label>
                                    <select class="form-control" id="responsiblePersonId"
                                            name="filter[responsible_person]">
                                        <option value="">Выберите</option>
                                        <?php foreach (ResponsiblePerson::getResponsiblePersons() as $person): ?>
                                            <option value="<?= $person->ID ?>"><?= $person->data->user_login ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col form-group" style="padding-top: 24px;">
                                <div style="margin-top: .5rem; text-align: right">
                                    <button class="btn btn-secondary mr-3" type="button" data-action="reset_filters">
                                        Сбросить
                                    </button>
                                    <button class="btn btn-primary" type="button" data-action="apply_filters">
                                        Применить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-3">
                <div class="card p-0" style="max-width: unset;">
                    <div class="card-header">
                        <h6 class="m-0">
                            Поиск по Email
                            <a data-toggle="collapse" href="#collapse-search" aria-expanded="true"
                               aria-controls="collapse-search"
                               class="d-block float-right" id="heading-search">
                                <i class="fa fa-chevron-down pull-right"></i>
                            </a>
                        </h6>
                    </div>
                    <div class="card-body p-3 collapse show" id="collapse-search" aria-labelledby="heading-search">
                        <div class="form-group">
                            <label for="userEmail">Почта пользователя</label>
                            <input class="form-control" type="text" id="userEmail" name="search_by_email">
                            <div class="text-right mt-2" style="padding-top: 30px; margin-bottom: 1rem; margin-top: 1rem!important;">
                                <button class="btn btn-primary" type="button" data-action="search_by_email">Поиск</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mblc-table card mb-4 p-0" style="max-width: 100%">
            <span class="table-loading-icon fas fa-spin fa-spinner"></span>
            <div class="card-header py-3">
                <p class="m-0 font-weight-bold text-primary">
                    <!-- Выданные сертификаты -->
                    <div class="d-inline-flex">
                        <div>
                            <label for="" class="text-secondary" style="font-size: 1rem; font-weight: normal;">Удалить выбранные</label><br>
                            <button class="btn btn-danger" data-action="delete" type="button">
                                <span class="" style="font-weight: normal;">Удалить</span></button>
                        </div>
                        <div class="ml-5">
                            <label for="" class="text-secondary" style="font-size: 1rem; font-weight: normal;">Сменить шаблон</label><br>
                            <select name="new_template" id="set_new_template" style="height: 38px;">
                                <option value="">Не выбран</option>
                                <?php foreach (CertificateTemplate::getCertificateTemplates() as $template): ?>
                                    <option value="<?= $template->id ?>"><?= $template->name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-success" data-action="set_new_template" type="button">
                                <span class="" style="font-weight: normal;">Применить</span></button>
                        </div>
                    </div>
                </p>
            </div>
            <div class="card-body mbl-certificate-table">
                <div class="table-responsive">
                    <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_length" id="dataTable_length">
                                    <label>Показывать по <select
                                            name="per_page" aria-controls="dataTable"
                                            class="custom-select custom-select-sm form-control form-control-sm">
                                            <option<?= $perPage === 10 ? ' selected' : ''; ?> value="10">10</option>
                                            <option<?= $perPage === 25 ? ' selected' : ''; ?> value="25">25</option>
                                            <option<?= $perPage === 50 ? ' selected' : ''; ?> value="50">50</option>
                                            <option<?= $perPage === 100 ? ' selected' : ''; ?> value="100">100</option>
                                        </select> на странице</label>
                                    <input type="hidden" name="page_num" value="<?= $pageNum ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="hidden" name="orderby" value="create_date">
                                <input type="hidden" name="order" value="desc">
                                <table class="table table-hover table-bordered dataTable" id="dataTable"
                                       role="grid" aria-describedby="dataTable_info"
                                       style="table-layout:fixed; width: 100%; border: 0; border-bottom: 1px solid #000;">
                                    <thead>
                                    <tr role="row">
                                        <th class="check-column mbl-th-check">
                                            <input type="checkbox" class="form-check">
                                        </th>
                                        <th class="sortable asc mbl-th" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="user_login">
                                            Клиент
                                        </th>
                                        <th class="sortable asc mbl-th" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="graduate_last_name">
                                            Фамилия
                                        </th>
                                        <th class="sortable asc mbl-th" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="graduate_first_name">
                                            Имя
                                        </th>
                                        <th class="sortable asc mbl-th" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="graduate_surname">
                                            Отчество
                                        </th>
                                        <th class="sortable asc mbl-th mbl-th-wpmlevel" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="wpmlevel_id">
                                            Уровень доступа
                                        </th>
                                        <th class="sorted desc mbl-th" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="create_date">
                                            Создан
                                        </th>
                                        <th class="sortable asc mbl-th" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="date_issue">
                                            Дата выдачи
                                        </th>
                                        <th class="sortable asc mbl-th" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="responsible_person">
                                            Кем выдан
                                        </th>
                                        <th class="sortable asc mbl-th" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="template_id">
                                            Шаблон
                                        </th>
                                        <th class="mbl-th" rowspan="1" colspan="1">
                                            Просмотр
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php renderTbody($certificates) ?>
                                    </tbody>
                                    <!-- tfoot>
                                    <tr>
                                        <th class="check-column">
                                            <input type="checkbox" class="form-check">
                                        </th>
                                        <th rowspan="1" colspan="1">Логин пользователя</th>
                                        <th rowspan="1" colspan="1">Фамилия</th>
                                        <th rowspan="1" colspan="1">Имя</th>
                                        <th rowspan="1" colspan="1">Отчество</th>
                                        <th rowspan="1" colspan="1">Название</th>
                                        <th rowspan="1" colspan="1">Дата выдачи</th>
                                        <th rowspan="1" colspan="1">Кем выдан</th>
                                        <th rowspan="1" colspan="1">Шаблон</th>
                                        <th rowspan="1" colspan="1">Просмотр</th -->
                                        <!-- th rowspan="1" colspan="1">Дата создания</th -->
                                    <!-- /tr>
                                    </tfoot -->
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite">
                                    <?= renderResultCount($pageNum, $perPage, $totalPages, $total); ?>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                                    <?php
                                    echo getPagination([
                                        'link' => "?page_num=",
                                        'page' => $pageNum,
                                        'total' => $totalPages
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
