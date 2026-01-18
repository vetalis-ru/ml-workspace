<?php
/**
 * @param int $term_id
 * @return void
 */
function mblc_save_certificate_fields(int $term_id)
{
    if (!isset($_POST['certificate'])) return;
    if (isset($_POST['certificate']['_mblc_has_certificate'])) {
        $wpmLevels = new MBLC_WpmLevels();
        $series = trim($_POST['certificate']['_mblc_certificate_series']);
        $course_name = trim($_POST['certificate']['_mblc_course_name']);
        $seriesExist = $wpmLevels->existCertificateSeries($series, $term_id);
        foreach ($_POST['certificate'] as $key => $value) {
            if ($seriesExist && $key === '_mblc_certificate_series') {
                MLC_AdminNotice::displayError('Такая серия уже существует');
                continue;
            }
            update_term_meta($term_id, $key, $value);
        }
        $updatingFields = [
            'certificate_template_id' => $_POST['certificate']['_mblc_template_id'],
            'course_name' => $course_name,
        ];
        if (!$seriesExist) {
            $updatingFields['series'] = $series;
        }
        try {
            foreach (Certificate::getCertificatesByWpmLevelId($term_id, 'ids') as $certificate_id) {
                Certificate::update($certificate_id, $updatingFields);
            }
        } catch (Exception $e) {
        }
    } else {
        delete_term_meta($term_id, '_mblc_has_certificate');
        foreach ($_POST['certificate'] as $key => $value) {
            delete_term_meta($term_id, $key);
        }
    }
}