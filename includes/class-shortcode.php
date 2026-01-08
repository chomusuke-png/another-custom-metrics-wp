<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ACM_Shortcode
 * * Procesa el shortcode [acm_widget] para mostrar la métrica en el frontend.
 */
class ACM_Shortcode {

    /**
     * Constructor.
     */
    public function __construct() {
        add_shortcode( 'acm_widget', [ $this, 'render_shortcode' ] );
    }

    /**
     * Genera el HTML del widget.
     * * @param array $atts Atributos del shortcode.
     * @return string HTML renderizado.
     */
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'id' => 0,
        ], $atts, 'acm_widget' );

        $post_id = intval( $atts['id'] );

        // Validar que el post existe y es del tipo correcto
        if ( ! $post_id || get_post_type( $post_id ) !== 'acm_widget' ) {
            return '';
        }

        // Obtener data
        $value = get_post_meta( $post_id, '_acm_value', true );
        $label = get_post_meta( $post_id, '_acm_label', true );
        $color = get_post_meta( $post_id, '_acm_color', true );
        $style_attr = $color ? "style='border-color: {$color}; color: {$color};'" : '';

        // Buffer de salida para el HTML
        ob_start();
        ?>
        <div class="acm-widget-card">
            <div class="acm-value" <?php echo $style_attr; ?>>
                <?php echo esc_html( $value ); ?>
            </div>
            <div class="acm-label">
                <?php echo esc_html( $label ); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}