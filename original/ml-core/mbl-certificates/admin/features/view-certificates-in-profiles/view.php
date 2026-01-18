<?php
/**
 * @var array $certificates
 */
?>
<?php if (!empty($certificates)): ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th><label for="twitter"><?= mblc_get_option_with_default('all_certificates') ?></label></th>
            <td>
                <div class="mbl-settings-color mblc-settings-color">
                    <div>
                        <?php foreach ($certificates as $i => $certificate): ?>
                            <div>
                                <p class="sv-title"><?= $certificate['text']; ?></p>
                                <div class="sv-group">
                                    <a target="_blank" class="sv-btn sv-btn--primary"
                                       style="--mbl-btn-color: <?= '#' . esc_attr(mblc_get_option_with_default('btn_text_color')) ?>;
                                               --mbl-btn-bgcolor: <?= '#' . esc_attr(mblc_get_option_with_default('btn_bg')) ?>"
                                       href="<?= esc_attr($certificate['download_pdf']) ?>">
                                        <i class="fa fa-download" aria-hidden="true"></i> <?=
                                        mblc_get_option_with_default('download_text') ?>
                                    </a>
                                    <?php if ( mblc_jpg_enabled() ): ?>
                                        <a target="_blank" class="sv-btn sv-btn--primary"
                                           style="--mbl-btn-color: <?= '#' . esc_attr(mblc_get_option_with_default('btn_text_color')) ?>;
                                                   --mbl-btn-bgcolor: <?= '#' . esc_attr(mblc_get_option_with_default('btn_bg')) ?>"
                                           href="<?= esc_attr($certificate['download_jpg']) ?>">
                                            <i class="fa fa-download" aria-hidden="true"></i> <?=
                                            mblc_get_option_with_default('download_text_jpg') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($i + 1 !== count($certificates)): ?>
                                <hr class="sv-hr"/>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>
