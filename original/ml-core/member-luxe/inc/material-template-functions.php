<?php
if ( !function_exists( 'wpm_material_title' ) ) {
    function wpm_material_title( $post = 0 ): string
    {
        return get_post( $post )->post_title;
    }
}

if ( !function_exists( 'wpm_material_description' ) ) {
    function wpm_material_description( $post = 0 )
    {
        $text = get_post_meta( get_post( $post )->ID, 'mbl_short_description', true );

        return apply_filters( 'mbl_material_description_filter', $text );
    }
}

if ( !function_exists( 'wpm_material_description_attributes' ) ) {
    function wpm_material_description_attributes(): string
    {
        return apply_filters( 'mbl_short_description_maxlength', false ) ? 'data-maxlength' : '';
    }
}

if ( !function_exists( 'wpm_material_use_individual_indicator' ) ) {
    function wpm_material_use_individual_indicator( $post = 0 ): bool
    {
        $enabled = false;
        $material_meta = get_post_meta( get_post( $post )->ID, '_wpm_page_meta', true );

        if ( isset( $material_meta['indicators']['individual_indicators'] ) ) {
            $enabled = (int)$material_meta['indicators']['individual_indicators'] === 1;
        }

        return $enabled;
    }
}
if ( !function_exists( 'wpm_material_show_indicator' ) ) {
    function wpm_material_show_indicator( $alias, $post = 0 ): bool
    {
        $post = get_post( $post );
        $material_meta = get_post_meta( $post->ID, '_wpm_page_meta', true );

        if ( wpm_material_use_individual_indicator( $post ) ) {
            $show = (int)$material_meta['indicators'][$alias] === 1;
        } else {
            $show = (int)wpm_get_option( 'materials.' . $alias, 1 ) === 1;
        }

        return $show;
    }
}

if ( !function_exists( 'wpm_material_show_comments' ) ) {
    function wpm_material_show_comments( $post = 0 ): bool
    {
        return !wpm_option_is( 'main.comments_mode', 'cackle' )
            && wpm_material_show_indicator( 'comments_show', get_post( $post ) );
    }
}

if ( !function_exists( 'wpm_material_show_views' ) ) {
    function wpm_material_show_views( $post = 0 ): bool
    {
        return wpm_material_show_indicator( 'views_show', get_post( $post ) );
    }
}

if ( !function_exists( 'wpm_material_show_accessible' ) ) {
    function wpm_material_show_accessible( $post = 0 ): bool
    {
        return wpm_material_show_indicator( 'access_show', get_post( $post ) );
    }
}

if ( !function_exists( 'wpm_material_show_date' ) ) {
    function wpm_material_show_date( $post = 0 ): bool
    {
        return wpm_material_show_indicator( 'date_show', get_post( $post ) );
    }
}

if ( !function_exists( 'wpm_material_show_homework_status' ) ) {
    function wpm_material_show_homework_status( $post = 0 ): bool
    {
        return wpm_material_show_indicator( 'homework_status_show', get_post( $post ) );
    }
}

if ( !function_exists( 'wpm_material_show_content_type' ) ) {
    function wpm_material_show_content_type( $post = 0 ): bool
    {
        return wpm_material_show_indicator( 'content_type_show', get_post( $post ) );
    }
}

if ( !function_exists( 'wpm_material_show_number' ) ) {
    function wpm_material_show_number( $post = 0 ): bool
    {
        return wpm_material_show_indicator( 'number_show', get_post( $post ) );
    }
}

if ( !function_exists( 'wpm_material_has_comments' ) ) {
    function wpm_material_has_comments( $post = 0 ): bool
    {
        return get_comments_number( $post ) > 0;
    }
}

if ( !function_exists( 'wpm_material_comments_count' ) ) {
    function wpm_material_comments_count( $post = 0 ): int
    {
        return get_comments_number( $post );
    }
}

if ( !function_exists( 'wpm_material_date' ) ) {
    function wpm_material_date( $post = 0 ): string
    {
        return (string)mysql2date( get_option( 'date_format' ), get_post( $post )->post_date );
    }
}

if ( !function_exists( 'wpm_material_views_number' ) ) {
    function wpm_material_views_number( $post = 0 ): int
    {
        return intval( get_term_meta( get_post( $post )->ID, 'wpm_page_views', true ) );
    }
}

if ( !function_exists( 'wpm_material_has_text_content' ) ) {
    function wpm_material_has_text_content( $post = 0 ): bool
    {
        $material_meta = get_post_meta( get_post( $post )->ID, '_wpm_page_meta', true );

        return intval( $material_meta['content_types']['text'] ?? '0' ) === 1;
    }
}


if ( !function_exists( 'wpm_material_has_audio_content' ) ) {
    function wpm_material_has_audio_content( $post = 0 ): bool
    {
        $material_meta = get_post_meta( get_post( $post )->ID, '_wpm_page_meta', true );

        return intval( $material_meta['content_types']['audio'] ?? '0' ) === 1;
    }
}

if ( !function_exists( 'wpm_material_has_video_content' ) ) {
    function wpm_material_has_video_content( $post = 0 ): bool
    {
        $material_meta = get_post_meta( get_post( $post )->ID, '_wpm_page_meta', true );

        return intval( $material_meta['content_types']['video'] ?? '0' ) === 1;
    }
}

if ( !function_exists( 'wpm_material_accessible' ) ) {
    function wpm_material_accessible( $user_id = null, $post = 0 ): bool
    {
        if ( is_null( $user_id ) ) {
            $user_id = get_current_user_id();
        }

        $accessible_levels = $user_id ? wpm_get_all_user_accesible_levels( $user_id ) : [];

        return !wpm_is_blocked( $user_id ) && wpm_check_access( get_post( $post )->ID, $accessible_levels );
    }
}


if ( !function_exists( 'wpm_material_link_attributes' ) ) {
    function wpm_material_link_attributes( $post = 0 ): string
    {
        $attributes = '';
        $material_meta = get_post_meta( get_post( $post )->ID, '_wpm_page_meta', true );

        $redirect_on = ($material_meta['redirect_page_on'] ?? '0') === '1';
        $page_blank_on = ($material_meta['redirect_page_blank'] ?? '0') === '1';
        if ( $redirect_on && $page_blank_on ) {
            $attributes .= 'target="_blank"';
        }

        return $attributes;
    }
}

if ( !function_exists( 'wpm_material_has_background' ) ) {
    function wpm_material_has_background( $post = 0 ): bool
    {
        $material_meta = get_post_meta( get_post( $post )->ID, '_wpm_page_meta', true );

        return !empty($material_meta['bg_url']) || !empty(wpm_get_option('materials.bg_url'));
    }
}


if ( !function_exists( 'wpm_material_background' ) ) {
    function wpm_material_background( $post = 0 ): string
    {
        $material_meta = get_post_meta( get_post( $post )->ID, '_wpm_page_meta', true );

        return ($material_meta['bg_url'] ?? '') ?: wpm_get_option('materials.bg_url');
    }
}
