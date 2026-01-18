<?php
/**
 * @var WP_Term $tag
 * @var $has_certificate
 * @var $how_to_issue
 * @var $certificate_series
 * @var $course_name
 * @var $template_id
 */
?>
<tr class="form-field mblc-options-group-certificate<?= $has_certificate === 'yes' ? '' : ' mblc-no-certificate' ?>">
    <th scope="row">Сертификат</th>
    <td>
        <div class="">
            <div class="mbl-color-content mblc-settings-color">
                <p class="mblc-form-field">
                    <label for="has_certificate" class="mblc-form-label">
                        Выдается сертификат?
                    </label>
                    <input type="checkbox"
                           id="has_certificate"
                           name="certificate[_mblc_has_certificate]"
                        <?= checked( $has_certificate, 'yes' ) ?>
                           value="yes">
                </p>
                <div class="show-if-yes-certificate">
                    <div class="mblc-form-field">
                        <label class="mblc-form-label">
                            Способ выдачи
                        </label>
                        <div>
                            <label style="display: block">
                                <input type="radio" name="certificate[_mblc_how_to_issue]"
                                    <?= checked( $how_to_issue, 'employee' ) ?>
                                       value="employee"> Вручную
                            </label>
                            <label style="display: block">
                                <input type="radio" name="certificate[_mblc_how_to_issue]"
                                    <?= checked( $how_to_issue, 'auto' ) ?>
                                       value="auto">Автоматически
                            </label>
                        </div>
                    </div>
                    <div class="mblc-series-group">
                        <div class="mblc-form-field">
                            <label for="certificate_series" class="mblc-form-label">Серия сертификата</label>
                            <div class="mblc-form-inp-wrap">
                                <input type="text" style="width: 50%;"
                                       class="mblc-form-inp"
                                       name="certificate[_mblc_certificate_series]"
                                       id="certificate_series" value="<?= esc_attr( $certificate_series ) ?>"
                                       placeholder="CA">
                                <p class="mblc-series-error-msg">Такая серия уже существует</p>
                            </div>
                            <div class="mblc-series-list">
                                <label for="series-list">
                                    Используются:
                                </label>
                                <select id="series-list">
                                    <?php foreach ( MBLC_WpmLevels::getAllSeries() as $key => $series ): ?>
                                        <option><?= $key + 1 . ". " . $series ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mblc-form-field">
                        <label class="mblc-form-label" for="course_name">
                            Название курса
                        </label>
                        <div class="mblc-form-inp-wrap">
                            <input class="mblc-form-inp" type="text" name="certificate[_mblc_course_name]"
                                   value="<?= esc_attr( $course_name ) ?>"
                                   id="course_name" placeholder="Название курса">
                        </div>
                    </div>
                    <div class="mblc-form-field">
                        <label class="mblc-form-label" for="template_id">Шаблон сертификата</label>
                        <div class="mblc-form-inp-wrap">
                            <select class="mblc-form-inp" name="certificate[_mblc_template_id]"
                                    id="template_id">
                                <option value="">Выберите шаблон</option>
                                <?php foreach ( CertificateTemplate::getCertificateTemplates() as $template ): ?>
                                    <option <?= selected( $template_id, $template->id ) ?>
                                            value="<?= esc_attr( $template->id ) ?>"><?= $template->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php if(mblc_get_option_with_default('custom_fields_enabled') === 'on'): ?>
                        <?php for ( $i = 1; $i <= mblc_certificate_custom_fields_count(); $i++ ): ?>
                            <div class="mblc-form-field">
                                <label class="mblc-form-label" for="_mblc_field<?= $i ?>">
                                    Дополнительное поле <?= $i ?> (не обязательно)
                                </label>
                                <div class="mblc-form-inp-wrap">
                                    <textarea class="mblc-form-inp short" name="certificate[_mblc_field<?= $i ?>]"
                                              id="_mblc_field<?= $i ?>"
                                              placeholder="Доп поле <?= $i ?>" rows="2"
                                              cols="20"><?= esc_html( get_term_meta( $tag->term_id, "_mblc_field$i", true ) ) ?></textarea>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </td>
</tr>
