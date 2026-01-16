<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ACM_Utils {

    /**
     * Formatea el valor numérico según la configuración.
     */
    public static function format_metric( $value, $format, $decimals = 0 ) {
        if ( empty( $value ) && $value !== '0' ) return $value;

        switch ( $format ) {
            case 'number': return number_format_i18n( (float) $value, $decimals );
            case 'money': return '$ ' . number_format_i18n( (float) $value, $decimals );
            case 'percent': return number_format_i18n( (float) $value, $decimals ) . '%';
            case 'compact': return self::format_compact_number( (float) $value, $decimals );
            case 'money_compact': return '$ ' . self::format_compact_number( (float) $value, $decimals );
            case 'weight': return self::format_weight( (float) $value, $decimals );
            case 'date': 
                $timestamp = strtotime( $value );
                return $timestamp ? date_i18n( get_option( 'date_format' ), $timestamp ) : $value;
            case 'raw':
            default: return $value;
        }
    }

    /**
     * Genera el HTML del icono/imagen.
     * Si se pasa $color, aplica técnica de CSS Masking para colorearlo.
     */
    public static function render_icon_html( $image_id, $width, $color = '' ) {
        if ( ! $image_id ) return '';
        
        $url = wp_get_attachment_image_url( $image_id, 'medium' );
        if ( ! $url ) return '';

        $w = intval( $width );
        if ( $w <= 0 ) $w = 80;

        // Opción A: Imagen Normal (Sin colorear)
        if ( empty( $color ) ) {
            return '<img class="acm-icon" src="' . esc_url( $url ) . '" alt="" style="width: ' . $w . 'px; max-width: none;" />';
        }

        // Opción B: Imagen Coloreada (CSS Mask)
        // Usamos un wrapper con el ancho definido.
        // La imagen <img> original tiene opacity:0 pero mantiene el aspect-ratio y altura correctos en el flujo.
        // El div .acm-icon-mask se superpone y toma el color de fondo recortado por la forma de la imagen.
        $html  = '<div class="acm-icon-wrapper" style="position: relative; width: ' . $w . 'px; display: inline-block; vertical-align: middle; line-height: 0;">';
        $html .= '<img src="' . esc_url( $url ) . '" alt="" style="width: 100%; height: auto; display: block; opacity: 0;" />';
        $html .= '<div class="acm-icon-mask" style="
                    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                    background-color: ' . esc_attr( $color ) . ';
                    -webkit-mask-image: url(' . esc_url( $url ) . ');
                    mask-image: url(' . esc_url( $url ) . ');
                    -webkit-mask-size: contain;
                    mask-size: contain;
                    -webkit-mask-repeat: no-repeat;
                    mask-repeat: no-repeat;
                    -webkit-mask-position: center;
                    pointer-events: none;
                  "></div>';
        $html .= '</div>';

        return $html;
    }

    private static function format_compact_number( $n, $decimals ) {
        if ( $n < 1000 ) return number_format_i18n( $n, $decimals );
        $suffix = [ '', 'k', 'M', 'B', 'T' ];
        $power  = floor( log( $n, 1000 ) );
        if ( $power >= count( $suffix ) ) $power = count( $suffix ) - 1;
        return number_format_i18n( round( $n / pow( 1000, $power ), $decimals ), $decimals ) . $suffix[ $power ];
    }

    private static function format_weight( $g, $decimals ) {
        $suffixes = [ 'g', 'kg', 't' ];
        if ( $g <= 0 ) return '0 g';
        $power = floor( log( $g, 1000 ) );
        if ( $power >= count( $suffixes ) ) $power = count( $suffixes ) - 1;
        if ( $power < 0 ) $power = 0;
        $number = $g / pow( 1000, $power );
        return number_format_i18n( round( $number, $decimals ), $decimals ) . ' ' . $suffixes[ $power ];
    }
}