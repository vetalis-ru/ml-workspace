<?php
add_shortcode('m_certificate_list', 'm_certificate_list');

function m_certificate_list()
{
    try {
        $certificates = array_map(
            function ($certificate) {
                return [
                    'text' => $certificate->certificate_name,
                    'view_pdf' => certUrl($certificate->id, 'pdf', 'view'),
                    'download_pdf' => certUrl($certificate->id, 'pdf', 'download'),
                    'download_jpg' => certUrl($certificate->id, 'jpg', 'download'),
                    'view_jpg' => certUrl($certificate->id, 'jpg', 'view'),
                ];
            },
            Certificate::getCustomerCertificates(get_current_user_id())
        );
    } catch (Exception $exception) {
    }
    ob_start();
    ?>
    <?php if (!empty($certificates)): ?>
    <div>
        <?php foreach ($certificates as $i => $certificate): ?>
            <div>
                <p class="sv-title"><?= $certificate['text']; ?></p>
                <div class="sv-group">
                    <a target="_blank"
                       style="--mbl-btn-color: <?= '#' . esc_attr(mblc_get_option_with_default('btn_text_color')) ?>;
                               --mbl-btn-bgcolor: <?= '#' . esc_attr(mblc_get_option_with_default('btn_bg')) ?>"
                       class="sv-btn sv-btn--primary"
                       href="<?= $certificate['download_pdf']; ?>">
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
<?php endif; ?>
    <?php
    return (new MBLCCompressedHTML(ob_get_clean()))->__toString();
}
