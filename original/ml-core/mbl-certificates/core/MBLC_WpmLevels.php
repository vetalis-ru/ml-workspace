<?php

class MBLC_WpmLevels
{
    private string $tax;

    function __construct()
    {
        $this->tax = 'wpm-levels';
    }

    public function query( $params = [] )
    {
        $args = [
            'taxonomy' => $this->tax,
            'hide_empty' => false,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => '_mblc_has_certificate',
                    'value' => 'yes'
                ]
            ]
        ];
        if ( isset( $params['how_to_issue'] ) ) {
            $args['meta_query'][] = [
                'key' => '_mblc_how_to_issue',
                'value' => $params['how_to_issue']
            ];
        }
        if ( isset( $params['template_id'] ) ) {
            $args['meta_query'][] = [
                'key' => '_mblc_template_id',
                'value' => $params['template_id']
            ];
        }
        if ( isset( $params['series'] ) ) {
            $args['meta_query'][] = [
                'key' => '_mblc_certificate_series',
                'value' => $params['series']
            ];
        }
        if ( isset( $params['include'] ) ) {
            $args['include'] = $params['include'];
        }
        if ( isset( $params['exclude'] ) ) {
            $args['exclude'] = $params['exclude'];
        }
        if ( isset( $params['fields'] ) ) {
            $args['fields'] = $params['fields'];
        }
        return get_terms( $args );
    }

    public function existCertificateSeries( string $series, int $exclude = 0 ): bool
    {
        $params = [
            'fields' => 'count',
            'exclude' => [ $exclude ],
            'series' => $series
        ];

        return intval( $this->query( $params ) ) !== 0;
    }

    public static function getAllSeries(): array
    {
        $levels = (new self())->query();
        $series = [];
        foreach ( $levels as $term ) {
            $series[] = get_term_meta( $term->term_id, '_mblc_certificate_series', true );
        }
        sort( $series );
        return $series;
    }
}
