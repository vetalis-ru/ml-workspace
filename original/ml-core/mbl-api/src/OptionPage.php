<?php

namespace Mbl\Api;

class OptionPage
{
    public function __construct()
    {
    }

    public function register()
    {
        add_action( 'mbl_options_items_after', [ __CLASS__, 'menu' ], 140 );
        add_action( 'mbl_options_content_after', [ __CLASS__, 'options_content' ], 10, 1 );
        add_action('admin_head', [__CLASS__, 'css']);
    }

    static public function menu()
    {
        ?>
        <li><a href="#tab-mbapi" class="mbapi-color-tab">Webhooks</a></li>
        <?php
    }

    static public function options_content()
    {
        $webhooks = array_map(fn($i) => $i->toArray(), (new Webhooks())->list());
        $actions = (new Actions())->toArray();
        ?>
        <div id="tab-mbapi" class="tab mbapi-color-content">
            <div class="wpm-tab-content">
                <div class="mbapi-notice" style="display: none">
                    <p></p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
                </div>

                <div data-wh-loading class="loader-ellipse">
                    <span class="loader-ellipse__dot"></span><span class="loader-ellipse__dot"></span>
                    <span class="loader-ellipse__dot"></span><span class="loader-ellipse__dot"></span>
                </div>

                <div id="webhooks-items" class="mbapi-items">
                    <?php foreach ($webhooks as $hook): ?>
                        <div class="mbapi-item" data-wh-id="<?= $hook['id'] ?>">
                            <input type="hidden" name="hook[<?= $hook['id'] ?>][sort]" value="<?= $hook['sort'] ?>">
                            <input type="hidden" name="hook[<?= $hook['id'] ?>][id]" value="<?= $hook['id'] ?>">
                            <div>
                                <label for="webhook-url-<?= $hook['id'] ?>">URL</label>
                                <div><input class="mbapi-inp" id="webhook-url-<?= $hook['id'] ?>" value="<?= $hook['destination'] ?>" name="hook[<?= $hook['id'] ?>][destination]" type="text"></div>
                            </div>
                            <div class="mbapi-action">
                                <label for="webhook-action-<?= $hook['id'] ?>">События</label>
                                <?php /*
                                <div>
                                    <select id="webhook-action-<?= $hook['id'] ?>" name="hook[<?= $hook['id'] ?>][action]" data-multiple-select multiple>
                                        <option <?= selected(in_array('free_registration_form', $hook['action'])) ?> value="free_registration_form">Форма бесплатной регистрации</option>
                                        <option <?= selected(in_array('bulk_operations_reg', $hook['action'])) ?> value="bulk_operations_reg">Массовые операции (регистрации)</option>
                                        <option <?= selected(in_array('bulk_operations_add', $hook['action'])) ?> value="bulk_operations_add">Массовые операции (добавление)</option>
                                        <option <?= selected(in_array('auto_registration', $hook['action'])) ?> value="auto_registration">Авторегистрация</option>
                                        <option <?= selected(in_array('activation_page', $hook['action'])) ?> value="activation_page">Добавление ключа на странице активации</option>
                                        <option <?= selected(in_array('profile_page_self', $hook['action'])) ?> value="profile_page_self">Добавление ключа в профиле</option>
                                        <option <?= selected(in_array('profile_page_admin', $hook['action'])) ?> value="profile_page_admin">Добавление доступа администратором</option>
                                        <option <?= selected(in_array('after_auto_training_passed', $hook['action'])) ?> value="after_auto_training_passed">После прохождения автотренинга</option>
                                    </select>
                                </div>
                                */ ?>
                                <div class="checkboxes_dropdown js-control-checkboxes_dropdown checkboxes_dropdown_index">
                                    <div class="checkboxes_dropdown__list custom-scroll ">
                                        <div class="checkboxes_dropdown__list__wrapper__inner">
                                            <div class="checkboxes_dropdown__item">
                                                <label class="control-checkbox checkboxes_dropdown__label js-master-checkbox-wrapper">
                                                    <span class="control-checkbox__body">
                                                        <input type="checkbox"
                                                               class="js-form-changes-skip js-master-checkbox"
                                                               >
                                                        <span class="control-checkbox__helper js-select-all-control"></span>
                                                    </span>
                                                    <span class="control-checkbox__text element__text js-select-all-text checkboxes_dropdown__label_title checkboxes_dropdown__label_title-not_active"
                                                         title="Выбрать всё">Выбрать всё
                                                    </span>
                                                </label>
                                            </div>
                                            <?php foreach ($actions as $v => $l): ?>
                                                <div class="checkboxes_dropdown__item">
                                                    <label class="control-checkbox checkboxes_dropdown__label">
                                                        <span class="control-checkbox__body">
                                                            <input type="checkbox"
                                                                   class="js-item-checkbox"
                                                                <?= checked(in_array($v, $hook['action']) || in_array('all', $hook['action'])) ?>
                                                                    name="hook[<?= $hook['id'] ?>][action][]"
                                                                   value="<?= $v ?>"
                                                            >
                                                            <span class="control-checkbox__helper "></span>
                                                        </span>
                                                        <span class="control-checkbox__text element__text checkboxes_dropdown__label_title"
                                                             title="<?= $l ?>"><?= $l ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="checkboxes_dropdown__title_wrapper ">
                                        <label class="control-checkbox checkboxes_dropdown__checkbox_master icon-checkbox js-master-checkbox-wrapper">
                                        <span class="control-checkbox__body">
                                            <input type="checkbox"
                                                   class="js-form-changes-skip js-master-checkbox"
                                                   name="hook[<?= $hook['id'] ?>][action][]"
                                                   value="all">
                                            <span class="control-checkbox__helper js-select-all-control"></span>
                                        </span>
                                        </label>
                                        <div class="checkboxes_dropdown__title-selected">
                                            <div class="checkboxes_dropdown__title"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mbapi-remove-wr">
                                <button class="mbapi-remove" data-wh-rm type="button"><span class="dashicons dashicons-trash"></span></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="button button-primary mbapi-add-button" data-wh-add type="button">
                    <span class="dashicons dashicons-plus"></span>  <span>Добавить</span>
                </button>
                <button class="button button-primary mbapi-add-button" data-wh-save type="button">
                    <span>Сохранить</span>
                </button>
            </div>
            <script>
                (function ($) {
                    var container =  $('#webhooks-items');
                    var loader = $('[data-wh-loading]');
                    var saveBtn = $('[data-wh-save]');
                    var addBtn = $('[data-wh-add]');
                    var notice = $('.mbapi-notice');
                    var counter = 1;
                    var UI = {
                        showNotice(text) {
                            notice.find('p').text(text)
                            notice.show()
                        },
                        hideNotice() {
                            notice.hide()
                        },
                        blocking() {
                            this.hideNotice()
                            loader.show()
                            saveBtn.attr('disabled', true)
                            addBtn.attr('disabled', true)
                            $('[data-wh-rm]').attr('disabled', true)
                        },
                        unblocking() {
                            loader.hide()
                            saveBtn.attr('disabled', false)
                            addBtn.attr('disabled', false)
                            $('[data-wh-rm]').attr('disabled', false)
                        },
                    }
                    var add = function () {
                        var id = counter++;
                        var item = $(
                            `<div class="mbapi-item" data-wh-new="${id}">
                                <input type="hidden" name="hook[new_item_${id}][sort]" value="${id}">
                                <input type="hidden" name="hook[new_item_${id}][id]" value="new_item_${id}">
                                <div>
                                    <label for="webhook-url-new${id}">URL</label>
                                    <div><input class="mbapi-inp" id="webhook-url-new${id}" name="hook[new_item_${id}][destination]" type="text"></div>
                                </div>
                                <div class="mbapi-action">
                                    <label for="webhook-action-new${id}">События</label>
                                    <div class="checkboxes_dropdown js-control-checkboxes_dropdown checkboxes_dropdown_index">
                                        <div class="checkboxes_dropdown__list custom-scroll ">
                                            <div class="checkboxes_dropdown__list__wrapper__inner">
                                                <div class="checkboxes_dropdown__item">
                                                    <label class="control-checkbox checkboxes_dropdown__label js-master-checkbox-wrapper">
                                                        <span class="control-checkbox__body">
                                                            <input type="checkbox"
                                                                   class="js-form-changes-skip js-master-checkbox"
                                                                   >
                                                            <span class="control-checkbox__helper js-select-all-control"></span>
                                                        </span>
                                                        <span class="control-checkbox__text element__text js-select-all-text checkboxes_dropdown__label_title checkboxes_dropdown__label_title-not_active"
                                                             title="Выбрать всё">Выбрать всё
                                                        </span>
                                                    </label>
                                                </div>
                                                <?php foreach ($actions as $v => $l): ?>
                                                    <div class="checkboxes_dropdown__item">
                                                        <label class="control-checkbox checkboxes_dropdown__label">
                                                            <span class="control-checkbox__body">
                                                                <input type="checkbox"
                                                                       class="js-item-checkbox"
                                                                       value="<?= $v ?>"
                                                                       name="hook[new_item_${id}][action][]"
                                                                >
                                                                <span class="control-checkbox__helper"></span>
                                                            </span>
                                                            <span class="control-checkbox__text element__text checkboxes_dropdown__label_title"
                                                                 title="<?= $l ?>"><?= $l ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="checkboxes_dropdown__title_wrapper ">
                                            <label class="control-checkbox checkboxes_dropdown__checkbox_master icon-checkbox js-master-checkbox-wrapper">
                                            <span class="control-checkbox__body">
                                                <input type="checkbox"
                                                       class="js-form-changes-skip js-master-checkbox"
                                                       name="hook[new_item_${id}][action][] "
                                                       value="all"
                                                >
                                                <span class="control-checkbox__helper js-select-all-control"></span>
                                            </span>
                                            </label>
                                            <div class="checkboxes_dropdown__title-selected">
                                                <div class="checkboxes_dropdown__title"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mbapi-remove-wr">
                                    <button class="mbapi-remove" data-wh-rm type="button"><span class="dashicons dashicons-trash"></span></button>
                                </div>
                            </div>
                            `
                        );
                        container.append(item);
                    };
                    var remove = function (event) {
                        var target = $(event.target)
                        var item = target.closest('.mbapi-item')
                        if(item.attr('data-wh-new')) {
                            item.remove()
                        } else {
                            UI.blocking()
                            var id = item.attr('data-wh-id')
                            $.ajax({
                                url: `/wp-json/mbl/v1/webhook/${id}`,
                                type: 'DELETE',
                                headers: {
                                    'X-WP-Nonce': '<?= wp_create_nonce( 'wp_rest' ) ?>'
                                }
                            }).done(function () {
                                item.remove()
                            })
                            .fail(function() {
                                UI.showNotice('Попробуйте позже')
                            })
                            .always(function () {
                                UI.unblocking()
                            });
                        }
                    };
                    var save = function () {
                        UI.blocking()
                        var formData = form_json(container);

                        var isEmpty = true;
                        for (var key of formData.entries()) {
                            isEmpty = false;
                            break;
                        }
                        if (isEmpty) {
                            UI.unblocking()
                            return;
                        }

                        $.ajax({
                            url: '/wp-json/mbl/v1/webhook/form/',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-WP-Nonce': '<?= wp_create_nonce( 'wp_rest' ) ?>'
                            }
                        })
                            .done(function () {
                                window.location.reload()
                            })
                            .fail(function(data) {
                                if(data.status === 400) {
                                    UI.showNotice(data.responseJSON.message)
                                } else {
                                    UI.showNotice('Попробуйте позже')
                                }
                               UI.unblocking()
                            });
                    };
                    var openControl = function (event) {
                        if (event.target.closest('.js-master-checkbox-wrapper')) {
                            return;
                        }

                        var control = $(event.target).closest('.js-control-checkboxes_dropdown');
                        control.addClass('expanded');
                        control.find('.checkboxes_dropdown__list').show();
                        onCloseCallback(control)
                    };
                    var onCloseCallback = function (element) {
                        var isFirst = false;
                        var callback = function (event) {
                            if (!isFirst) {
                                isFirst = true
                                return
                            }
                            var current = $(event.target).closest(".js-control-checkboxes_dropdown")

                            if (current.get(0) !== element.get(0)) {
                                $(document).off("click", callback);
                                element.removeClass('expanded');
                                element.find('.checkboxes_dropdown__list').hide();
                            }
                        };
                        $(document).on("click", callback);
                    };
                    var dropdown_render = function () {
                        var elements = '';
                        var html = '';
                        var title = '';
                        var dropdown = $(this).closest('.js-control-checkboxes_dropdown');

                        dropdown.find('.js-item-checkbox:checked').each(function () {
                            var title = $(this).closest('label').find('.control-checkbox__text').attr('title')
                            elements += `<div class="checkboxes_dropdown__title-item">${title}</div>`;
                        })

                        if (dropdown.find('.js-item-checkbox:checked').length === dropdown.find('.js-item-checkbox').length) {
                            title = 'Снять выделение';
                            dropdown.find('.js-select-all-control').removeClass('control-checkbox__helper_minus')
                            dropdown.find('.js-form-changes-skip').prop('indeterminate', false).prop('checked', true)
                            html = '<div class="checkboxes_dropdown__title-item">Все</div>'
                        } else if(dropdown.find('.js-item-checkbox:checked').length) {
                            title = 'Снять выделение';
                            dropdown.find('.js-form-changes-skip').prop('indeterminate', true).prop('checked', false)
                            dropdown.find('.js-select-all-control').addClass('control-checkbox__helper_minus')
                            html = elements
                        } else {
                            title = 'Выбрать всё';
                            html = '<div class="checkboxes_dropdown__title-item">Все</div>'
                            dropdown.find('.js-form-changes-skip').prop('indeterminate', false).prop('checked', false)
                            dropdown.find('.js-select-all-control').removeClass('control-checkbox__helper_minus')
                        }
                        dropdown.find('.js-select-all-text').attr('title', title).text(title)
                        dropdown.find('.checkboxes_dropdown__title').html(html)
                    };
                    var ready = function () {
                        loader.hide()
                        $('[data-multiple-select]').select2({width: 'full'});
                        $('[data-wh-add]').on('click', add);
                        $('[data-wh-save]').on('click', save);
                        container.on('click', '[data-wh-rm]', remove);
                        notice.on('click', '.notice-dismiss', dismiss);
                        container.on('click', '.checkboxes_dropdown__title_wrapper', openControl);
                        container.on('change', '.js-form-changes-skip', function (event) {
                            var dropdown = $(this).closest('.js-control-checkboxes_dropdown');
                            if ($(this).is(':checked')) {
                                if (dropdown.find('.js-item-checkbox:checked').length) {
                                    dropdown.find('.js-select-all-text').attr('title', 'Выбрать всё').text('Выбрать всё')
                                    dropdown.find('.checkboxes_dropdown__title').html('<div class="checkboxes_dropdown__title-item">Все</div>')
                                    dropdown.find('.js-item-checkbox').prop('checked', false)
                                    dropdown.find('.js-select-all-control').removeClass('control-checkbox__helper_minus')
                                    dropdown.find('.js-form-changes-skip').prop('checked', false)
                                } else {
                                    dropdown.find('.js-select-all-text').attr('title', 'Снять выделение').text('Снять выделение')
                                    dropdown.find('.checkboxes_dropdown__title').html('<div class="checkboxes_dropdown__title-item">Все</div>')
                                    dropdown.find('.js-item-checkbox').prop('checked', true)
                                    dropdown.find('.js-select-all-control').removeClass('control-checkbox__helper_minus')
                                    dropdown.find('.js-form-changes-skip').prop('checked', true)
                                }
                            } else {
                                dropdown.find('.js-select-all-text').attr('title', 'Выбрать всё').text('Выбрать всё')
                                dropdown.find('.checkboxes_dropdown__title').html('<div class="checkboxes_dropdown__title-item">Все</div>')
                                dropdown.find('.js-item-checkbox').prop('checked', false)
                                dropdown.find('.js-select-all-control').removeClass('control-checkbox__helper_minus')
                                dropdown.find('.js-form-changes-skip').prop('checked', false)
                            }
                        });
                        container.on('change', '.js-item-checkbox', dropdown_render);
                        container.find('.js-control-checkboxes_dropdown').each(dropdown_render)
                    };
                    var form_json = function (root) {
                        var formElements = root.find('[name]')
                        var formData = new FormData();
                        formElements.each(function() {
                            if ($(this).is('[type=checkbox]')) {
                                if ($(this).is(':checked')) {
                                    formData.append($(this).attr('name'), $(this).val());
                                }
                            } else {
                                formData.append($(this).attr('name'), $(this).val());
                            }
                        });

                        return formData
                    };
                    var dismiss = function () {
                      UI.hideNotice()
                    };
                    $(document).ready(ready);
                })(jQuery);
            </script>
        </div>
        <?php
    }

    static public function css() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'wpm-options') {
            return;
        }
        ?>
        <style id="mbapi-admin-css">
            .wpm-tabs .tabs-nav .ui-tabs-active .mbapi-color-tab {
                background-color: #A0D5CD !important;
            }
            .mbapi-color-content {
                min-height: 900px !important;
                box-shadow: #A0D5CD 4px 4px 0 0 !important;
                border: 2px solid #32373c !important;
                background-color: #fff !important;
            }
            .mbapi-color-content .wpm-tab-content {
                overflow: auto;
                min-height: 900px!important;
            }
            .mbapi-items {
                margin-bottom: 25px;
            }
            .mbapi-items .mbapi-item:not(:last-child){
                margin-bottom: 10px;
            }
            .mbapi-add-button {
                display: inline-flex!important;
                align-items: center;
            }
            .mbapi-add-button .dashicons{
                width: auto;
                height: auto;
            }
            .mbapi-item {
                display: flex;
                align-items: flex-start;
                column-gap: 10px;
            }
            .mbapi-color-content .select2-selection {
            }
            .mbapi-color-content .select2 {
                width: 100%;
            }
            .mbapi-inp {
                margin-top: 1px;
                height: 32px;
                min-width: 320px
            }
            .mbapi-action {
                flex-grow: 1;
            }
            .mbapi-color-content .select2-container .select2-search--inline .select2-search__field {
                margin-top: 0;
            }
            .mbapi-color-content .select2-container--default .select2-selection--multiple .select2-selection__choice {
                margin-bottom: 0;
            }
            .mbapi-remove-wr {
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-top: 17px;
            }
            .mbapi-remove {
                background: none;
                border: 0;
                cursor: pointer;
            }
            .mbapi-notice {
                position: relative;
                background: #fff;
                box-shadow: 1px 1px 3px 0px rgba(0,0,0,.04);
                margin-bottom: 10px;
                padding: 1px 12px;
                border-left: 4px solid #d63638;
            }
            .checkboxes_dropdown {
                display: inline-block;
                z-index: 0;
                position: relative;
                line-height: 1;
                width: 100%;
            }
            .checkboxes_dropdown.expanded {
                z-index: 2;
            }
            .checkboxes_dropdown__title_wrapper {
                position: relative;
                color: #363b44;
                padding: 8px 5px 7px 38px;
                border: 1px solid #c5c5c5;
                border-radius: 3px;
                cursor: pointer;
                min-height: 15px;
                background: #fff;
                box-shadow: inset 0 -1px 0 0 #f2f2f2;
            }
            .checkboxes_dropdown__checkbox_master.control-checkbox {
                position: absolute;
                top: 8px;
                left: 8px;
            }
            .checkboxes_dropdown .checkboxes_dropdown__checkbox_master {
                top: 5px;
            }
            .control-checkbox {
                display: inline-flex;
                align-items: center;
                margin: 0 7px 0 0;
                padding: 0;
                border: none;
                cursor: pointer;
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
            }
            .control-checkbox__body {
                 display: inline-flex;
             }
            .checkboxes_dropdown__title-selected {
                display: flex;
                overflow: hidden;
                width: 100%;
                text-overflow: ellipsis;
            }
            .checkboxes_dropdown_icon {
                position: absolute;
                top: 15px;
                right: -3px;
                transform: scale(1.2);
                width: 10px;
            }
            .control-checkbox input {
                position: absolute;
                opacity: 0;
                z-index: 4;
                width: 20px;
                height: 20px;
                cursor: pointer;
            }
            .control-checkbox__helper {
                position: relative;
                width: 18px;
                height: 18px;
                display: inline-flex;
                z-index: 3;
                background-color: #ffffff;
                border: 1px solid #e8eaeb;
                border-radius: 3px;
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
            }
            :root {
                --icon-checkbox-minus: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3C!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'%3E%3Csvg version='1.1' baseProfile='full' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns:ev='http://www.w3.org/2001/xml-events' width='10' height='4' viewBox='0 0 10 4'%3E%3Cg transform='scale(NaN)' %3E%3Cpath fill-rule='evenodd' fill='%23363b44' d='M-0.444,0.111 C-0.444,0.111 1.809,-0.139 4.046,-0.139 C6.253,-0.139 8.445,0.111 8.445,0.111 C8.445,0.111 8.445,1.583 8.445,1.583 C8.445,1.583 6.305,2.056 4.125,2.056 C1.862,2.056 -0.444,1.583 -0.444,1.583 C-0.444,1.583 -0.444,0.111 -0.444,0.111 Z'/%3E%3C/g%3E%3C/svg%3E");
                --icon-checkbox-checked-mark: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' version='1.1' id='_x2014__Item_1_' x='0px' y='0px' width='12px' height='11px' viewBox='0 0 12 11' enable-background='new 0 0 12 11' xml:space='preserve'%3E%3Cg id='check.svg'%3E%3Cg%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' fill='%23363b44' d='M11.476,0.151c-0.377-0.296-0.997-0.139-1.359,0.324L5.153,6.53 C5.077,6.624,4.884,6.657,4.785,6.595L1.723,4.688C1.24,4.342,0.624,4.366,0.349,4.753c-0.268,0.378-0.18,0.994,0.209,1.464 l3.509,4.234C4.361,10.805,4.742,11,5.14,11c0.465,0,0.895-0.266,1.177-0.728l5.264-8.615C11.916,1.109,11.871,0.462,11.476,0.151 z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }
            .control-checkbox input:indeterminate ~ .control-checkbox__helper.control-checkbox__helper_minus, .control-checkbox input:checked ~ .control-checkbox__helper.control-checkbox__helper_minus {
                background: #ffffff var(--icon-checkbox-minus) no-repeat;
                background-size: 75%;
                background-position: 75% 60%;
            }
            .control-checkbox input:checked ~ .control-checkbox__helper:not(.control-checkbox__helper_minus) {
                background: #ffffff var(--icon-checkbox-checked-mark) no-repeat;
                background-size: 70%;
                background-position: center;
            }
            .checkboxes_dropdown__title {
                display: inline-flex;
                flex-direction: column;
            }
            .checkboxes_dropdown__title-item {
                position: relative;
            }
            .checkboxes_dropdown__title-item:not(:last-child) {
                margin-bottom: 10px;
            }
            .checkboxes_dropdown__list {
                position: absolute;
                z-index: 10;
                top: 0;
                left: 0;
                right: 0;
                background: #fff;
                border: 1px solid #c5c5c5;
                border-radius: 3px;
                font-size: 15px;
                color: #363b44;
                display: none;
                height: auto;
                overflow: auto;
                -webkit-overflow-scrolling: touch;
                box-sizing: border-box;
                max-height: 355px;
            }
            .checkboxes_dropdown .checkboxes_dropdown__list {
                width: 260px;
                overflow-x: hidden;
            }
            .checkboxes_dropdown.expanded .checkboxes_dropdown__list {
                top: 0;
                bottom: auto;
            }
            .checkboxes_dropdown__label.control-checkbox {
                padding: 8px;
                width: 100%;
                box-sizing: border-box;
                margin-right: 0;
            }
            .control-checkbox__text {
                display: inline-flex;
                font-size: 15px;
                line-height: 20px;
                margin: 0 0 0 7px;
                transform: translateY(1px);
            }
            .checkboxes_dropdown__label:hover, .checkboxes_dropdown__label:active {
                background-color: #f5f5f5;
            }
        </style>
        <?php
    }
}
