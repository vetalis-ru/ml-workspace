<?php
function m_certificate_verification($atts)
{
    ob_start();
    ?>
    <div id="certificateSearchResult"></div>
    <div class="mblc-alert mblc-alert--success" role="alert" data-status="success" style="display:none;"></div>
    <div class="mblc-alert mblc-alert--warning" role="alert" data-status="warning" style="display:none;"></div>
    <div x-data="{active: 'nav-fio'}">
        <nav class="sv-nav-tabs">
            <div class="sv-tabs" id="nav-tab-sv" role="tablist">
                <button class="sv-tabs__item" id="nav-fio-tab" role="tab" aria-controls="nav-fio"
                        @click="active = 'nav-fio'"
                        :class="active === 'nav-fio' && 'sv-tabs__item--active'" type="button"
                        :aria-selected="active === 'nav-fio'" aria-selected="true">
                    <?= mblc_get_option_with_default('by_fio') ?>
                </button>
                <button class="sv-tabs__item" id="nav-series-number-tab" role="tab" aria-controls="nav-series-number"
                        @click="active = 'nav-series-number'"
                        :class="active === 'nav-series-number' && 'sv-tabs__item--active'" type="button"
                        :aria-selected="active === 'nav-series-number'" aria-selected="false">
                    <?= mblc_get_option_with_default('by_serial_number') ?>
                </button>
            </div>
        </nav>
        <div class="sv-tab-content" id="nav-tabContent">
            <div x-show="active === 'nav-fio'"
                 class="sv-tab-pane" id="nav-fio" role="tabpanel" aria-labelledby="nav-fio-tab">
                <form class="certificateForm">
                    <div class="sv-form-row">
                        <div class="sv-col-12">
                            <div class="sv-form-group">
                                <label for="graduate_last_name" class="sv-label">
                                    <?= mblc_get_option_with_default('field_surname') ?>
                                </label>
                                <input required type="text" class="sv-form-control" id="graduate_last_name"
                                       name="graduate_last_name">

                            </div>
                            <div class="sv-form-group">
                                <label for="graduate_first_name" class="sv-label">
                                    <?= mblc_get_option_with_default('field_name') ?>
                                </label>
                                <input required type="text" class="sv-form-control" id="graduate_first_name"
                                       name="graduate_first_name">
                            </div>
                        </div>
                        <div class="sv-col-12">
                            <input type="hidden" name="action" value="ml_verify_certificate_by_fio">
                            <button class="sv-btn sv-btn--primary"
                                    style="--mbl-btn-color: <?= '#' . esc_attr(mblc_get_option_with_default('btn_text_color')) ?>;
                                            --mbl-btn-bgcolor: <?= '#' . esc_attr(mblc_get_option_with_default('btn_bg')) ?>"
                                    type="submit" name="submit">
                            <span style="display:none;" class="spinner-border spinner-border-sm" role="status"
                                  aria-hidden="true"></span>
                                <?= mblc_get_option_with_default('check_text') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div x-cloak x-show="active === 'nav-series-number'"
                 class="sv-tab-pane" id="nav-series-number" role="tabpanel" aria-labelledby="nav-series-number-tab">
                <form class="certificateForm">
                    <div class="sv-form-row">
                        <div class="sv-col-12">
                            <div class="sv-form-group">
                                <label for="series" class="sv-label">
                                    <?= mblc_get_option_with_default('field_serial') ?>
                                </label>
                                <input required type="text" class="sv-form-control" name="series" id="series">
                            </div>
                            <div class="sv-form-group">
                                <label for="number" class="sv-label">
                                    <?= mblc_get_option_with_default('field_number') ?>
                                </label>
                                <input required type="text" class="sv-form-control" name="number" id="number">
                            </div>
                        </div>
                        <div class="sv-col-12">
                            <input type="hidden" name="action" value="ml_verify_certificate_by_series">
                            <button class="sv-btn sv-btn--primary"
                                    style="--mbl-btn-color: <?= '#' . esc_attr(mblc_get_option_with_default('btn_text_color')) ?>;
                                            --mbl-btn-bgcolor: <?= '#' . esc_attr(mblc_get_option_with_default('btn_bg')) ?>"
                                    type="submit" name="submit">
                            <span class="spinner-border spinner-border-sm" style="display:none;" role="status"
                                  aria-hidden="true"></span>
                                <?= mblc_get_option_with_default('check_text') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    return (new MBLCCompressedHTML(ob_get_clean()))->__toString();
}
