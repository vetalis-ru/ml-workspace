<?php

add_shortcode('m_certificate_pdf', 'm_certificate_pdf');

function m_certificate_pdf($attributes): string
{
    $args = wp_parse_args($attributes, [
        'ext' => 'pdf',
        'user_id' => 0,
        'type' => 'download',
    ]);
    $certificateId = (int)$args['certificate'];

    return certUrl($certificateId, $args['ext'], $args['type']);
}
