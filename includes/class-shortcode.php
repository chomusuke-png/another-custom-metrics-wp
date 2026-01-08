<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ACM_Shortcode
 * * Procesa el shortcode y aplica el formateo de datos.
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
        $atts = shortcode_atts( [ 'id' => 0 ], $atts, 'acm_widget' );
        $post_id = intval( $atts['id'] );

        if ( ! $post_id || get_post_type( $post_id ) !== 'acm_widget' ) {
            return '';
        }

        // Obtener data cruda
        $raw_value = get_post_meta( $post_id, '_acm_value', true );
        $label     = get_post_meta( $post_id, '_acm_label', true );
        $color     = get_post_meta( $post_id, '_acm_color', true );
        $format    = get_post_meta( $post_id, '_acm_format', true );

        // Procesar valor inicial (Server Side Render para SEO/No-JS)
        $formatted_value = $this->format_metric( $raw_value, $format );

        $style_attr = $color ? "style='border-color: {$color}; color: {$color};'" : '';

        // Preparamos atributos data para JS
        // Si es fecha, no pasamos valor numérico para evitar animación errónea
        $data_attr = '';
        if ( $format !== 'date' && is_numeric( $raw_value ) ) {
            $data_attr = 'data-acm-value="' . esc_attr( $raw_value ) . '" data-acm-format="' . esc_attr( $format ) . '"';
        }

        ob_start();
        ?>
        <div class="acm-widget-card">
            <div class="acm-value" <?php echo $style_attr; ?> <?php echo $data_attr; ?>>
                <?php echo esc_html( $formatted_value ); ?>
            </div>
            <div class="acm-label">
                <?php echo esc_html( $label ); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Aplica reglas de formato al valor (PHP Fallback).
     * * @param mixed $value Valor crudo.
     * @param string $format Tipo de formato.
     * @return string Valor formateado.
     */
    private function format_metric( $value, $format ) {
        if ( empty( $value ) ) return $value;

        switch ( $format ) {
            case 'money':
                return '$ ' . number_format_i18n( (float) $value, 2 );

            case 'number':
                return number_format_i18n( (float) $value );

            case 'date':
                $timestamp = strtotime( $value );
                if ( $timestamp ) {
                    return date_i18n( get_option( 'date_format' ), $timestamp );
                }
                return $value;

            case 'compact':
                return $this->format_compact_number( (float) $value );

            case 'raw':
            default:
                return $value;
        }
    }

    /**
     * Convierte números grandes a formatos legibles.
     * * @param float $n Número a formatear.
     * @return string Número compacto.
     */
    private function format_compact_number( $n ) {
        if ( $n < 1000 ) {
            return number_format_i18n( $n );
        }

        $suffix = [ '', 'k', 'M', 'B', 'T' ];
        $power  = floor( log( $n, 1000 ) );

        if ( $power >= count( $suffix ) ) {
            $power = count( $suffix ) - 1;
        }

        return round( $n / pow( 1000, $power ), 1 ) . $suffix[ $power ];
    }
}