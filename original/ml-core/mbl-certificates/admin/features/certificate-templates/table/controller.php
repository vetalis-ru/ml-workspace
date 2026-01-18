<?php
$certificates = CertificateTemplate::getCertificateTemplates();
$data['certificates'] = array_map(function ($certificate) {
    $certificates = Certificate::query([
        'filter' => ['certificate_template_id' => $certificate->id]
    ]);
    $wpmLevels = new MBLC_WpmLevels();
    $levels_ids = array_map(fn($p) => $p->term_id, $wpmLevels->query(['template_id' => $certificate->id]));

    return [
        'template_id' => $certificate->id,
        'name' => $certificate->name,
        'edit_url' => CertificateTemplate::getEditLink($certificate->id),
        'view_pdf' => CertificateTemplate::getDownloadLink($certificate->id),
        'levels_link' => admin_url("edit-tags.php?taxonomy=wpm-levels&post_type=wpm-page&include=" . implode(',', $levels_ids)),
        'levels_count' => count($levels_ids),
        'certificate_count' => $certificates['total'],
        'certificate_link' => admin_url("admin.php?page=mblc_certificate_edit&certificate_template_id=" . $certificate->id),
    ];
}, $certificates);
include 'view.php';
