<?php
/**
 * @global array $categoryOptions
 * @global array $courseOptions
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
</style>
<div class="container-fluid pt-5 mblc_options">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="settingsMailer-tab" data-toggle="tab" href="#settingsMailer" role="tab"
               aria-controls="settingsMailer" aria-selected="true">
                Шаблон для оповещения
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="settingsText-tab" data-toggle="tab" href="#settingsText" role="tab"
               aria-controls="settingsText" aria-selected="false">
                Тексты
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="settingsColor-tab" data-toggle="tab" href="#settingsColor" role="tab"
               aria-controls="settingsColor" aria-selected="false">Цвета</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="settingsShortcode-tab" data-toggle="tab" href="#settingsShortcode" role="tab"
               aria-controls="settingsShortcode" aria-selected="false">Список шорткодов</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="settingsCustomFunc-tab" data-toggle="tab" href="#settingsCustomFunc" role="tab"
               aria-controls="settingsCustomFunc" aria-selected="false">Дополнительные функции</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="settingsMailer" role="tabpanel" aria-labelledby="settingsMailer-tab">
            <h1 class="mt-4">Шаблон для оповещения</h1>
            <div class="alert alert-danger" role="alert" style="display: none"></div>
            <form id="templateOptions" class="needs-validation double_save simple_alert" novalidate>
                <div class="form-group">
                    <label for="mailer_topic">Тема письма</label>
                    <input type="text" name="cert_mailer_topic"
                           value="<?= mblc_get_option_with_default('cert_mailer_topic') ?>"
                           required class="form-control" id="mailer_topic"/>
                </div>
                <div class="form-group">
                    <label for="mailer_text">Текст письма</label>
                    <?php
                    $mailerText = mblc_get_option_with_default('cert_mailer_text');
                    wp_editor(
                        wp_unslash($mailerText), 'cert_mailer_text',
                        array('textarea_rows' => 10, 'editor_class' => 'cert_mailer_text')
                    ); ?>
                </div>
                <input type="hidden" name="action" value="save_cert_mailer">
                <div>
                    <div class="text-left mb-3">
                        [m_certificate_pdf] - ссылка на скачивание сертификата в формате PDF <br>
                        <?php if (mblc_jpg_enabled()): ?>
                            [m_certificate_jpg] - ссылка на скачивание сертификата в формате JPEG <br>
                        <?php endif; ?>
                        <?php wpm_auto_login_shortcodes_tips(); ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
        <div class="tab-pane fade" id="settingsText" role="tabpanel" aria-labelledby="settingsText-tab">
            <form action="" class="mt-4 needs-validation" novalidate data-option-form>
                <div class="alert alert-danger" role="alert" style="display: none"></div>
                <input type="hidden" name="action" value="mblc_save_option">
                <?php foreach (array_keys(mblc_get_default_text()) as $ind => $field): ?>
                    <?php $val = mblc_get_option_with_default($field); ?>
                    <div class="form-group d-flex align-items-center">
                        <span style="width: 24px;" class="mr-2"><?= $ind + 1 ?>.</span>
                        <label for="mblc_<?= esc_attr($field) ?>" style="display:none;"></label>
                        <input type="text" class="form-control" id="mblc_<?= esc_attr($field) ?>"
                               value="<?= esc_attr($val) ?>"
                               name="option[<?= esc_attr($field) ?>]">
                    </div>
                <?php endforeach; ?>
                <div>
                    <button class="btn btn-primary" type="submit">
                        <span class="spinner-border spinner-border-sm"
                              style="display: none"
                              role="status" aria-hidden="true"></span>
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
        <div class="tab-pane fade" id="settingsColor" role="tabpanel" aria-labelledby="settingsColor-tab">
            <form action="" class="mt-4 needs-validation" novalidate data-option-form>
                <div class="alert alert-danger" role="alert" style="display: none"></div>
                <div class="form-group">
                    <label style="display:block;" for="btnBgColor">Цвет кнопок</label>
                    <input id="btnBgColor" class="color" name="option[btn_bg]"
                           value="<?= esc_attr(mblc_get_option_with_default('btn_bg')) ?>">
                </div>
                <div class="form-group">
                    <label style="display:block;" for="btnTextColor">Цвет текста на кнопках</label>
                    <input id="btnTextColor" class="color" name="option[btn_text_color]"
                           value="<?= esc_attr(mblc_get_option_with_default('btn_text_color')) ?>">
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">
                        <span class="spinner-border spinner-border-sm"
                              style="display: none"
                              role="status" aria-hidden="true"></span>
                        Сохранить
                    </button>
                </div>
                <input type="hidden" name="action" value="mblc_save_option">
            </form>
        </div>
        <div class="tab-pane fade" id="settingsShortcode" role="tabpanel" aria-labelledby="settingsShortcode-tab">
            <div class="mt-4">
                <div class="mb-3">
                    [m_certificate_pdf] - ссылка на скачивание сертификата в формате PDF
                </div>
                <?php if (mblc_jpg_enabled()): ?>
                    <div class="mb-3">
                        [m_certificate_jpg] - ссылка на скачивание сертификата в формате JPEG
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    [m_certificate_verification] - форма проверки подлинности
                </div>
                <div class="mb-3">
                    [m_certificate_list] - список доступных сертификатов
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="settingsCustomFunc" role="tabpanel" aria-labelledby="settingsCustomFunc-tab">
            <form class="mt-4 needs-validation" novalidate data-option-form>
                <input type="hidden" name="action" value="mblc_save_option">
                <div class="alert alert-danger" role="alert" style="display: none"></div>
                <div class="form-group">
                    <label style="cursor:pointer;" class="form-check-label">
                        <input type="hidden" name="option[custom_fields_enabled]" value="off">
                        <input type="checkbox" value="on"
                               <?= checked(mblc_get_option_with_default('custom_fields_enabled'), 'on') ?>
                               name="option[custom_fields_enabled]"> Включить дополнительные поля</label>
                </div>
                <div class="form-group">
                    <?php if (!extension_loaded('imagick')): ?>
                        <div class="notice notice-warning ml-0">
                            <p>
                                Формат jpg не доступен, потому что на вашем хостинге не установлено расширение imagemagick.
                                <br>
                                Обратитесь к техническому специалисту или напишите в службу поддержки вашего хостинга,
                                с просьбой установки данного расширения для php
                            </p>
                        </div>
                    <?php endif; ?>
                    <label style="cursor:pointer;" class="form-check-label">
                        <input type="hidden" name="option[jpg_enabled]" value="off">
                        <input type="checkbox" value="on"
                            <?= checked(mblc_get_option_with_default('jpg_enabled'), 'on') ?>
                               name="option[jpg_enabled]"> Включить JPEG</label>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">
                        <span class="spinner-border spinner-border-sm" style="display: none"
                              role="status" aria-hidden="true"></span>
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
