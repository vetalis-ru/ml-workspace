<?php
$certificate_template_id = intval($_GET['certificate_id']);
$certificate = CertificateTemplate::getTemplate($certificate_template_id);
$data['title'] = $certificate->name;
$data['orientation'] = $certificate->orientation;
$content = $certificate->getContent();
$data['image'] = $certificate->getImgSrc();
$fields = [];
foreach ($certificate->getFields() as $field) {
    $globalField = Field::getFieldByCode($field->getCode());

	if (!$globalField) {
		continue;
    }

	if($globalField->getName() !== $field->getName()) {
        $field->setName($globalField->getName());
    }
    $fields[] = $field;
}
$data['attachment_id'] = $content->attachment_id;
$data['download'] = CertificateTemplate::getDownloadLink($certificate_template_id);
include 'view.php';
