<?php
$certificate_template_id = intval($_GET['download']);
$certificate = CertificateTemplate::getTemplate($certificate_template_id);
$content = $certificate->getContent();
$fields = $certificate->getFields();
$fields = array_map(function ($field) {
    return array_merge((array)$field, ['text' => $field->example_text]);
}, $fields);
$image_src = $certificate->getImgPath();
$template = CertificateTemplate::getTemplate($certificate_template_id);

$type = $_GET['type'] ?? 'pdf';
if ($type === 'jpg') {
    $generator = new CertificateGeneratorJPG($template);
} else {
    $generator = new CertificateGeneratorPDF($template);
}

$generator->render();
