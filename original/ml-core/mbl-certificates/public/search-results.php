<?php
function printSearchResult($certificates)
{

    ob_start();
    ?>
    <table class="certificate-search-result">
        <thead>
        <tr class="certificate-search-result__heading"
            style="--mbl-btn-color: <?= '#' . esc_attr(mblc_get_option_with_default('btn_text_color')) ?>;
                    --mbl-btn-bgcolor: <?= '#' . esc_attr(mblc_get_option_with_default('btn_bg')) ?>">
            <th scope="col"><?= mblc_get_option_with_default('cert_fio') ?></th>
            <th scope="col"><?= mblc_get_option_with_default('cert_course') ?></th>
            <th scope="col"><?= mblc_get_option_with_default('cert_serial') ?></th>
            <th scope="col"><?= mblc_get_option_with_default('cert_number') ?></th>
            <th scope="col"><?= mblc_get_option_with_default('cert_date_issuance') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($certificates as $userId => $result): ?>
            <tr>
                <th class="result-fio table-light" rowspan="<?= count($result['certificates']) ?>" scope="row"><?= $result['fio'] ?></th>
            <?php foreach ($result['certificates'] as $key => $certificate): ?>
                <?php if ($key !== 0): ?>
                <tr>
                <?php endif; ?>
                    <td class="result-certificate-name"> <?= $certificate->getCertificateName(); ?> </td>
                    <td> <?= $certificate->series; ?> </td>
                    <td> <?= $certificate->number; ?> </td>
                    <td> <?= $certificate->getDateIssue(); ?> </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <?php
    return (new MBLCCompressedHTML(ob_get_clean()))->__toString();
}
